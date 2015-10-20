<?php
/**
 * Created by OxProfitGroup.
 * User: aliaxander
 * Date: 06.06.15
 * Time: 14:36
 */

namespace Ox;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Auth extends AbstractModel
{
	public static $table = "users";
	protected static $userConfig;

	public static function addSession($user)
	{
echo "1";
		//self::delSession();
		echo "2";
		$data = self::getUserConfig($user);
		echo "3";
		if (!empty($data->rows[0]->remember_token)) {
			$newremember_token = $data->rows[0]->remember_token;
			echo "4";
		} else {
			echo "5";
			$hash = new \Ox\Hash;
			$newremember_token = $hash->make($data->rows['0']->remember_token . date("H:m:d:Y:s"));
			self::Update(array("remember_token" => $newremember_token), array("id" => $user));
			echo "6";
		}
		echo "7";
		$response = new Response();
		$response->headers->setCookie(new Cookie('id', $user, time() + 60 * 60 * 24 * 30 * 12, "/"));
		$response->headers->setCookie(new Cookie('username', $data->rows['0']->email, time() + 60 * 60 * 24 * 30 * 12, "/"));
		$response->headers->setCookie(new Cookie('remember_token', $newremember_token, time() + 60 * 60 * 24 * 30 * 12, "/"));
		$response->send();
		echo "8";
		return true;
	}

	public static function delSession()
	{
		$response = new Response();
		$response->headers->clearCookie('id', '');
		$response->headers->clearCookie('username', '');
		$response->headers->clearCookie('remember_token', '');
		$response->send();

		return true;
	}

	private static function getUserConfig($id = NULL)
	{
		return self::findByColumn(array("id" => $id));
	}

	public static function getStatus()
	{
		$cookie=new Request($_COOKIE);
	

			$user = self::findByColumn(array("id" => $cookie->get("id")));

			if (isset($user->rows['0']->remember_token) and isset($user->rows['0']->email) and $user->rows['0']->remember_token == $cookie->get("remember_token") and $user->rows['0']->email == $cookie->get("username")) {
				self::$userConfig = $user->rows['0'];
				return true;
			} else {
				self::$userConfig = false;
				return false;
			}
	}

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

	public static function GiveMeUserSettings()
	{
		$id = self::$userConfig;
		if (empty($id)) {
			$id = (object)array();
			$id->id = 0;
			$id->name = null;
		}
		return $id;
	}

}
