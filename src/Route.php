<?php

/**
 * Created by OxProfitGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 14:34
 */

/**
 * Class route
 */
namespace Ox;


class Route
{
	public $route;
	public $class;
	public $cous = 0;
	public $setHost = "all";
	public $status = "all";
	public $config;
	public static $get;

	public function __construct()
	{
		$this->config = new Config();

		if (Auth::getStatus() == true) {
			$this->type = Auth::GiveMeUserSettings()->status;
		} else {
			$this->type = "all";
		}


	}


	private function FileController($route, $class)
	{

		$file = "../http/controllers/" . $class . "_controller.php";
		$file = str_replace("\\", "/", $file);
		if (is_readable($file) == false) {
			die ($file . ' Controller Not Found');
		} else {
			$this->cous++;
			$class .= "_controller";

			try {
				$class = "\\OxApp\\controllers\\" . $class;

				$controller = new  $class();
				if (is_subclass_of($controller, 'Ox\App')) {

					if (!empty($_POST)) {
						$controller->post();
					} else {
						$controller->view();
					}
					die();
				} else {
					$controller = "";
					die ('No extends App');
				}
			} catch (\Exception $e) {
				// catch( Exception $e ) will give no warning, but will not catch Exception
				echo "ERROR: $e";
			}
		}
	}


	public function get($route, $class, $funcs = "", $location = "")
	{
		if (!isset($_GET['q']))
			$_GET['q'] = "/";
		$GET = $_GET['q'];
		if (substr($GET, -1) != "/")
			$GET .= "/";
		if ($GET{0} != "/")
			$GET = "/" . $GET;

		if (substr($route, -1) != "/")
			$route .= "/";
		if ($route{0} != "/")
			$route = "/" . $route;

		if ($this->setHost != "n/a" and $this->setHost != "sub" and $this->setHost != "all") {
			$GET = $this->config->subDomain() . $GET;
			$route = $this->setHost . $route;
		}

		$rootHost = true;
		if ($this->setHost == "sub") {
			if ($this->config->subDomain() == "") {
				$rootHost = false;
			}
		} else {
			$rootHost = $this->config->checkHost();
		}
		if ($this->setHost == "all") {
			$rootHost = true;
		}


		if (($this->type == $this->status or $this->status == "all") and $rootHost == true):
			//  echo $this->type." - ".$route."<br/>";
			if (!empty($funcs)) {

				if ($route == $GET) {
					$this->cous++;
					$func = explode(":", $funcs);
					if (isset($func[1])) {
						if (file_exists(__DIR__ . "/../../../../http/models" . $func[0] . ".php")) {
							$object = "\\OxApp\\models\\" . $func[0];
						} else {
							$object = "\\Ox\\" . $func[0];
						}
						$$func[0] = new $object;
						$$func[0]->$func[1]();
					} else {
						$func[0]();
					}

				} else {
					return false;
				}
			} else {


				$routePreg = str_replace("/", '\/', $route);
				$routePreg = str_replace(":num", "[0-9]*", $routePreg);
				$routePreg = str_replace(":img", ".*[.](png|jpg|jpeg|gif)", $routePreg);


				//  echo "$route==$GET<br/>";
				if ((preg_match("/^" . $routePreg . "$/i", $GET) and $route != $GET) or $route == $GET) {

					self::$get = explode("/", $GET);

					$this->cous++;
					return $this->FileController($route, $class);

				} else {
					return false;
				}
			}
			if (!empty($location)) {
				$this->cous++;
				header("Location: " . $location);
			}

		endif;

	}

	public function setUser($status)
	{
		$this->status = $status;
	}

	public function setSubdomain($subdomain)
	{
		if (class_exists('\OxApp\models\AuthStuff') and \OxApp\models\AuthStuff::getStatus() == true) {
			$this->type = "stuff";
		} else {
			$this->type = "all";
		}
		$this->setHost = $subdomain;
	}


}