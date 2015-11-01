<?php
/**
 * Created by OxProfitGroup.
 * User: Александр
 * Date: 31.05.2015
 * Time: 20:44
 */

//Activ method :)

/**
 * Class AbstractModel
 */
namespace Ox;

use \Ox\DbMysql;

abstract class AbstractModel

{
    protected static $table;
    protected static $cache;

    /**
     * @param $data
     * @param $orderBy
     *
     * @return bool
     */
    public static function getCache($data, $orderBy)
    {
        $cache = static::$cache;
        if (isset($cache[static::$table][serialize($data)]) and !empty($cache[static::$table][serialize($data)][serialize($orderBy)])) {
            return $cache[static::$table][serialize($data)][serialize($orderBy)];
        } else {
            return false;
        }
    }

    /**
     * @param $data
     * @param $orderBy
     * @param $result
     */
    public static function addCache($data, $orderBy, $result)
    {
        $cache = static::$cache;
        $cache[static::$table][serialize($data)][serialize($orderBy)] = $result;
        static::$cache = $cache;
    }

    public static function clearCache()
    {
        static::$cache = array();
    }


    /**
     * @param array $orderBy
     *
     * @return object|string|array
     */
    public static function findAll($orderBy = array())
    {
        $cache = self::getCache("allData", $orderBy);
        if ($cache == false) {
            if (!empty($orderBy)) {
                $orderBy = array("order" => $orderBy);
            }
            $mysql = new DbMysql();
            $mysql->cfg = array("table" => static::$table) + $orderBy;
            $result = $mysql->read();
            self::addCache("allData", $orderBy, $result);
            return $result;
        } else {
            return $cache;
        }
    }

    /**
     * @param       $data
     * @param array $orderBy
     *
     * @return object|string|array
     */
    public static function findByColumn($data, $orderBy = array())
    {
        $cache = self::getCache($data, $orderBy);
        if ($cache == false) {
            if (!empty($orderBy))
                $orderBy = array("order" => $orderBy);
            $mysql = new DbMysql();
            $mysql->cfg = array("table" => static::$table, "where" => $data) + $orderBy;
            $result = $mysql->read();
            self::addCache($data, $orderBy, $result);
            return $result;
        } else {
            return $cache;
        }
    }

    /**
     * @param $data
     *
     * @return object|string
     */
    public static function findByColumnFree($data)
    {
        $cache = self::getCache("freeData", $data);
        if ($cache == false) {
            $mysql = new DbMysql();
            $mysql->cfg = array("table" => static::$table);
            $mysql->freeWhere = $data;
            $result = $mysql->read();
            self::addCache("freeData", $data, $result);
            return $result;
        } else {
            return $cache;
        }
    }

    /**
     * @param $data
     *
     * @return array|string
     */
    public static function Add($data)
    {
        $mysql = new DbMysql();
        $mysql->cfg = array("table" => static::$table, "data" => $data);
        self::clearCache();
        return $mysql->create();
    }

    /**
     * @param $data
     * @param $where
     *
     * @return array|string
     */
    public static function Update($data, $where)
    {
        $mysql = new DbMysql();
        $mysql->cfg = array("table" => static::$table, "data" => $data, "where" => $where);
        self::clearCache();
        return $mysql->update();
    }

    /**
     * @param $where
     *
     * @return array|string
     */
    public static function Delete($where)
    {
        $mysql = new DbMysql();
        $mysql->cfg = array("table" => static::$table, "where" => $where);
        self::clearCache();
        return $mysql->delete();
    }
}
