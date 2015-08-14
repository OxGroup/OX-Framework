<?php
/**
 * Created by OxCRM.
 * User: Александр
 * Date: 31.05.2015
 * Time: 20:44
 */

//Activ method :)

/**
 * Class AbstractModel
 */
namespace oxCore;
use \oxCore\dbMysql;
abstract class AbstractModel

{
    protected static $table;

    /**
     * @return array|string
     *
     */
    public static function findAll()
    {
        $mysql = new dbMysql();
        $mysql->cfg = array("table" => static::$table);
        return $mysql->read();
    }

    public static function findByColumn($data)
    {
        $mysql = new dbMysql();
        $mysql->cfg = array("table" => static::$table,"where"=>$data);
        return $mysql->read();
    }

    /**
     * @param $data
     * @return array|string
     */
    public static function Add($data)
    {
        $mysql = new dbMysql();
        $mysql->cfg = array("table" => static::$table, "data" => $data);
        return $mysql->create();
    }

    /**
     * @param $data
     * @param $where
     * @return array|string
     */
    public static function Update($data,$where)
    {
        $mysql = new dbMysql();
        $mysql->cfg = array("table" => static::$table, "data" => $data, "where" => $where);
        return $mysql->update();
    }

    /**
     * @param $where
     * @return array|string
     */
    public static function Delete($where)
    {
        $mysql = new dbMysql();
        $mysql->cfg = array("table" => static::$table, "where" => $where);
        return $mysql->delete();
    }
}