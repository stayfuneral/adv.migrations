<?php


namespace Adv\Migrations\Entity;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Tasks\TaskTable;
use Bitrix\Tasks\Internals\Task\MemberTable;
use CTasks;

Loader::includeModule('tasks');


class Tasks
{
    const MEMBER_TYPE_ORIGINATOR = 'O';
    const MEMBER_TYPE_RESPONSIBLE = 'R';
    const MEMBER_TYPE_ACCOMPLICE = 'A';
    const MEMBER_TYPE_AUDITOR = 'U';

    public static $taskRoles = ['originator', 'responsible', 'accomplice', 'auditor'];

    protected static function prepareSql($oldUser, $newUser, $type)
    {
        return "update b_tasks_member set USER_ID = $newUser where USER_ID = $oldUser and TYPE = '$type'";
    }

    public static function prepareMemberTableSql(array &$sqls, $oldUser, $newUser, $type)
    {
        switch (gettype($oldUser)) {
            case 'integer':
                $sqls[] = self::prepareSql($oldUser, $newUser, $type);
                break;
            case 'array':
                foreach ($oldUser as $oldId) {
                    $sqls[] = self::prepareSql($oldId, $newUser, $type);
                }
                break;
        }

        return $sqls;
    }

    public static function prepareOrmParams($userId, $accomplice = false, $auditor = false)
    {
        $params = [];

        if(!is_array($userId)) {
            $userId = [$userId];
        }

        $params['b_tasks'] = [
            'filter' => [
                'LOGIC' => 'OR',
                [
                    '=CREATED_BY' => $userId
                ],
                [
                    '=RESPONSIBLE_ID' => $userId
                ]
            ],
            'select' => ['ID', 'CREATED_BY', 'RESPONSIBLE_ID']
        ];

        if($accomplice) {
            $params['b_tasks_member']['filter'] = [
                'LOGIC' => 'OR',
                [
                    '=USER_ID' => $userId,
                    'TYPE' => self::MEMBER_TYPE_ACCOMPLICE
                ]
            ];
        }

        if($auditor) {
            $params['b_tasks_member']['filter'][] = [
                '=USER_ID' => $userId,
                'TYPE' => self::MEMBER_TYPE_AUDITOR
            ];
        }

        return $params;
    }

    public static function getTasks($userId, $accomplice = false, $auditor = false)
    {
        $tasks = [];

        $ormParams = self::prepareOrmParams($userId, $accomplice, $auditor);

        $taskList = TaskTable::getList($ormParams['b_tasks'])->fetchAll();

        foreach ($taskList as $task) {

            $originatorId = (int) $task['CREATED_BY'];
            $responsibleId = (int) $task['RESPONSIBLE_ID'];

            if($userId === $originatorId) {
                $tasks['originator'][] = (int) $task['ID'];
            }

            if($userId === $responsibleId) {
                $tasks['responsible'][] = (int) $task['ID'];
            }

        }

        if(!empty($ormParams['b_tasks_member'])) {

            $memberList = MemberTable::getList($ormParams['b_tasks_member'])->fetchAll();

            foreach ($memberList as $member) {
                switch ($member['TYPE']) {
                    case self::MEMBER_TYPE_ACCOMPLICE:
                        $tasks['accomplice'][] =(int) $member['TASK_ID'];
                        break;
                    case self::MEMBER_TYPE_AUDITOR:
                        $tasks['auditor'][] = (int) $member['TASK_ID'];
                        break;
                }
            }
        }

        return $tasks;
    }

    public static function updateTasks($oldUser, $newUser)
    {
        $result = [];
        $sqls = [];
        $updateOrmParams = null;
        $conn = Application::getConnection();
        $tasks = self::getTasks($oldUser, true, true);

        if(!empty($tasks)) {

            foreach ($tasks as $role => $ids) {

                switch ($role) {
                    case 'originator':
                        $updateOrmParams = ['CREATED_BY' => $newUser];
                        self::prepareMemberTableSql($sqls, $oldUser, $newUser, self::MEMBER_TYPE_ORIGINATOR);
                        break;
                    case 'responsible':
                        $updateOrmParams = ['RESPONSIBLE_ID' => $newUser];
                        self::prepareMemberTableSql($sqls, $oldUser, $newUser, self::MEMBER_TYPE_RESPONSIBLE);
                        break;
                    case 'accomplice':
                        self::prepareMemberTableSql($sqls, $oldUser, $newUser, self::MEMBER_TYPE_ACCOMPLICE);
                        break;
                    case 'auditor':
                        self::prepareMemberTableSql($sqls, $oldUser, $newUser, self::MEMBER_TYPE_AUDITOR);
                        break;
                }

                if(!is_null($updateOrmParams)) {
                    foreach ($ids as $id) {
                        $upd = TaskTable::update($id, $updateOrmParams);
                        if($upd->isSuccess()) {
                            $result[$id] = $upd->isSuccess();
                        } elseif ($upd->getErrorMessages()) {
                            $result['errors'][$id] = $upd->getErrorMessages();
                        }
                    }
                }

                if(!empty($sqls)) {
                    foreach ($sqls as $sql) {
                        $conn->queryExecute($sql);
                    }
                }

                unset($sql);

            }

        }

        return $result;
    }
}