<?php

/**
 * Created by oxCRM.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 14:41
 */

/**
 * Class App
 */
namespace Ox;
use Ox\models\Auth;
use Ox\models\ErrorNotif;

class App
    implements IController
{

    public function __construct()
    {
        $this->mysql = new dbMysql();
        $this->charFix = new charFix();
        //$this->jsBuilder = new jsBuilder();
        $this->config = new AppConfig();
        $this->View=new View();
        View::addKey("userNameProfile", Auth::GiveMeUserSettings()->name);
        View::addKey("errorNotif", ErrorNotif::build());

    }

    public function view()
    {

    }
    public function post(){

    }

    public function __destruct()
    {

    }


}