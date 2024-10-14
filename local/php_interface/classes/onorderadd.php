<?php

class OnOrderAdd
{
    public static function addContact(string $name, string $phone, string $email) : int
    {
        /**
         * получает контакт
         */
        if ($contactID = self::findContact($name, $phone, $email))
            return $contactID;

        $result = CRest::call(
            'crm.contact.add',
            [
                'fields' => [
                    "NAME" => $name,
                    "TYPE_ID" => "CLIENT",
                    "OPENED" => "Y",
                    "PHONE" => array((object)["VALUE" => $phone, "VALUE_TYPE" => "WORK"]),
                    "EMAIL" => array((object)["VALUE" => $email]),
                ],
            ]
        );

        print_r($result['result']);
        return $result['result'];

    }

    public static function findContact (string $name, string $phone, string $email) : int
    {
        if ($phone) {
            $result = CRest::call('crm.duplicate.findbycomm', [
                'type' => 'PHONE',
                'values' => [$phone]
            ]);

            if (is_array($result['result']['CONTACT'])) {
                return $result['result']['CONTACT'][0];
            }
        }
        return 0;
    }

    public static function addOrder (int $orderId, array $arFields, int $contactId) :void
    {
        $deal = CRest::call(
            'crm.deal.add',
            [
                'fields' => [
                    CRM_ORDER_ID => $orderId, //id заказа
                    'TITLE' => 'Заказ № '. $orderId,
                    'IS_NEW' => 'Y',
                    "CONTACT_ID" => $contactId,
                    "CURRENCY_ID" => $arFields["CURRENCY"],
                    "OPPORTUNITY" => $arFields["PRICE"],
                ],
                'params' => ["REGISTER_SONET_EVENT" => "Y"]
            ]);
    }
}
