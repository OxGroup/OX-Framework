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

    /**
     * @return array|string
     *
     */
    public static function findAll()
    {
        if (!empty($orderBy))
            $orderBy = array("order" => $orderBy);
        $mysql = new DbMysql();
        $mysql->cfg = array("table" => static::$table)+ $orderBy;
        return $mysql->read();
    }

    public static function findByColumn($data,$orderBy=array())
    {
        if(!empty($orderBy))
            $orderBy=array("order" => $orderBy);
        $mysql = new DbMysql();
        $mysql->cfg = array("table" => static::$table,"where"=>$data)+ $orderBy;
        return $mysql->read();
    }
    
    public static function findByColumnFree($data)
    {
        $mysql = new DbMysql();
        $mysql->cfg = array("table" => static::$table);
        $mysql->freeWhere=$data;
        return $mysql->read();
    }

    /**
     * @param $data
     * @return array|string
     */
    public static function Add($data)
    {
        $mysql = new DbMysql();
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
        $mysql = new DbMysql();
        $mysql->cfg = array("table" => static::$table, "data" => $data, "where" => $where);
        return $mysql->update();
    }

    /**
     * @param $where
     * @return array|string
     */
    public static function Delete($where)
    {
        $mysql = new DbMysql();
        $mysql->cfg = array("table" => static::$table, "where" => $where);
        return $mysql->delete();
    }
}
