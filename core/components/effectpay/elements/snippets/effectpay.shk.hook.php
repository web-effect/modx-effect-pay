<?php
if ($hook) {
    require MODX_CORE_PATH . 'components/effectpay/autoload.php';

    $fields = $hook->getValues();

    $pay = PayShk::pay($fields['orderID'], $fields['payment']);

    return true;
}