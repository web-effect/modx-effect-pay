<?php
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');
define('MODX_API_MODE', true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/index.php');
$modx = new modX();
$modx->initialize('web');
require MODX_CORE_PATH . 'components/effectpay/autoload.php';
$out = [];

switch ($_REQUEST['mode']) {


    case 'robokassa_pay':
        $id = (int)$_GET['id'];
        $key = $_GET['key'];
        $resp = Robokassa::buildFormData($id, $key);
        
        if (!$resp[0]) {
            die($resp[1] ?? 'Ошибка');
        } else {
            $form = '';
            foreach ($resp[1] as $name => $value) {
                $form .= "<input type=hidden name='$name' value='$value'>";
            }
            print
            "<html><body onload='document.robo.submit()'>".
            "<form name='robo' action='https://auth.robokassa.ru/Merchant/Index.aspx' method=POST>".
            $form.
            "</form>
            </body></html>";
        }
        break;

    case 'robokassa_callback':
        if ($_REQUEST['InvId']) {
            echo Robokassa::callback($_REQUEST);
        }
        break;

    case 'sberbank_callback':
        
        break;

    default:
}