<?php


namespace Adv\Duplicates\Messages;


use Bitrix\Forum\MessageTable;
use Bitrix\Main\Application;

class Forum
{
    public static function prepareMessageListParams($userId)
    {
        $select = ['ID', 'AUTHOR_ID'];
        switch (gettype($userId)) {
            case 'array':
                return [
                    'filter' => [
                        'LOGIC' => 'OR',
                        [
                            '=AUTHOR_ID' => $userId
                        ]
                    ],
                    'select' => $select
                ];
            default:
                return [
                    'filter' => [
                        'AUTHOR_ID' => $userId
                    ],
                    'select' => $select
                ];
        }
    }

    public static function prepareSql($oldUser, $newUser)
    {
        $table = MessageTable::getTableName();
        return "update $table set AUTHOR_ID = {$newUser} where AUTHOR_ID = {$oldUser}";

    }

    public static function prepareSqlMulti(array &$sqls, $oldUser, $newUser)
    {
        switch (gettype($oldUser)) {
            case 'array':
                foreach ($oldUser as $oldUserId) {
                    $sqls[] = self::prepareSql($oldUserId, $newUser);
                }
                break;
            case 'integer':
                $sqls[] = self::prepareSql($oldUser, $newUser);
        }

        return $sqls;
    }

    public static function prepareMessageUpdateParams($userId)
    {
        return [
            'AUTHOR_ID' => $userId
        ];
    }

    public static function getMessagesByUser($userId)
    {
        $messageIds = [];

        $messageParams = self::prepareMessageListParams($userId);
        $messages = MessageTable::getList($messageParams)->fetchAll();

        foreach ($messages as $message) {
            $authorId = (int) $message['AUTHOR_ID'];
            if($userId === $authorId || in_array($authorId, $userId)) {
                $messageIds[] = (int) $message['ID'];
            }
        }

        return $messageIds;
    }

    public static function changeMessageAuthor($oldUser, $newUser)
    {
        $conn = Application::getConnection();

        $sqls = [];
        self::prepareSqlMulti($sqls, $oldUser, $newUser);

        if(!empty($sqls)) {
            foreach ( $sqls as $sql) {
                $conn->queryExecute($sql);
            }
        }

        unset($sqls);

    }
}