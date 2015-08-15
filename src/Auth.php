<?php
/**
 * Created by OxProfitGroup.
 * User: aliaxander
 * Date: 06.06.15
 * Time: 14:36
 */

namespace Ox;

use Ox;

class Auth extends AbstractModel
{
    protected static $table = "users";
    private static $user, $sess;
    public static $userConfig;

    public function __construct($user = NULL, $sess = NULL)
    {
        self::$user=$user;
        self::$sess=$sess;
    }


    private static function getUserConfig($id = NULL)
    {
        return self::findByColumn(array("id" => $id));
    }

    public static function addSession()
    {


        $config=new Config();
        $CharFix=new \Ox\CharFix();
        $data = self::getUserConfig(self::$user);
        $hash = new \Ox\Hash;
        $newpass = $hash->make($data->rows['0']->password . date("H:m:d:Y:s"));

       // $_SESSION['id'] = self::$user;
       // $_SESSION['userneme'] = $data->rows['0']->email;
       // $_SESSION['pass'] = $newpass;
        setcookie("id",self::$user, time() + 60 * 60 * 24 * 30 * 12, "/", ".".Config::$domain);
        setcookie("userneme", $data->rows['0']->email, time() + 60 * 60 * 24 * 30 * 12, "/", ".".Config::$domain);
        setcookie("pass", $newpass, time() + 60 * 60 * 24 * 30 * 12, "/", ".".Config::$domain);
        self::Update(array("remember_token"=>$CharFix->char($newpass)),array("id"=>self::$user));

        return true;
    }

    public static function delSession(){

        if(self::getStatus()==true) {
            session_destroy();
            self::Update(array("remember_token"=>""),array("id"=>$_SESSION['id']));
            self::$user="";
            self::$sess="";
            setcookie('id');
            setcookie('userneme');
            setcookie('pass');
         //   $_SESSION['id'] = "";
         //   $_SESSION['userneme'] = "";
         //   $_SESSION['pass'] = "";
            unset($_COOKIE['id']);
            unset($_COOKIE['userneme']);
            unset($_COOKIE['pass']);

            return true;
        }else{
            return false;
        }
    }

    public static function getStatus()
    {

        $CharFix = new \Ox\CharFix();
        if (!empty($_COOKIE['id'])) $_SESSION['id'] = $_COOKIE['id'];
        if (!empty($_COOKIE['userneme'])) $_SESSION['userneme'] = $_COOKIE['userneme'];
        if (!empty($_COOKIE['pass'])) $_SESSION['pass'] = $_COOKIE['pass'];

        if (isset($_SESSION['id']) and isset($_SESSION['userneme']) and !empty($_SESSION['id']) and !empty($_SESSION['userneme'])) {
            if (empty($_SESSION['pass'])) $_SESSION['pass'] = "";
            $_SESSION['pass'] = $CharFix->char($_SESSION['pass']);
            $user = self::findByColumn(array("id" => $_SESSION['id']));
            if (empty($_COOKIE['pass'])) {
                $_SESSION['pass'] = "";
            }
            if (isset($user->rows['0']->remember_token) and isset($user->rows['0']->email) and $user->rows['0']->remember_token == $_SESSION['pass'] and $user->rows['0']->email == $_SESSION['userneme']) {
                $user->rows['0']->balance = $user->rows['0']->balance - $user->rows['0']->payd;
                self::$userConfig = $user->rows['0'];
                return true;
            } else {
                self::$userConfig = false;
                return false;
            }
        } else {
            return false;
        }
    }


    public static function getConfigSess()
    {

        $CharFix = new \Ox\CharFix();


        if (isset($_COOKIE['id']) and isset($_COOKIE['userneme']) and !empty($_COOKIE['id']) and !empty($_COOKIE['userneme'])) {
            if (empty($_COOKIE['pass'])) $_COOKIE['pass'] = "";
            $_COOKIE['pass'] = $CharFix->char($_COOKIE['pass']);
            $user = self::findByColumn(array("id" => $_COOKIE['id']));
            if (empty($_COOKIE['pass'])) {
                $_COOKIE['pass'] = "";
            }
            if (isset($user->rows['0']->remember_token) and isset($user->rows['0']->email) and $user->rows['0']->remember_token == $_COOKIE['pass'] and $user->rows['0']->email == $_COOKIE['userneme']) {
                $user->rows['0']->balance = $user->rows['0']->balance - $user->rows['0']->payd;
                self::$userConfig = $user->rows['0'];
                return true;
            } else {
                self::$userConfig = false;
                return false;
            }
        } else {
            return false;
        }
    }

    public static function GiveMeUserSettings(){
        $id=self::$userConfig;
        if(empty($id)) {
            $id= (object)array();
            $id->id = 0;
            $id->name = null;
        }
        return $id;
    }

}