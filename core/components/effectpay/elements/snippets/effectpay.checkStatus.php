<?php
require MODX_CORE_PATH . 'components/effectpay/autoload.php';

if (empty($_GET['method'])) return '';

switch ($_GET['method']) {
    case 'alpha':
        if (!empty($_GET['orderId'])) return PayAlpha::getOrderStatus($_GET['orderId']);
    default:
}