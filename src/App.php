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