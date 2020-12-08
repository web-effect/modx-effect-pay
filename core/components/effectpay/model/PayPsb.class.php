<?php

class PayPsb {
    
    /**
     * 
     */
    private static function cfg()
    {
        global $modx;

        $isTest = (int)$modx->getOption('effectpay.psb.is_test', null, 1);
        $keys = $modx->getOption('effectpay.psb.keys', null, '');
        $keys = explode('||', $keys);

        return [
            'sitename' => $modx->getOption('site_name', null, 'SiteName'),
            'isTest' => $isTest,
            'terminal' => $modx->getOption('effectpay.psb.terminal', null, ''),
            'merchant' => $modx->getOption('effectpay.psb.merchant', null, ''),
            'key1' => $keys[0],
            'key2' => $keys[1],
            'merchant_email' => $modx->getOption('effectpay.merchant_email', null, 'merchant_email@mail.test'),
        ];
    }


    /**
     * Получаем массив для вставки в форму
     * $key случайно генерируется при заказе
     */
    public static function buildFormData(int $id, $key = '')
    {
        $cfg = self::cfg();
        $order = Pay::getOrder($id);
        $options = $order['options'] ?: [];
        
        $keyCheck = ($key && $order['options']['pay_key'] == $key) ? true : false; 
        if (empty($cfg) || empty($order) || !$keyCheck) {
            return [0, 'PSB: не переданы данные или неверный ключ'];
        }

        //$login = $cfg['id'];
        //pass = $cfg['pass1'];

        $id = $order['id'];
        $summ = round($order['total_price'], 2);
        
        //Первая компонента ключа
        $comp1 = $cfg['key1'];
        //Вторая компонента ключа
        $comp2 = $cfg['key2'];
        //Данные для отправки на ПШ
        $data = [
            'amount' => number_format($summ, 2, '.',''),
            'currency' => 'RUB',
            'order' => (string)$id + 100000, // мин. длина - 6
            'desc' => 'Платёж № ' . $id,
            'terminal' => $cfg['terminal'],
            'trtype' => '1',
            'merch_name' => $cfg['sitename'],
            'merchant' => $cfg['merchant'],
            'email' => 'cardholder@mail.test',
            'timestamp' => gmdate("YmdHis"),
            'nonce' => bin2hex(random_bytes(16)),
            'backref' => 'https://' . $_SERVER['SERVER_NAME'],
            'notify_url' => 'https://' . $_SERVER['SERVER_NAME'] . '/assets/components/effectpay/payment.php?mode=psb_callback',
            'cardholder_notify' => 'EMAIL',
            'merchant_notify' => 'EMAIL',
            'merchant_notify_email' => $cfg['merchant_email'],
        ];
        if (!empty($order['contacts']['email'])) {
            $data['email'] = $order['contacts']['email'];
        }

        //Расчет P_SIGN
        $vars =
            ["amount","currency","order","merch_name","merchant","terminal","email","trtype","timestamp","nonce","backref"
        ];
        $string = '';
        foreach ($vars as $param){
            if (isset($data[$param]) && strlen($data[$param]) != 0){
                $string .= strlen($data[$param]) . $data[$param];
            } else {
                $string .= "-";
            }
        }
        $key = strtoupper(implode(unpack("H32",pack("H32",$comp1) ^ pack("H32",$comp2))));
        $data['p_sign'] = strtoupper(hash_hmac('sha1', $string, pack('H*', $key)));
        //Вывод формы для передачи запроса на ПШ
        $url = $cfg['isTest'] ? 'https://test.3ds.payment.ru/cgi-bin/cgi_link' : 'https://test.3ds.payment.ru/cgi-bin/cgi_link';
        $out = "<form id='payment_form' action='$url' method = 'POST'>";
        foreach ($data as $param => $value) {
            $out .= "<input type='hidden' name='" . strtoupper($param) . "' value='" . $value . "'/>";
        }
        $out .= "<input type='submit' name='SUBMIT' value='Перейти к оплате' />";
        $out .= "</form>";
        $out .= "Если не произошло автоматического перенаправления, нажмите на кнопку 'Перейти к оплате'";
        $out .= "<script type='text/javascript'>document.getElementById('payment_form').submit();</script>";

        return [1, $out];
    }


    /**
     * 
     */
    public function callback()
    {     
        if (isset($_POST['P_SIGN'])) {
            $cfg = self::cfg();
            $comp1 = $cfg['key1'];
            $comp2 = $cfg['key2'];

            $params = array_change_key_case($_POST, CASE_LOWER);
            $vars =
            ["amount","currency","order","merch_name","merchant","terminal","email","trtype","timestamp","nonce","backref"
            ,"result","rc","rctext","authcode","rrn","int_ref"];
            $string = '';
            foreach ($vars as $param){
                if (isset($params[$param]) && strlen($params[$param]) != 0){
                    $string .= strlen($params[$param]) . $params[$param];
                } else {
                    $string .= "-";
                }
            }
            $key = strtoupper(implode(unpack("H32",pack("H32",$comp1) ^ pack("H32",$comp2))));
            $sign = strtoupper(hash_hmac('sha1', $string, pack('H*', $key)));
            if (strcasecmp($params['p_sign'],$sign) == 0) {
                //Если подпись совпала, то выполняем необходимые действия.
                if ((int)$params['result'] == 0 && strcasecmp($params['rc'],'00') == 0) {
                    $orderID = $_POST['ORDER'] - 100000; // это из-за мин. длины;
                    Pay::changeStatus($orderID, 1);
                }
            }
        }
    }


    /**
     * Отправка корзины для чеков
     * Не работает
     */
    public static function sendCart()
    {
        $cfg = self::cfg();
        $comp1 = $cfg['key1'];
        $comp2 = $cfg['key2'];

        $receipt = [
            "PhoneOrEmail" => 'test@yourdomain.test',
            "ClientId" => 'AAAAA15',
            "TaxMode" => 4,
            "MaxDocumentsInTurn" => 10000,
            "Group" => 'c3ad4e66-cc73-4c27-91f0-745aa0303456',
            "DocumentType" => 0,
            "Password" => 1,
            "RequestId" => bin2hex(random_bytes(16)),
            "NonCash" => [23000],
            "Device" => 'auto',
            "FullResponse" => false,
            "Place" => $_SERVER['SERVER_NAME']
        ];
        $receipt['Lines'][] = [
            "Description" => 'Булочка с маком',
            "Price" => 3000,
            "Qty" => 3000,
            "TaxId" => 1,
            "PayAttribute" => 4
        ];
        $receipt['Lines'][] = [
            "Description" => 'Кефир',
            "Price" => 7000,
            "Qty" => 2000,
            "TaxId" => 1,
            "PayAttribute" => 4
        ];
        $data['terminal'] = '79036777';
        $data['timestamp'] = gmdate("YmdHis");
        $data['nonce'] = bin2hex(random_bytes(16));
        $data['receipt_info'] = json_encode($receipt);
        $data['ofd'] = 'STARRYS';
        $vars = ["terminal","timestamp","nonce"];
        $string = '';
        foreach ($vars as $param) {
            if(isset($data[$param]) && strlen($data[$param]) != 0){
                $string .= strlen($data[$param]) . $data[$param];
            } else {
                $string .= "-";
            }
        }
        $key = strtoupper(implode(unpack("H32",pack("H32",$comp1) ^ pack("H32",$comp2))));
        $data['p_sign'] = strtoupper(hash_hmac('sha1', $string, pack('H*', $key)));
        $url = "https://test.3ds.payment.ru/cgi-bin/54fz/send_receipt";
        $host = "test.3ds.payment.ru";
        $headers = [
            "Host: " . $host,
            "User-Agent: " . $_SERVER['HTTP_USER_AGENT'],
            "Accept: */*",
            "Content-Type: application/x-www-form-urlencoded; charset=utf-8"
        ];
        $params = array_change_key_case($data,CASE_UPPER);
        $query = http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$query);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,15);
        curl_setopt($ch,CURLOPT_TIMEOUT,60);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        $response = curl_exec($ch);
        if(!$response){
            return curl_error($ch);
        }
        curl_close($ch);
        echo $response;
    }
}
