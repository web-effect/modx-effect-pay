<?php

class PayAlpha {
    
    /**
     * 
     */
    private static function cfg()
    {
        global $modx;

        $returnPage = (int)$modx->getOption('effectpay.return_page', null, 1);

        return [
            'isTest' => $isTest,
            'id' => $modx->getOption('effectpay.alpha.id', null, ''),
            'pass' => $modx->getOption('effectpay.alpha.password', null, ''),
            'returnUrl' => $modx->makeUrl($returnPage, '', '', 'full')
        ];
    }


    /**
     * Возвращает цену в копейках с учетом скидки
     */
    private static function calcPrice($price, $discount = 0) 
    {
        $rub = $price - ($price * $discount / 100);
        return round($rub * 100);
    }


    /**
     * Возвращает ссылку на оплату и id заказа
     */
    public static function pay(int $id)
    {
        global $modx;

        $cfg = self::cfg();
        $order = Pay::getOrder($id);

        $discount = $order['discount'] ?? 0;
        $orderBundle = [];$orderBundle = [];

        foreach ($order['items'] as $p) {
            $orderBundle['cartItems']['items'][] = [
                "positionId" => $p['id'],
                "itemCode" => 'p_' . $p['id'],
                "name" => $p['name'],
                "itemPrice" => self::calcPrice($p['price'], $discount),
                "quantity" => ["value" => (int)$p['qty'], "measure" => "шт."],
                "itemAmount" => self::calcPrice($p['total_price'], $discount),
                "tax" => $tax,
            ];
        }

        $data = array(
            'userName' => $cfg['id'],
            'password' => $cfg['pass'],
            'orderNumber' => $order['id'],
            'amount' => self::calcPrice($order['total_price'], 0),
            'returnUrl' => $cfg['returnUrl'].'?method=alpha',
            'orderBundle' => json_encode($orderBundle),
        );
    
        $response = self::gateway('register.do', $data);
         
  		
        if ($response){
		    if (!isset($response['errorCode'])) {
                return [1, [
                    'pay_key' => $response['orderId'],
                    'pay_link' => $response['formUrl'],
                ]];
		    } else {
                return [0, 'Ошибка #' . $response['errorCode'] . ': ' . $response['errorMessage']];
		    }
		} else {
            return [0, 'Ошибка онлайн-оплаты (не пришёл ответ)'];
		}
		
        return [1, $out];
    }


    /**
     * 
     */
    private static function gateway($method, $data)
    {
        $curl = curl_init(); // Инициализируем запрос
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://web.rbsuat.com/ab/rest/' . $method, // Полный адрес метода
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
            CURLOPT_POST => true, // Метод POST
            CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
        ));
        $response = curl_exec($curl); // Выполняем запрос
         
        $response = json_decode($response, true); // Декодируем из JSON в массив
        curl_close($curl); // Закрываем соединение
        return $response; // Возвращаем ответ
    }


    /**
     * 
     */
    public function getOrderStatus($key)
    {
        $cfg = self::cfg();

        $data = array(
            'userName' => $cfg['id'],
            'password' => $cfg['pass'],
            'orderId' => $key,
        );
        $response = self::gateway('getOrderStatus.do', $data);
        
        if (!empty($response['OrderNumber'])) {
            Pay::changeStatus($response['OrderNumber'], $response['OrderStatus'] == 2 ? 1 : 0);
            return "Статус оплаты: {$response['ErrorMessage']}";
        } else {
            return "Ошибка #{$response['ErrorCode']}: {$response['ErrorMessage']}";
        }
    }

}
