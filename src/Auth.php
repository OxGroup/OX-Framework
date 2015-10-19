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
	private $sessions=array();

	public function __construct($user = NULL, $sess = NULL)
	{
		self::$user = $user;
		self::$sess = $sess;
	}

	protected static function getSession(){
		$array=array();
		if(empty($sessions) and isset($_COOKIE)){
			foreach($_COOKIE as $key=>$val){
				$array[$key]=$val;
			}
			self::$sessions= $array;
		}else{
			$array= self::$sessions;
		}
		return $array;
	}

	public static function addSession()
	{
		$sessions =self::getSession();

		if(empty(self::$user) and isset($sessions['id'])){
			self::$user= $sessions['id'];
		}
		$data = self::getUserConfig(self::$user);
		if (!empty($data->rows[0]->remember_token)) {
			$newremember_token = $data->rows[0]->remember_token;
		} else {
			$hash = new \Ox\Hash;
			$newremember_token = $hash->make($data->rows['0']->remember_token . date("H:m:d:Y:s"));
			self::Update(array("remember_token" => $newremember_token), array("id" => self::$user));
		}
			setcookie("id", self::$user, time() + 60 * 60 * 24 * 30 * 12, "/",  Config::$domain);
			setcookie("username", $data->rows['0']->email, time() + 60 * 60 * 24 * 30 * 12, "/", Config::$domain);
			setcookie("remember_token", $newremember_token, time() + 60 * 60 * 24 * 30 * 12, "/", Config::$domain);
			setcookie("test", "123", time() + 60 * 60 * 24 * 30 * 12, "/", Config::$domain);

		setcookie("id", "1", time() + 60 * 60 * 24 * 30 * 12, "/", Config::$domain);
		setcookie("username", "1", time() + 60 * 60 * 24 * 30 * 12, "/", Config::$domain);
		setcookie("remember_token", "1", time() + 60 * 60 * 24 * 30 * 12, "/", Config::$domain);
		setcookie("test", "1", time() + 60 * 60 * 24 * 30 * 12, "/", Config::$domain);
		return true;
	}

	public static function delSession()
	{
		$sessions =self::getSession();


		if (isset($sessions['id']) and isset($sessions['username']) and isset($sessions['remember_token'])) {

			self::Update(array("remember_token" => ""), array("id" => $sessions['id']));
			self::$user = "";
			self::$sess = "";

				setcookie("id", "", time() + 60 * 60 * 24 * 30 * 12, "/", Config::$domain);
				setcookie("username", "", time() + 60 * 60 * 24 * 30 * 12, "/", Config::$domain);
				setcookie("remember_token", "", time() + 60 * 60 * 24 * 30 * 12, "/", Config::$domain);
			/*unset($_COOKIE['id']);
			unset($_COOKIE['username']);
			unset($_COOKIE['remember_token']);
	*/
			//session_unset();

			//session_destroy();
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
		$sessions =self::getSession();

		if (isset($sessions['id']) and isset($sessions['username']) and $sessions['id'] != "" and $sessions['username'] != "") {
			if (empty($sessions['remember_token']))
				$sessions['remember_token'] = "";
			$user = self::findByColumn(array("id" => $_COOKIE['id']));
			if (empty($sessions['remember_token'])) {
				$sessions['remember_token'] = "";
			}
			if (isset($user->rows['0']->remember_token) and isset($user->rows['0']->email) and $user->rows['0']->remember_token == $sessions['remember_token'] and $user->rows['0']->email == $sessions['username']) {
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
		$sessions=self::getSession();

		if (isset($sessions['id']) and isset($sessions['username']) and !empty($sessions['id']) and !empty($sessions['username'])) {
			if (empty($sessions['remember_token']))
				$sessions['remember_token'] = "";
			$user = self::findByColumn(array("id" => $sessions['id']));
			if (empty($sessions['remember_token'])) {
				$sessions['remember_token'] = "";
			}
			if (isset($user->rows['0']->remember_token) and isset($user->rows['0']->email) and $user->rows['0']->remember_token == $sessions['remember_token'] and $user->rows['0']->email == $sessions['username']) {
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
