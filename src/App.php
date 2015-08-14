<?php

/**
 * Created by OxProfitGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 14:41
 */

/**
 * Class App
 */
namespace Ox;

class App
    implements IController
{

    public function __construct()
    {
        $this->mysql = new dbMysql();
        $this->CharFix = new CharFix();
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