<?php

class Effectpay
{
    const SHOP = 'shopkeeper3';

    /**
     * 
     */
    public static function getOrder(int $id)
    {
        switch (self::SHOP) {
            case 'shopkeeper3':
                return Shopkeeper::getOrder($id);
        }
    }


    /**
     * 
     */
    public static function changeStatus(int $id, $isSuccess)
    {
        switch (self::SHOP) {
            case 'shopkeeper3':
                return Shopkeeper::changeStatus($id, $isSuccess);
        }
    }
}