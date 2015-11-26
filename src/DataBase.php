<?php
/**
 * Created by PhpStorm.
 * User: aliaxander
 * Date: 08.11.15
 * Time: 13:51
 */
namespace Ox;

use Doctrine\DBAL\DriverManager;

class DataBase
{
    public static $where, $orderBy, $table;
    protected static $whereTpl, $whereParams = array(), $limit, $dataTpl, $dataParams = array(), $forIn;
    public $dbh;

    public function __construct()
    {
        $config = new \Doctrine\DBAL\Configuration();
        $this->dbh = DriverManager::getConnection(array(
            'dbname' => Config::$dbname,
            'user' => Config::$dbuser,
            'password' => Config::$dbuserpass,
            'host' => Config::$dbhost,
            'driver' => Config::$dbDriver,
            'driverOptions' => array(
                1002 => 'SET NAMES utf8'
            )
        ), $config);

    }

    /**
     * @param $string
     *
     * @return $this
     */
    public function table($string)
    {
        static::$table = $string;
        return $this;
    }

    /**
     * @param $array
     *
     * @return $this
     */
    public function where($array = array())
    {
        if (!empty($array)) {
            $where = $this->buildParams($array, "w_");
            static::$whereTpl = $where['tpl'];
            static::$whereParams = $where['data'];
        }
        return $this;
    }

    protected function clearParam()
    {
        self::$where = "";
        self::$orderBy = "";
        self::$table = "";
        self::$whereTpl = "";
        self::$whereParams = array();
        self::$limit = "";
        self::$dataTpl = "";
        self::$dataParams = array();
    }

    protected function buildParams($params = array(), $sub = "")
    {
        $doubleKeys = array();
        $count = 0;
        $CharFix = new CharFix();
        foreach ($params as $key => $val) {
            $keyChar = explode("|", $key);
            if (isset($keyChar['1'])) {
                switch ($keyChar['1']) {
                    case("noSpec"):
                        $val = $CharFix->noSpec($val);
                        break;
                    case("noHtml"):
                        $val = $CharFix->noHtml($val);
                        break;
                    case("charNumber"):
                        $val = $CharFix->charNumber($val);
                        break;
                    case("char"):
                        $val = $CharFix->char($val);
                        break;
                    case("number"):
                        $val = $CharFix->number($val);
                        break;
                }
            }

            $spec = "=";
            $specStart = "";
            $specStop = "";
            $key = $keyChar['0'];
            if (!empty($keyChar['2'])) {
                $and = $keyChar['2'];
            } else {
                $and = "and";
            }
            $keyCheck = explode("/", $key);
            $in = false;
            if (!empty($keyCheck[1])) {
                $key = $keyChar['0'] = $keyCheck['0'];
                switch ($keyCheck[1]) {
                    case ("="):
                        $spec = "=";
                        break;
                    case ("!="):
                        $spec = "<>";
                        break;
                    case (">"):
                        $spec = ">";
                        break;
                    case ("<"):
                        $spec = "<";
                        break;
                    case ("<="):
                        $spec = "<=";
                        break;
                    case (">="):
                        $spec = ">=";
                        break;
                    case ("in"):
                        $in = $keyCheck[1];
                        break;
                    case ("not in"):
                        $in = $keyCheck[1];
                        break;
                }
            }
           // echo $key . "\n";
            $key = $CharFix->charNumber($key);
            if (isset($doubleKeys[$sub . $key])) {
                $doubleKeys[$sub . $key . $count++] = 0;
                $dataPocess[$sub . $key . $count] = $val;
                $tplKeyCh = "{$sub}{$key}{$count}";
            } else {
                $dataPocess[$sub . $key] = $val;
                $doubleKeys[$sub . $key] = 0;
                $tplKeyCh = "{$sub}{$key}";
            }
            $tplKey = ":" . $tplKeyCh;
            if ($in !== false) {
                $tplKey = "";
                foreach ($val as $keyV => $valV) {
                    $dataPocess[$tplKeyCh . "" . $keyV] = $valV;
                    if (!empty($tplKey)) {
                        $tplKey .= ",";
                    }
                    $tplKey .= ":{$tplKeyCh}{$keyV}";
                }
                $spec = " {$in} ";
                $specStart = "(";
                $specStop = ")";
                unset($dataPocess[$tplKeyCh]);

            }
            $str = "`{$keyChar['0']}`{$spec}{$specStart}{$tplKey}{$specStop}";
            if (!isset($tplProcess)) {
                $tplProcess = $str;
            } else {
                $tplProcess .= " {$and} " . $str;
            }
        }
        if (empty($dataPocess))
            $dataPocess = array();
        if (empty($tplProcess))
            $tplProcess = "";
        return array("data" => $dataPocess, "tpl" => $tplProcess);

    }

    /**
     * @param $array
     *
     * @return $this
     */
    public function data($array = array())
    {
        if (!empty($array)) {
            $where = $this->buildParams($array, "d_");
            static::$dataTpl = str_replace(" and ", ', ', $where['tpl']);
            static::$dataParams = $where['data'];
        }
        return $this;
    }

    /**
     * @param $array
     *
     * @return $this
     */
    public function orderBy($array = array())
    {
        $orderBy = "";

        if (!empty($array) and gettype($array) != "string") {
            foreach ($array as $key => $val) {
                if (!empty($orderBy)) {
                    $orderBy .= ", ";
                }
                $orderBy .= "`$key` $val";
            }
            static::$orderBy = " ORDER BY {$orderBy}";
        } elseif (!empty($array)) {
            static::$orderBy = " ORDER BY {$array}";
        }
        return $this;
    }

    /**
     * @param $array
     *
     * @return $this
     */
    public function limit($array = array())
    {
        $limit = "";
        foreach ($array as $key => $val) {
            if (!empty($limit)) {
                $limit .= ", ";
            }
            $limit .= "$key, $val";
        }
        if (!empty($limit)) {
            static::$limit = " LIMIT {$limit}";
        }
        return $this;
    }

    /**
     * @return array
     */
    public function show()
    {
        $array = array(
            "table" => self::$table,
            "where" => self::$whereTpl,
            "data" => self::$whereParams,
            "orderby" => self::$orderBy
        );
        return $array;
    }

    /**
     * @param $where
     *
     * @return $this
     */
    public function setFreeWhere($where = array())
    {
        self::$whereTpl = $where;
        return $this;
    }

    /**
     * @return object|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function read()
    {
        if (empty(self::$table)) {
            return "No isset table";
        } else {
            $where = "";
            if (!empty(self::$whereTpl)) {
                $where = "WHERE " . self::$whereTpl;
            }
            try {
                $sqltxt = "SELECT * FROM `" . self::$table . "` {$where} " . self::$orderBy . " " . self::$limit . ";";
                $sth = $this->dbh->prepare($sqltxt);
                $sth->execute(self::$whereParams);
                $result = $sth->fetchAll(\PDO::FETCH_OBJ);
                $this->clearParam();
                return (object)array("count" => $sth->rowCount(), "rows" => $result, "sqlquery" => $sqltxt, "errorInfo" => $sth->errorInfo());
            } catch (\PDOException $e) {
                $this->clearParam();
                return "ERROR: $e";
            }

        }

    }

    /**
     * @return object|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function update()
    {
        if (empty(self::$table)) {
            return "No isset table";
        } elseif (empty(self::$dataParams)) {
            return "No isset data";
        } else {
            $where = "";
            if (!empty(self::$whereTpl)) {
                $where = "WHERE " . self::$whereTpl;
            }
            try {
                $sqltxt = "UPDATE `" . self::$table . "` SET " . self::$dataTpl . " {$where} " . self::$orderBy . " " . self::$limit;
                $sth = $this->dbh->prepare($sqltxt);
                $sth->execute(self::$dataParams + self::$whereParams);
                $this->clearParam();
                return (object)array("count" => $sth->rowCount(), "sqlquery" => $sqltxt, "errorInfo" => $sth->errorInfo());
            } catch (\PDOException $e) {
                $this->clearParam();
                return "ERROR: $e";
            }
        }
    }

    /**
     * @return object|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function delete()
    {
        if (empty(self::$table)) {
            return "No isset table";
        } else {
            $where = "";
            if (!empty(self::$whereTpl)) {
                $where = "WHERE " . self::$whereTpl;
            }
            try {
                $sqltxt = "DELETE FROM `" . self::$table . "` {$where} " . self::$orderBy . " " . self::$limit;
                $sth = $this->dbh->prepare($sqltxt);
                $sth->execute(self::$whereParams);
                $result = "";
                $this->clearParam();
                return (object)array("count" => $sth->rowCount(), "rows" => $result, "sqlquery" => $sqltxt, "errorInfo" => $sth->errorInfo());
            } catch (\PDOException $e) {
                $this->clearParam();
                return "ERROR: $e";
            }
        }
    }

    /**
     * @return object|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function create()
    {
        if (empty(self::$table)) {
            return "No isset table";
        } elseif (empty(self::$dataParams)) {
            return "No isset data";
        } else {
            try {
                $sqltxt = "INSERT INTO `" . self::$table . "` SET " . self::$dataTpl;
                $sth = $this->dbh->prepare($sqltxt);
                $sth->execute(self::$dataParams);
                $this->clearParam();
                return (object)array("count" => $sth->rowCount(), "id" => $this->dbh->lastInsertId(), "sqlquery" => $sqltxt, "errorInfo" => $sth->errorInfo());
            } catch (\PDOException $e) {
                $this->clearParam();
                return "ERROR: $e";
            }
        }
    }

    /**
     * @param $array
     *
     * @return bool
     */
    public function transaction($array)
    {
        $this->clearParam();
        try {
            // do stuff
            $this->dbh->beginTransaction(); // start inner transaction, nesting level 2
            foreach ($array as $val) {
                $stmt = $this->dbh->prepare($val);
                $stmt->execute();
            }
            try {
                // do stuff
                $this->dbh->commit(); // commits inner transaction, does not start a new one
                return true;
            } catch (\Exception $e) {
                $this->dbh->rollback(); // rolls back inner transaction, does not start a new one
                return false;
            }
            $this->dbh->commit(); // commits outer transaction, and immediately starts a new one
        } catch (\Exception $e) {
            $this->dbh->rollback(); // rolls back outer transaction, and immediately starts a new one
            return false;
        }
    }
}