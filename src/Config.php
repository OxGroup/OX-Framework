<?php
/**
 * Created by oxCRM.
 * User: aliaxander
 * Date: 10.07.15
 * Time: 12:58
 */

namespace ox\core;


class Config
{

    public $domain="oxcrm.dev";
    public $dbhost = "localhost";
    public $dbname = "oxcrm";
    public $dbuser = "oxcrm";
    public $dbuserpass = "oxcrm";


    public static $curr = array("ru" => "руб.", "by" => "руб.", "ua" => "грн.", "kz" => "тенге.",);


    public function __construct(){
 
        if(file_exists(__DIR__."/../test.conf.php")){
            include_once(__DIR__."/../test.conf.php");
            $this->domain=\TestConfig::$domain;
            $this->dbhost=\TestConfig::$dbhost;
            $this->dbname=\TestConfig::$dbname;
            $this->dbuser=\TestConfig::$dbuser;
            $this->dbuserpass=\TestConfig::$dbuserpass;

        }
    }
    public function subDomain(){
        $dir=str_replace($this->domain,"",$_SERVER['HTTP_HOST']);
        if(isset($dir{0}) and $dir{0}==".")$dir = substr_replace($dir, "", 0, 1);

        if(substr($dir, -1)==".")$dir = substr_replace($dir, "", -1);
        return $dir;
    }

    public function checkHost(){
       if($this->domain==$_SERVER['HTTP_HOST']){
           return true;
       }else{
           return false;
       }
    }


}