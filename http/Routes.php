<?php
/**
 * Created by OxCRM.
 * User: aliaxander
 * Date: 03.06.15
 * Time: 12:49
 */
namespace Ox\controllers;

use Ox\core\App as App;
use Ox\core\route as route;
use Ox\models\Auth;

class Routes extends App
{
    public function __construct()
    {

        $route = new route();

        //Все:
        $route->setUser("all");
        $route->setSubdomain("n/a");
        $route->get("/", "index");
        $route->get("/phpinfo", "", "phpinfo");



        if ($route->cous == 0) {
            header("Location: /login");
        }


    }

}