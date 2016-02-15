<?php
/**
 * Created by OxProfitGroup.
 * User: aliaxander
 * Date: 10.07.15
 * Time: 12:58
 */

namespace Ox;


class Config
{
    public static $domain = "test.dev";
    public static $dbhost = "localhost";
    public static $dbname = "test";
    public static $dbuser = "root";
    public static $dbuserpass = "";
    public static $dbDriver = "pdo_mysql";


    public static $curr = array("ru" => "руб.", "by" => "руб.", "ua" => "грн.", "kz" => "тенге.",);


    /**
     * Config constructor.
     */
    public function __construct()
    {

        if (file_exists(__DIR__ . "/../../../../test.conf.php")) {
            include_once(__DIR__ . "/../../../../test.conf.php");
            self::$dbhost = \TestConfig::$dbhost;
            self::$dbname = \TestConfig::$dbname;
            self::$dbuser = \TestConfig::$dbuser;
            self::$dbuserpass = \TestConfig::$dbuserpass;
            if (isset(\TestConfig::$dbDriver))
                self::$dbDriver = \TestConfig::$dbDriver;

        }
    }

    /**
     * @return mixed
     */
    public function subDomain()
    {
        $dir = str_replace(self::$domain, "", $_SERVER['HTTP_HOST']);
        if (isset($dir{0}) and $dir{0} == ".")
            $dir = substr_replace($dir, "", 0, 1);

        if (substr($dir, -1) == ".")
            $dir = substr_replace($dir, "", -1);
        return $dir;
    }

    /**
     * @return bool
     */
    public function checkHost()
    {
        if (self::$domain == $_SERVER['HTTP_HOST']) {
            return true;
        } else {
            return false;
        }
    }


}