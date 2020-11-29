<?php


namespace Adv\Migrations;


use Bitrix\Crm;

class CrmEntity
{
    protected static $entities = [
        Crm\DealTable::class,
        Crm\LeadTable::class,
        Crm\ContactTable::class,
        Crm\CompanyTable::class,
        Crm\InvoiceTable::class
    ];

    public static function prepareParams($userId)
    {
        $select = ['ID', 'ASSIGNED_BY_ID'];

        switch (gettype($userId)) {
            case 'integer':
                return [
                    'filter' => [
                        'ASSIGNED_BY_ID' => $userId
                    ],
                    'select' => $select
                ];
                break;
            case 'array':
                return [
                    'filter' => [
                        'LOGIC' => 'OR',
                        [
                            '=ASSIGNED_BY_ID' => $userId
                        ],
                        'select' => $select
                    ]
                ];
        }

    }
    public static function getEntities($userId)
    {

    }
}