<?php
/**
 * Created by OxGroupMedia.
 * User: aliaxander
 * Date: 06.06.15
 * Time: 14:36
 */

namespace Ox;

use Ox\AbstractModel;
use OxApp\models\Users;
use \Symfony\Component\HttpFoundation\Cookie;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

/**
 * Class Auth
 *
 * @package Ox\models
 */
class Auth
{
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
            $newrememberToken = $data->rows[0]->remember_token;
        } else {
            $hash = new \Ox\Hash;
            $newrememberToken = $hash->make($data->rows['0']->password . date("H:m:d:Y:s"));
            Users::data(array("remember_token" => $newrememberToken))->where(array("id" => $user))->update();
        }

        $response = new Response();
        $response->headers->setCookie(
            new Cookie('id', $user, time() + 60 * 60 * 24 * 30 * 12, "/")
        );
        $response->headers->setCookie(
            new Cookie('username', $data->rows['0']->email, time() + 60 * 60 * 24 * 30 * 12, "/")
        );
        $response->headers->setCookie(
            new Cookie('remember_token', $newrememberToken, time() + 60 * 60 * 24 * 30 * 12, "/")
        );
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
     * @param null $userId
     *
     * @return bool|object|string
     */
    private static function getUserConfig($userId = null)
    {
        return Users::where(array("id" => $userId))->find();
    }

    /**
     * @return bool
     */
    public static function getStatus()
    {
        $cookie = new Request($_COOKIE);
        $user = Users::where(array("id" => $cookie->get("id")))->find();

        if (isset($user->rows['0']->remember_token, $user->rows['0']->email) &&
            $user->rows['0']->remember_token === $cookie->get("remember_token") &&
            $user->rows['0']->email === $cookie->get("username")
        ) {
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

        $user = Users::find(array("id" => $cookie->get("id")));

        if (isset($user->rows['0']->remember_token, $user->rows['0']->email) &&
            $user->rows['0']->remember_token == $cookie->get("remember_token") &&
            $user->rows['0']->email == $cookie->get("username")
        ) {
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
    public static function giveMeUserSettings()
    {
        $profile = self::$userConfig;
        if (empty($profile)) {
            $profile = (object)array();
            $profile->id = 0;
            $profile->name = null;
            $profile->login = null;
        }
        return $profile;
    }
}
