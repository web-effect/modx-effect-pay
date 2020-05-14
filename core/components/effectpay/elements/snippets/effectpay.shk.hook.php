<?php
if ($hook) {
    $fields = $hook->getValues();

    $pay = '';
    $link = '';
    $error = '';

    if (stripos($fields['payment'], 'robokassa') !== false) {
        $pay = 'robokassa';
    }

    if ($pay) {
        $url = $modx->getOption('site_url');

        $statuses = $modx->getOption('effectpay.shk.statuses', null, '2||6||7');
        $statuses = explode('||', $statuses);

        $order = $modx->getObject('shk_order', $fields['orderID']);
        $order->set('status', $statuses[0] ?? 2);

        $opts = json_decode($order->get('options'), true);

        if ($pay == 'robokassa') {
            $key = uniqid();
            $link = $url . "assets/components/effectpay/payment.php?mode=robokassa_pay&id={$fields['orderID']}&key={$key}";
            $opts['pay_key'] = $key;
            $opts['pay_link'] = $link;
        }
        
        $order->set('options', json_encode($opts));
        $order->save();  
    }


    $_SESSION['shk_pay_method'] = $pay;
    if ($link) {
        $_SESSION['shk_pay_link'] = $link;
    }
    if ($error) {
        $_SESSION['shk_pay_error'] = $error;
    }

    return true;
}