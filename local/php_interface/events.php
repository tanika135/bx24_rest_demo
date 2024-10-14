<?php
use Bitrix\Main\EventManager;
use Bitrix\Sale;

EventManager::getInstance()->addEventHandler(
    'sale',
    'OnOrderAdd',
    ['MyClass', 'OnOrderAddHandler']
);

EventManager::getInstance()->addEventHandler(
    'sale',
    'OnOrderSave',
    ['MyClass', 'OnOrderSaveHandler']
);

class MyClass
{
    public static function OnOrderAddHandler($ID, $arFields)
    {
//        $order = Sale\Order::load($ID);
//        $propertyCollection = $order->getPropertyCollection();

        $name = $arFields["ORDER_PROP"][1];
        $email = $arFields["ORDER_PROP"][2];
        $phone = $arFields["ORDER_PROP"][3];

        echo "<pre>";
        print_r($name);
        echo "<pre>";
        $contactId = OnOrderAdd::addContact($name, $phone, $email);
        /**
         * создается сделка
         */
        OnOrderAdd::addOrder($ID, $arFields, $contactId);
    }


    public static function OnOrderSaveHandler($orderId, $fields, $orderFields, $isNew)
    {
        $order = Sale\Order::load($orderId);
        $paymentCollection = $order->getPaymentCollection();
        $paymentCollection->isPaid();

        if ($paymentCollection->isPaid() === true) {
            $dealId = OnOrderSave::getDealId($orderId);
            $newPayment = CRest::call(
                'crm.deal.update',
                [
                    "id" => $dealId["result"][0]["ID"],
                    'fields' => [
                        CRM_ORDER_PAID => true, //свойство заказа "Оплачено"
                        CRM_DATETIME_PAYMENT =>  date("Y-m-d H:i:s", time()),
                    ],
                ]);
        }
    }
}