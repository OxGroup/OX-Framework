<?php

/**
 * Created by OxCRM.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 14:46
 */
namespace Ox\controllers;
use Ox\core\App as App;
use Ox\core\Config;
use Ox\core\View;
use Ox\models\Auth;
use Symfony\Component\Filesystem\Filesystem;

class index_controller extends App
{

    public function view()
    {

        echo"<pre>";
        print_r($_REQUEST);
        print_r($_SERVER);


	   //$view=new \Ox\core\View;
	    //$view->addKey("key","Значение");


//View::build("showcase/offers");

	    //Добавление новостиRoute /user/add:
	    //$view->build("advert/test");

	    //Добавление новостиRoute /user/add:
	    //$view->build("advert/test");

	    //Добавление новостиRoute /user/add:
	    //$view->build("advert/stuff-edit");


    }

    public function post()
    {
        echo "<pre>";
        print_r($_FILES);

    }
}