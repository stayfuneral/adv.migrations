<?php


namespace Adv\Migrations\Entity;


class Crm
{

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