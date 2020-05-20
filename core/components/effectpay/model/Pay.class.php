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
}