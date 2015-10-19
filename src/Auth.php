<?php
/**
 * Created by OxProfitGroup.
 * User: aliaxander
 * Date: 06.06.15
 * Time: 14:36
 */

namespace Ox;

class Auth extends AbstractModel
{
	public static $table = "users";
	public static $userConfig;
	private static $user, $sess;

	public function __construct($user = NULL, $sess = NULL)
	{
		self::$user = $user;
		self::$sess = $sess;
	}

	public static function addSession()
	{
		self::delSession();

		$CharFix = new \Ox\CharFix();
		$data = self::getUserConfig(self::$user);
		if (!empty($data->rows[0]->remember_token)) {
			$newpass = $data->rows[0]->remember_token;
		} else {
			$hash = new \Ox\Hash;
			$newpass = $hash->make($data->rows['0']->password . date("H:m:d:Y:s"));
			self::Update(array("remember_token" => $CharFix->char($newpass)), array("id" => self::$user));
		}
			setcookie("id", self::$user, time() + 60 * 60 * 24 * 30 * 12, "/",  Config::$domain);
			setcookie("username", $data->rows['0']->email, time() + 60 * 60 * 24 * 30 * 12, "/",  Config::$domain);
			setcookie("pass", $newpass, time() + 60 * 60 * 24 * 30 * 12, "/", Config::$domain);
		return true;
	}

	public static function delSession()
	{

		if (isset($_COOKIE['id']) and isset($_COOKIE['username']) and isset($_COOKIE['pass'])) {

			self::Update(array("remember_token" => ""), array("id" => $_COOKIE['id']));
			self::$user = "";
			self::$sess = "";
				setcookie("id", "", time() + 60 * 60 * 24 * 30 * 12, "/",  Config::$domain);
				setcookie("username", "", time() + 60 * 60 * 24 * 30 * 12, "/", Config::$domain);
				setcookie("pass", "", time() + 60 * 60 * 24 * 30 * 12, "/",  Config::$domain);
			unset($_COOKIE);
			session_destroy();
			return true;
		} else {
			return false;
		}
	}

	private static function getUserConfig($id = NULL)
	{
		return self::findByColumn(array("id" => $id));
	}

	public static function getStatus()
	{
		$CharFix = new \Ox\CharFix();

		if (isset($_COOKIE['id']) and isset($_COOKIE['username']) and $_COOKIE['id'] != "" and $_COOKIE['username'] != "") {
			if (empty($_COOKIE['pass']))
				$_COOKIE['pass'] = "";
			$_COOKIE['pass'] = $CharFix->char($_COOKIE['pass']);
			$user = self::findByColumn(array("id" => $_COOKIE['id']));
			if (empty($_COOKIE['pass'])) {
				$_COOKIE['pass'] = "";
			}
			if (isset($user->rows['0']->remember_token) and isset($user->rows['0']->email) and $user->rows['0']->remember_token == $_COOKIE['pass'] and $user->rows['0']->email == $_COOKIE['username']) {
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


		if (isset($_COOKIE['id']) and isset($_COOKIE['username']) and !empty($_COOKIE['id']) and !empty($_COOKIE['username'])) {
			if (empty($_COOKIE['pass']))
				$_COOKIE['pass'] = "";
			$_COOKIE['pass'] = $CharFix->char($_COOKIE['pass']);
			$user = self::findByColumn(array("id" => $_COOKIE['id']));
			if (empty($_COOKIE['pass'])) {
				$_COOKIE['pass'] = "";
			}
			if (isset($user->rows['0']->remember_token) and isset($user->rows['0']->email) and $user->rows['0']->remember_token == $_COOKIE['pass'] and $user->rows['0']->email == $_COOKIE['username']) {
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
