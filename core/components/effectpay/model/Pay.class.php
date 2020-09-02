<?php

class Pay
{
    const SHOP = 'shopkeeper3';

    
    /**
     * 
     */
    public static function getOrder(int $id)
    {
        switch (self::SHOP) {
            case 'shopkeeper3':
                return PayShk::getOrder($id);
        }
    }


    /**
     * 
     */
    public static function changeStatus(int $id, $isSuccess)
    {
        switch (self::SHOP) {
            case 'shopkeeper3':
                return PayShk::changeStatus($id, $isSuccess);
        }
    }


    /**
     * 
     */
    private static function getPaymentKey($payment)
    {
        $pay = '';
        if (stripos($payment, 'robokassa') !== false) {
            $pay = 'robokassa';
        }
        if (stripos($payment, 'sberbank') !== false || stripos($payment, 'сбербанк') !== false) {
            $pay = 'sberbank';
        }
        if (stripos($payment, 'paykeeper') !== false) {
            $pay = 'paykeeper';
        }
        return $pay;
    }


    /**
     * 
     */
    public static function payment(int $id, $payment)
    {
        global $modx;

        $method = self::getPaymentKey($payment);
        $link = '';
        $key = '';
        $error = '';

        if ($method == 'robokassa') {
            $url = $modx->getOption('site_url');
            $key = uniqid();
            $link = $url . "assets/components/effectpay/payment.php?mode=robokassa_pay&id={$id}&key={$key}";
        } else if ($method == 'sberbank') {
            $resp = PaySberbank::pay($id);
            if ($resp[0]) {
                $key = $resp[1]['pay_key'];
                $link = $resp[1]['pay_link'];
            } else {
                $error = $resp[1] ?? 'Ошибка оплаты';
            }
        } else if ($method == 'paykeeper') {
            $resp = PayPaykeeper::pay($id);
            if ($resp[0]) {
                $key = $resp[1]['pay_key'];
                $link = $resp[1]['pay_link'];
            } else {
                $error = $resp[1] ?? 'Ошибка оплаты';
            }
        }

        return [
            'method' => $method,
            'pay_link' => $link,
            'pay_key' => $key,
            'error' => $error,
        ];
    }

}