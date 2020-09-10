<?php

class PaySberbank {
    
    /**
     * 
     */
    private static function cfg()
    {
        global $modx;

        $isTest = (int)$modx->getOption('effectpay.sberbank.is_test', null, 1);
        $pass = $modx->getOption('effectpay.sberbank.passwords', null, '');
        $pass = explode('||', $pass);
        $returnPage = (int)$modx->getOption('effectpay.return_page', null, 1);

        $tax = 0;
        $taxCfg = $modx->getOption('effectpay.tax', null, '');
        if ($taxCfg == '20/120') {
            $tax = 7; // НДС чека по расчётной ставке 20/120
        }

        return [
            'isTest' => $isTest,
            'id' => $modx->getOption('effectpay.sberbank.id', null, ''),
            'pass' => $isTest ? ($pass[0] ?? '') : ($pass[1] ?? ''),
            'returnUrl' => $modx->makeUrl($returnPage, '', '', 'full'),
            'tax' => $tax,
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

        $orderBundle = [];
        $discount = $order['discount'] ?? 0;
        $tax =  ["taxType" => $cfg['tax']];

        if (!empty($order['contacts']['email'])) {
            // выключено из-за строгой валидации email
            // $orderBundle['customerDetails']['email'] = $order['contacts']['email'];
        }

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
        
  
        $url = $cfg['isTest'] ? 'https://3dsec.sberbank.ru' : 'https://securepayments.sberbank.ru';
        $data = [
			'userName' => $cfg['id'],
			'password' => $cfg['pass'],
			'returnUrl' => $cfg['returnUrl'],
			'orderNumber' => $order['id'],
			'amount' => self::calcPrice($order['total_price'], 0),
            'orderBundle'=>json_encode($orderBundle),
            'taxSystem' => 0, // СНО - общая
            'sessionTimeoutSecs' => 60 * 60 * 24 * 7, // неделя
        ];
        
		$curl = curl_init();
		curl_setopt_array($curl, array(
		    CURLOPT_URL =>  $url . "/payment/rest/register.do",
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_POST => true,
		    CURLOPT_POSTFIELDS => http_build_query($data)
		));
		$responseStr = curl_exec($curl);
		curl_close($curl);
		
        if ($responseStr){
		    $response = json_decode($responseStr, true);
		    if ($response['orderId'] && $response['formUrl']) {
                return [1, [
                    'pay_key' => $response['orderId'],
                    'pay_link' => $response['formUrl'],
                ]];
		    } else {
                return [0, 'Ошибка онлайн-оплаты ' . $responseStr];
		    }
		} else {
            return [0, 'Ошибка онлайн-оплаты (не пришёл ответ)'];
		}
		
        return [1, $out];
    }


    /**
     * Пример запроса: orderNumber=5&mdOrder=63e518bc-e19d-7b1c-63c5-18be04b1cf02&operation=deposited&status=1
     * Возвращает ok или error для дебага, для работы не нужно
     */
    public function callback(array $input)
    {
        $id = (int)$input['orderNumber'];

        $order = Pay::getOrder($id);
        $pay_key = $order['options']['pay_key'] ?? '';

        if ($order && $input['operation'] == "deposited" && $input['mdOrder'] == $pay_key) {
            $success = Pay::changeStatus($id, (int)$input['status']);
            if ($success) return 'ok';
        }

        return 'error';
    }


}
