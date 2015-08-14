<?php

/**
 * Created by OxCRM.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 14:34
 */

/**
 * Class route
 */
namespace Ox\core;

use Ox\models\Auth;

class route
{
    public $route;
    public $class;
    public $cous = 0;
    public $setHost;
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
                $class = "\\Ox\\controllers\\" . $class;

                $controller = new  $class();
                if (is_subclass_of($controller, 'Ox\core\App')) {

                    if (!empty($_POST)) {
                        $controller->post();
                    } else {
                        $controller->view();
                    }
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
        if (!isset($_GET['q'])) $_GET['q'] = "/";
        $GET = $_GET['q'];
        if (substr($GET, -1) != "/") $GET .= "/";
        if ($GET{0} != "/") $GET = "/" . $GET;

        if (substr($route, -1) != "/") $route .= "/";
        if ($route{0} != "/") $route = "/" . $route;

        if ($this->setHost != "n/a" and $this->setHost != "sub" and $this->setHost != "all") {
            $GET = $this->config->subDomain() . $GET;
            $route = $this->setHost . $route;
        }

        $rootHost = true;
        if ($this->setHost == "sub") {
            if($this->config->subDomain()==""){
                $rootHost = false;
            }
        } else{
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
                        $object = "\\Ox\\models\\" . $func[0];
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

                    self::$get= explode("/", $GET);

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
        $this->setHost = $subdomain;
    }


}