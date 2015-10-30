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

/**
 * Class Route
 *
 * @package Ox
 */
class Route
{
    public static $get;
    public $cous = 0;
    public $auth = "\Ox\Auth";
    public $setHost = "all";
    public $forUser = "all";
    public $forHostName = "all";
    public $ContentType = "";
    public $debug;
    protected $route;
    protected $class;
    protected $config;

    /**
     * Route constructor.
     *
     * @param bool|false $debug
     */
    public function __construct($debug = false)
    {
        $this->debug = $debug;
        $this->config = new Config();
        $this->auth = new $this->auth;
        if ($this->auth->getStatus() == true) {
            $this->type = $this->auth->GiveMeUserSettings()->status;
        } else {
            $this->type = "all";
        }


    }

    /**
     * @param        $route
     * @param        $class
     * @param string $funcs
     * @param string $location
     *
     * @return bool|void
     */
    public function get($route, $class, $funcs = "", $location = "")
    {
        if (!isset($_GET['q']))
            $_GET['q'] = "/";

        if (!empty($_SERVER['REQUEST_URI'])) {
            $GET = $_SERVER['REQUEST_URI'];
        } else if (!empty($_SERVER['REDIRECT_URL'])) {
            $GET = $_SERVER['REDIRECT_URL'];
        } else {
            $GET = $_GET['q'];
        }

        $check = explode("?", $GET);
        if (isset($check[1])) {
            $GET = $check[0];
        }

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


        $hostName = true;
        if ($this->forHostName != "all" and $this->forHostName != $_SERVER['HTTP_HOST']) {
            $hostName = false;
        }
        if (($this->type == $this->forUser or $this->forUser == "all") and $rootHost == true and $hostName == true):
            if ($this->debug == true)
                echo $this->type . " - " . $route . "<br/>";
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

                $setGetRoutes = explode("/", $route);
                if (!empty($setGetRoutes)) {
                    $getResut = explode("/", $GET);
                    $i = 0;
                    foreach ($setGetRoutes as $rout) {
                        $testRoute = explode("=>", $rout);
                        if (!empty($testRoute[1]) and isset($getResut[$i])) {
                            $SetGet[$testRoute[1]] = $getResut[$i];
                            $route = str_replace("{$testRoute[0]}=>$testRoute[1]", "$testRoute[0]", $route);
                        }
                        $i++;
                    }
                }else{
                    $SetGet="";
                }

                $routePreg = str_replace(":num", "[0-9]*", $route);
                $routePreg = str_replace(":char", "[A-Za-z]*", $routePreg);
                $routePreg = str_replace(":charNum", "[A-Za-z0-9-]*", $routePreg);
                $routePreg = str_replace(":text", "[A-Za-z0-9- .,:;]*", $routePreg);
                $routePreg = str_replace(":img", ".*[.](png|jpg|jpeg|gif)", $routePreg);
                $routePreg = str_replace("/", '\/', $routePreg);
                $routePreg = "/^" . $routePreg . "$/i";
                if ($this->debug == true)
                    echo "$routePreg==$GET<br/>";
                if ((preg_match($routePreg, $GET) and $route != $GET) or $route == $GET) {
                    if(isset($setGet) and !empty($SetGet)){
                    $_GET=$setGet;
                    $_REQUEST=$setGet;
                    }

                    self::$get = explode("/", $GET);

                    $this->cous++;
                    $resultRoute = explode("::", $class);
                    if (!empty($resultRoute[1])) {
                        return $this->FileController($route, $resultRoute[0], $resultRoute[1]);
                    } else {
                        return $this->FileController($route, $class);
                    }

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

    /**
     * @param        $route
     * @param        $class
     * @param string $method
     */
    private function FileController($route, $class, $method = "")
    {

        $file = "../http/controllers/" . $class . "Controller.php";
        $file = str_replace("\\", "/", $file);
        if (is_readable($file) == false) {
            die ($file . ' Controller Not Found');
        } else {
            $this->cous++;
            $class .= "Controller";
            try {
                $class = "\\OxApp\\controllers\\" . $class;

                $controller = new  $class();
                if (is_subclass_of($controller, 'Ox\App')) {
                    if (!empty($this->ContentType))
                        header('Content-Type: ' . $this->ContentType);


                    if (!empty($method)) {
                        try {
                            $controller->$method();
                        } catch (\Exception $e) {
                            // catch( Exception $e ) will give no warning, but will not catch Exception
                            echo "ERROR: $e";
                        }

                    } else {
                        if (!empty($_POST)) {
                            try {
                                $controller->post();
                            } catch (\Exception $e) {
                                // catch( Exception $e ) will give no warning, but will not catch Exception
                                echo "ERROR: $e";
                            }

                        } else {
                            try {
                                $controller->view();
                            } catch (\Exception $e) {
                                // catch( Exception $e ) will give no warning, but will not catch Exception
                                echo "ERROR: $e";
                            }
                        }
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

    /**
     * @param $status
     */
    public function setUser($status)
    {
        $this->forUser = $status;
    }

    /**
     * @param $subdomain
     */
    public function setSubdomain($subdomain)
    {
        if (class_exists('\OxApp\models\AuthStuff') and \OxApp\models\AuthStuff::getStatus() == true) {
            $this->type = "stuff";
        } elseif ($this->auth->getStatus() == true) {
            $this->type = $this->auth->GiveMeUserSettings()->status;
        } else {
            $this->type = "all";
        }
        $this->setHost = $subdomain;
    }


}
