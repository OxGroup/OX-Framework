<?php
/**
 * Created by OxProfitGroup.
 * User: aliaxander
 * Date: 06.06.15
 * Time: 14:36
 */

namespace Ox;

use Ox\AbstractModel;
use \Symfony\Component\HttpFoundation\Cookie;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

/**
 * Class Auth
 *
 * @package Ox\models
 */
class Auth extends AbstractModel
{
    protected static $table = "users";
    protected static $userConfig;

    /**
     * @param $user
     *
     * @return bool
     */
    public static function addSession($user)
    {

        //self::delSession();

        $data = self::getUserConfig($user);

        if (!empty($data->rows[0]->remember_token)) {
            $newremember_token = $data->rows[0]->remember_token;

        } else {

            $hash = new \Ox\Hash;
            $newremember_token = $hash->make($data->rows['0']->password . date("H:m:d:Y:s"));
            self::update(array("remember_token" => $newremember_token), array("id" => $user));

        }

        $response = new Response();
        $response->headers->setCookie(new Cookie('id', $user, time() + 60 * 60 * 24 * 30 * 12, "/"));
        $response->headers->setCookie(new Cookie('username', $data->rows['0']->email, time() + 60 * 60 * 24 * 30 * 12, "/"));
        $response->headers->setCookie(new Cookie('remember_token', $newremember_token, time() + 60 * 60 * 24 * 30 * 12, "/"));
        $response->sendHeaders();
        return true;
    }

    /**
     * @return Response
     */
    public static function delSession()
    {
        $response = new Response();
        $response->headers->clearCookie('id', '');
        $response->headers->clearCookie('username', '');
        $response->headers->clearCookie('remember_token', '');
        return $response->sendHeaders();
    }

    /**
     * @param null $id
     *
     * @return array|object|string
     */
    private static function getUserConfig($id = null)
    {
        return self::findByColumn(array("id" => $id));
    }

    /**
     * @return bool
     */
    public static function getStatus()
    {
        $cookie = new Request($_COOKIE);


        $user = self::findByColumn(array("id" => $cookie->get("id")));

        if (isset($user->rows['0']->remember_token) and isset($user->rows['0']->email) and $user->rows['0']->remember_token == $cookie->get("remember_token") and $user->rows['0']->email == $cookie->get("username")) {
            self::$userConfig = $user->rows['0'];
            return true;
        } else {
            self::$userConfig = false;
            return false;
        }
    }

    /**
     * @return bool
     */
    public static function getConfigSess()
    {
        $cookie = new Request($_COOKIE);

        $user = self::findByColumn(array("id" => $cookie->get("id")));

        if (isset($user->rows['0']->remember_token) and isset($user->rows['0']->email) and $user->rows['0']->remember_token == $cookie->get("remember_token") and $user->rows['0']->email == $cookie->get("username")) {
            $user->rows['0']->balance = $user->rows['0']->balance - $user->rows['0']->payd;
            self::$userConfig = $user->rows['0'];
            return true;
        } else {
            self::$userConfig = false;
            return false;
        }
    }

    /**
     * @return object
     */
    public static function GiveMeUserSettings()
    {
        $id = self::$userConfig;
        if (empty($id)) {
            $id = (object)array();
            $id->id = 0;
            $id->name = null;
            $id->login = null;
        }
        return $id;
    }

}
