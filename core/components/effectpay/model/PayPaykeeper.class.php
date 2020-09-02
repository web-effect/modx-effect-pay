<?php

class PayPaykeeper {
    
    /**
     * 
     */
    private static function cfg()
    {
        global $modx;
        return [
            'id' => $modx->getOption('effectpay.paykeeper.id', null, ''),
            'pass' => $modx->getOption('effectpay.paykeeper.password', null, ''),
            'server' => $modx->getOption('effectpay.paykeeper.server', null, ''),
            'secret' => $modx->getOption('effectpay.paykeeper.secret', null, ''),
        ];
    }


    /**
     * Возвращает цену в копейках с учетом скидки
     */
    private static function calcPrice($price, $discount = 0) 
    {
        $rub = $price - ($price * $discount / 100);
        return number_format($rub, 2, '.', '');
    }


    /**
     * Возвращает ссылку на оплату и id заказа
     */
    public static function pay(int $id)
    {
        global $modx;

        $cfg = self::cfg();
        $order = Pay::getOrder($id);

        # Логин и пароль от личного кабинета PayKeeper
        $user = $cfg['id'];
        $password = $cfg['pass']; 
        $server_paykeeper = $cfg['server']; 
        $discount = $order['discount'] ?? 0;

        # Basic-авторизация передаётся как base64
        $base64=base64_encode("$user:$password");         
        $headers=Array(); 
        array_push($headers,'Content-Type: application/x-www-form-urlencoded');
        # Подготавливаем заголовок для авторизации
        array_push($headers,'Authorization: Basic '.$base64);                                                  
        
        # Параметры платежа, сумма - обязательный параметр
        # Остальные параметры можно не задавать

        $cart = [];
        foreach ($order['items'] as $p) {
            $cart[] = [
                "name" => $p['name'],
                "price" => self::calcPrice($p['price'], $discount),
                "quantity" => (int)$p['qty'],
                "sum" => self::calcPrice($p['total_price'], $discount),
                "tax" => 'none',
            ];
        }

        $payment_data = array (
            "pay_amount" => self::calcPrice($order['total_price'], 0),
            "orderid" => $id,
            "service_name" => ";PKC|".json_encode($cart)."|",
        );
        
        # Готовим первый запрос на получение токена безопасности
        $uri="/info/settings/token/";
        
        # Для сетевых запросов в этом примере используется cURL
        $curl=curl_init(); 
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_URL,$server_paykeeper.$uri);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'GET');
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl,CURLOPT_HEADER,false);
        # Инициируем запрос к API
        $response=curl_exec($curl);                       
        $php_array=json_decode($response,true);
        
        # В ответе должно быть заполнено поле token, иначе - ошибка
        if (isset($php_array['token'])) $token = $php_array['token']; else {
            return [0, $response['msg'] ?: 'Ошибка paykeeper'];
        }
        
        # Готовим запрос 3.4 JSON API на получение счёта
        $uri="/change/invoice/preview/";
        
        # Формируем список POST параметров
        $request = http_build_query(array_merge($payment_data, array ('token'=>$token)));
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_URL,$server_paykeeper.$uri);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl,CURLOPT_HEADER,false);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$request);
        $response = json_decode(curl_exec($curl), true);

        //print_r($response);

        if (isset($response['invoice_id'])) {
            $invoice_id = $response['invoice_id'];
        }  else {
            return [0, $response['msg'] ?: 'Ошибка paykeeper'];
        }

        return [1, [
            'pay_key' => $invoice_id,
            'pay_link' => "http://$server_paykeeper/bill/$invoice_id/",
        ]];
    }


    /**
     * 
     * 
     */
    public function callback(array $input)
    {
        $cfg = self::cfg();
        $secret_seed = $cfg['secret'];

        $id = $input['id'] ?? '';
        $sum = $input['sum'] ?? '';
        $clientid = $input['clientid'] ?? '';
        $orderid = $input['orderid'] ?? '';
        $key = $input['key'] ?? '';
        
        if ($key == md5($id.$sum.$clientid.$orderid.$secret_seed)) {
            $success = Pay::changeStatus($orderid, 1);
            if ($success) return "OK " . md5($id.$secret_seed);
            return 'Status not changed';
        } else {
            return "Error! Hash mismatch " . md5(($id.$sum.$clientid.$orderid.$secret_seed));
        }
    }


}
