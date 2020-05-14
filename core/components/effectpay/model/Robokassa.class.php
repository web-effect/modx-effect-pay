<?php

class Robokassa {
    
    /**
     * 
     */
    private static function cfg()
    {
        global $modx;

        $isTest = (int)$modx->getOption('effectpay.robokassa.is_test', null, 1);
        $pass = $modx->getOption('effectpay.robokassa.passwords', null, '');
        $pass = explode('||', $pass);

        return [
            'isTest' => $isTest,
            'id' => $modx->getOption('effectpay.robokassa.id', null, ''),
            'pass1' => $isTest ? ($pass[0] ?? '') : ($pass[2] ?? ''),
            'pass2' => $isTest ? ($pass[1] ?? '') : ($pass[3] ?? ''),
        ];
    }


    /**
     * Получаем массив для вставки в форму
     * $key случайно генерируется при заказе
     */
    public static function buildFormData(int $id, $key = '')
    {
        $cfg = self::cfg();
        $order = Effectpay::getOrder($id);
        $options = $order['options'] ?: [];

        $keyCheck = ($key && $order['options']['pay_key'] == $key) ? true : false; 
        if (empty($cfg) || empty($order) || !$keyCheck) {
            return [0, 'Робокасса: не переданы данные или неверный ключ'];
        }

        $login = $cfg['id'];
        $pass = $cfg['pass1'];

        $id = $order['id'];
        $summ = round($order['total_price'], 2);
        
        $orderBundle = [
            'sno' => 'osn',
        ];
        foreach($order['items'] as $p) {
            $orderBundle['items'][] = [    
                "name" => $p['name'],
                "quantity" => $p['qty'],
                "sum" => round($p['total_price'], 2),
                "tax" => 'vat120'
            ];
        }
        $receipt = urlencode(json_encode($orderBundle));

        $crc  = md5("$login:$summ:$id:$receipt:$pass");
        
        $out = [
            'MerchantLogin' => $login,
            'OutSum' => $summ,
            'InvId' => $id,
            'Receipt' => $receipt,
            'SignatureValue' => $crc,
            'Email' => $order['contacts']['email'] ?: '',
            'isTest' => $cfg['isTest'],
        ];

        return [1, $out];
    }


    /**
     * 
     */
    public function callback(array $input)
    {
        $cfg = self::cfg();
        $login = $cfg['id'];
        $pass = $cfg['pass2'];

        $id = $input['InvId'];
        $sum = $input['OutSum'];
        
        $order = Effectpay::getOrder($id);

        $crc2  = strtoupper(md5("$sum:$id:$pass"));
        
        if ($crc2 == strtoupper($input['crc'])) {
            $success = Effectpay::changeStatus($id, 1);
            if ($success) return 'OK' . $id;
        }

        return 'error';
    }


}
