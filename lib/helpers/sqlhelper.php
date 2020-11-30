<?php


namespace Adv\Migrations\Helpers;

/**
 * Class SqlHelper
 *
 * @package Adv\Migrations\Helpers
 */

class SqlHelper
{
    /**
     * @param array $values
     *
     * @return string
     */
    public static function prepareValues(array $values)
    {
        foreach ($values as $field => $value) {
            $result = "$field = ";

            switch (gettype($value)) {
                case 'integer':
                    $result .= $value;
                    break;
                case 'string':
                    $result .= "'$value'";
                    break;
            }

            return $result;
        }
    }

    /**
     * @param string $table
     * @param array $data
     * @param array $where
     * @param null|string $logic
     *
     * @return bool|string
     */
    public static function prepareSqlForUpdate($table, $data, $where, $logic = null)
    {
        if(count($data) > 1) {
            return false;
        }

        $sql = "update $table set " . self::prepareValues($data) . " where ";

        if(!is_null($logic) && count($where) > 1) {

            $wheres = [];

            foreach ($where as $item) {
                $wheres[] = self::prepareValues($item);
            }

            $sql .= implode(" $logic ", $wheres);

        } else {
            $sql .= self::prepareValues($where);
        }

        return $sql;
    }

    /**
     * @param array $sqls
     * @param string $table
     * @param array $datas
     * @param array $where
     * @param null|string $logic
     *
     * @return array|bool
     */
    public static function prepareSqlForUpdateMulti(&$sqls, $table, $datas, $where, $logic = null)
    {
        if(count($datas) <= 1) {
            return false;
        }

        foreach ($datas as $data) {
            $sqls[] = self::prepareSqlForUpdate($table, $data, $where, $logic);
        }

        return $sqls;
    }

}