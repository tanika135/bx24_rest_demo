<?php

class OnOrderSave
{
    public static function getDealId (int $orderId) {
        return CRest::call(
            'crm.deal.list',
            [
                'filter' => [CRM_ORDER_ID => $orderId],
                'select' => ["ID"]
            ]);
    }

}