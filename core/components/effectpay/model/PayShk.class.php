<?php

class PayShk
{

    /**
     * 
     */
    public static function pay(int $id, $payment = '')
    {
        global $modx;

        $pay = '';
        $link = '';
        $error = '';

        if (stripos($payment, 'robokassa') !== false) {
            $pay = 'robokassa';
        }
        if (stripos($payment, 'sberbank') !== false || stripos($payment, 'сбербанк') !== false) {
            $pay = 'sberbank';
        }

        if ($pay) {
            $url = $modx->getOption('site_url');

            $statuses = $modx->getOption('effectpay.shk.statuses', null, '2||6||7');
            $statuses = explode('||', $statuses);

            $order = $modx->getObject('shk_order', $id);
            $order->set('status', $statuses[0] ?? 2);

            $opts = json_decode($order->get('options'), true);

            if ($pay == 'robokassa') {
                $key = uniqid();
                $link = $url . "assets/components/effectpay/payment.php?mode=robokassa_pay&id={$id}&key={$key}";
            } else if ($pay == 'sberbank') {
                $resp = PaySberbank::pay($id);
                if ($resp[0]) {
                    $key = $resp[1]['pay_key'];
                    $link = $resp[1]['pay_link'];
                } else {
                    $error = $resp[1] ?? 'Ошибка оплаты';
                }
            }

            if (!$error) {
                $opts['pay_key'] = $key;
                $opts['pay_link'] = $link;
            }
            $order->set('options', json_encode($opts));
            $order->save();  
        }


        $_SESSION['shk_pay_method'] = $pay;
        $_SESSION['shk_pay_link'] = $link?:false;
        $_SESSION['shk_pay_error'] = $error?:false;

        return true;
    }


    /**
     * 
     */
    public static function changeStatus(int $id, $isSuccess)
    {
        global $modx;
        if (!defined('SHOPKEEPER_PATH')) define('SHOPKEEPER_PATH', MODX_CORE_PATH."components/shopkeeper3/");
        $modx->addPackage('shopkeeper3', SHOPKEEPER_PATH . 'model/');

        $statuses = $modx->getOption('effectpay.shk.statuses', null, '2||6||7');
        $statuses = explode('||', $statuses);
        $status = $isSuccess ? ($statuses[1] ?? 6) : ($statuses[2] ?? 7);
        
        $order = $modx->getObject('shk_order', $id, false);
        if (!$order) return false;
        $oldStatus = $order->get('status');

        if (in_array($oldStatus, $statuses) && $oldStatus != $status) {
            $modx->runProcessor('updateorderstatus',
                ['status' => $status, 'order_id' => [$id]],
                ['processors_path' => $modx->getOption('core_path') . 'components/shopkeeper3/processors/mgr/']
            );
            return true;
        }

        return false;
    }


    /**
     * 
     */
    public static function getOrder(int $order)
    {
        global $modx;

        if(!defined('SHOPKEEPER_PATH'))define('SHOPKEEPER_PATH', MODX_CORE_PATH."components/shopkeeper3/");
        $modx->addPackage( 'shopkeeper3', SHOPKEEPER_PATH . 'model/' );

        $order = $modx->getObject('shk_order',$order,false);
        if(!$order)return false;
        $cart_q = $modx->newQuery('shk_purchases',array('order_id'=>(int)$order->id),false);
        $cart_q->prepare();
        $cart_q->stmt->execute();
        $cart=array();
        while ($purchase=$cart_q->stmt->fetch(PDO::FETCH_ASSOC)) {
            $_keys=explode('||',str_replace('shk_purchases_','',implode('||',array_keys($purchase))));
            $purchase = array_combine($_keys,array_values($purchase));
            $purchase['options']=json_decode($purchase['options'],true)?:[];
            $purchase['_price']=$purchase['price'];
            $purchase['id'] = $purchase['p_id'];
            $purchase['qty'] = $purchase['count'];
            foreach($purchase['options'] as $p_option){
                if(!empty($p_option[1]))$purchase['price']+=round((float)$p_option[1],2);
            }
            $purchase['price_count']=round($purchase['price']*$purchase['count'],2);
            $purchase['total_price'] = $purchase['price_count'];
            $cart[]=$purchase;
        }
        if ($order->delivery_price) {
            $cart[]=array(
                'id'=>'delivery',
                'name'=>'Доставка',
                'price'=>$order->delivery_price,
                'price_count'=>$order->delivery_price,
                'total_price'=>$order->delivery_price,
                'options'=>array(),
                'qty'=>1,
            );
        }
        $_order=$order->toArray();
        $_order['options']=json_decode($order->options,true);
        $_order['items'] = $cart;
        $_order['total_price'] = $_order['price'];

        $contacts = json_decode($order->contacts,true);
		$_order['contacts'] = array_column($contacts, 'value', 'name');

        return $_order;
    }
}