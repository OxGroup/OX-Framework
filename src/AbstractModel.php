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

    protected static function clearCache()
    {
        static::$cache = array();
    }

    /**
     * @param $data
     * @param $orderBy
     *
     * @return bool
     */
    protected static function getCache($data, $orderBy)
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
    protected static function addCache($data, $orderBy, $result)
    {
        $cache = static::$cache;
        $cache[static::$table][serialize($data)][serialize($orderBy)] = $result;
        static::$cache = $cache;
    }

    /**
     * @param array $orderBy
     *
     * @return object|string|array
     */
    public static function findAll($orderBy = array(), $limit = array())
    {
        $cache = self::getCache("allData", $orderBy);
        if ($cache == false) {
            $mysql = new DataBase();
            $result = $mysql->table(static::$table)->orderBy($orderBy)->limit($limit)->read();
            self::addCache("allData", $orderBy, $result);
        } else {
            $result = $cache;
        }
        if (!empty($result) && $result->errorInfo[0] == 00000) {
            $success = true;
            $error = null;
        } else {
            $success = false;
            $error = $result->errorInfo[0];
        }
        $result->result = (object)array("success" => $success,
            "error" => $error,
            "method" => "read");
        return $result;
    }

    /**
     * @param       $data
     * @param array $orderBy
     *
     * @return object|string|array
     */
    public static function findByColumn($data, $orderBy = array(), $limit = array())
    {
        $cache = self::getCache($data, $orderBy);
        if ($cache == false) {
            $mysql = new DataBase();
            $result = $mysql->table(static::$table)->where($data)->orderBy($orderBy)->limit($limit)->read();
            self::addCache($data, $orderBy, $result);
        } else {
            $result = $cache;
        }
        if (!empty($result) && $result->errorInfo[0] == 00000) {
            $success = true;
            $error = null;
        } else {
            $success = false;
            $error = $result->errorInfo[0];
        }
        $result->result = (object)array("success" => $success,
            "error" => $error,
            "method" => "read");
        return $result;
    }

    /**
     * @param $data
     *
     * @return object|string|array
     */
    public static function findByColumnFree($data)
    {
        $cache = self::getCache("freeData", $data);
        if ($cache == false) {
            $mysql = new DataBase();
            $result = $mysql->table(static::$table)->setFreeWhere($data)->read();
            self::addCache("freeData", $data, $result);
        } else {
            $result = $cache;
        }
        if (!empty($result) && $result->errorInfo[0] == 00000) {
            $success = true;
            $error = null;
        } else {
            $success = false;
            $error = $result->errorInfo[0];
        }
        $result->result = (object)array("success" => $success,
            "error" => $error,
            "method" => "read");
        return $result;
    }

    /**
     * @param $data
     *
     * @return object|array|string
     */
    public static function Add($data = array())
    {
        $mysql = new DataBase();
        $result = $mysql->table(static::$table)->data($data)->create();
        self::clearCache();
        if (!empty($result) && $result->errorInfo[0] == 00000) {
            $success = true;
            $error = null;
        } else {
            $success = false;
            $error = $result->errorInfo[0];
        }
        $result->result = (object)array("success" => $success,
            "error" => $error,
            "method" => "add");
        return $result;
    }

    /**
     * @param $data
     * @param $where
     *
     * @return object|array|string
     */
    public static function Update($data = array(), $where = array())
    {
        $mysql = new DataBase();
        $result = $mysql->table(static::$table)->data($data)->where($where)->update();
        self::clearCache();
        if (isset($result->errorInfo) and isset($result->errorInfo[0]) and $result->errorInfo[0] == 00000) {
            $success = true;
            $error = null;
        } else {
            $success = false;
            $error = $result->errorInfo[0];
        }
        $result->result = (object)array("success" => $success,
            "error" => $error,
            "method" => "update");
        return $result;
    }

    /**
     * @param $where
     *
     * @return object|array|string
     */
    public static function Delete($where = array())
    {
        $mysql = new DataBase();
        $result = $mysql->table(static::$table)->where($where)->delete();
        self::clearCache();
        if (!empty($result) && $result->errorInfo[0] == 00000) {
            $success = true;
            $error = null;
        } else {
            $success = false;
            $error = $result->errorInfo[0];
        }
        $result->result = (object)array("success" => $success,
            "error" => $error,
            "method" => "delete");
        return $result;
    }

    /**
     * @param           $data
     * @param           $where
     * @param array     $checkArray
     * @param bool|true $update
     * @param string    $customText
     *
     * @return array|bool|object|string
     */
    public static function updateCheckDouble($data = array(), $where = array(), $checkArray = array(), $update = true, $customText = "")
    {
        $doubleFind = self::findByColumn($checkArray);
        if ($doubleFind->count > 1) {
            $double = true;
        } else {
            $double = false;
        }
        if ($update == true or $double == false) {
            $update = self::Update($data, $where);
            if (!empty($update) && $update->errorInfo[0] == 00000) {
                $success = true;
                $error = null;
            } else {
                $success = false;
                $error = $update->errorInfo[0];
            }
            $update->result = (object)array("success" => $success,
                "error" => $error,
                "text" => $customText,
                "method" => "update",
                "double" => $double);
            return $update;
        } else {
            return (object)array("result" => array("success" => false,
                "text" => $customText,
                "method" => "update",
                "double" => $double));
        }
    }

    /**
     * @param           $data
     * @param array     $checkArray
     * @param bool|true $add
     * @param string    $customText
     *
     * @return array|bool|object|string
     */
    public static function addCheckDouble($data = array(), $checkArray = array(), $add = true, $customText = "")
    {
        $doubleFind = self::findByColumn($checkArray);
        if ($doubleFind->count > 0) {
            $double = true;
        } else {
            $double = false;
        }
        if ($add == true or $double == false) {
            $add = self::Add($data);
            if (!empty($add) && $add->errorInfo[0] == 00000) {
                $success = true;
                $error = null;
            } else {
                $success = false;
                $error = $add->errorInfo[0];
            }
            $add->result = (object)array("success" => $success,
                "error" => $error,
                "text" => $customText,
                "method" => "add",
                "double" => $double);
            return $add;
        } else {
            return (object)array("result" => array("success" => false,
                "text" => $customText,
                "method" => "add",
                "double" => $double));
        }
    }
}
