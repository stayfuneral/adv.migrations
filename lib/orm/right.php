<?php


namespace Adv\Migrations\ORM;

use Bitrix\Main;
use Bitrix\Main\ORM\Data\DataManager;

class RightTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_disk_right';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true
            ),
            'OBJECT_ID' => array(
                'data_type' => 'integer',
                'required' => true
            ),
            'TASK_ID' => array(
                'data_type' => 'integer',
                'required' => true
            ),
            'ACCESS_CODE' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateAccessCode')
            ),
            'DOMAIN' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateDomain')
            ),
            'NEGATIVE' => array(
                'data_type' => 'integer',
                'required' => true
            ),
            'OBJECT' => array(
                'data_type' => 'Bitrix\Disk\DiskObject',
                'reference' => array('=this.OBJECT_ID' => 'ref.ID'),
            ),
            'TASK' => array(
                'data_type' => 'Bitrix\Task\Task',
                'reference' => array('=this.TASK_ID' => 'ref.ID'),
            ),
        );
    }
    /**
     * Returns validators for ACCESS_CODE field.
     *
     * @return array
     */
    public static function validateAccessCode()
    {
        return array(
            new Main\Entity\Validator\Length(null, 50),
        );
    }
    /**
     * Returns validators for DOMAIN field.
     *
     * @return array
     */
    public static function validateDomain()
    {
        return array(
            new Main\Entity\Validator\Length(null, 50),
        );
    }
}