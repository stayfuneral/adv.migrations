<?php


namespace Adv\Migrations;

use Bitrix\Main\Loader;
use Bitrix\Disk\Driver;
use Adv\Duplicates\ORM\RightTable;

Loader::includeModule('disk');

class Disk
{
    protected $rightManager;

    public function __construct()
    {
        $this->rightManager = Driver::getInstance()->getRightsManager();
    }

    public function prepareObjectParams($userId)
    {
        $filter = [];
        switch (gettype($userId)) {
            case 'integer':
                $filter['filter'] = [
                    '=ACCESS_CODE' => $this->getUserAccessCode($userId)
                ];
                break;
            case 'array':
                $filter['filter']['LOGIC'] = 'OR';
                foreach ($userId as $id) {
                    $filter['filter'][]['=ACCESS_CODE'][] = $this->getUserAccessCode($id);
                }
        }
        return $filter;
    }

    /**
     * @param $userId
     * @param $objectIds
     *
     * @return array
     */
    public function prepareRightParams($userId, $objectIds)
    {
        $params = [];

        foreach ($objectIds as $id) {
            $params[] = [
                'OBJECT_ID' => $id,
                'TASK_ID' => $this->rightManager::TASK_FULL,
                'ACCESS_CODE' => $this->getUserAccessCode($userId),
                'NEGATIVE' => 0
            ];
        }

        return $params;
    }

    /**
     * @param $userId
     *
     * @return string
     */
    public function getUserAccessCode($userId)
    {
        return 'U' . $userId;
    }

    /**
     * @param $userId
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getUserObjects($userId)
    {
        $objectIds = [];
        $obJectRights = RightTable::getList($this->prepareObjectParams($userId))->fetchAll();

        foreach ($obJectRights as $right) {
            $objectIds[] = (int) $right['OBJECT_ID'];
        }

        return $objectIds;
    }

    /**
     * @param $oldUser
     * @param $newUser
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function setRightsToDiskObjects($oldUser, $newUser)
    {
        $result = [];
        $userObjects = $this->getUserObjects($oldUser);

        if(!empty($userObjects)) {

            $preparedRightParams = $this->prepareRightParams($newUser, $userObjects);

            foreach ($preparedRightParams as $rightParam) {
                $add = RightTable::add($rightParam);

                if($add->isSuccess()) {
                    $result[$add->getId()] = $add->isSuccess();
                }

                if($add->getErrorMessages()) {
                    $result['errors'][] = $add->getErrorMessages();
                }
            }

        }


        return $result;
    }

}