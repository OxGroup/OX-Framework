<?php
/**
 * Created by OxProfitGroup.
 * User: ���������
 * Date: 11.07.2015
 * Time: 22:26
 */

namespace Ox;

use OxApp\controllers\Routes;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Ox
{
    public static function start()
    {
        session_start();
        Routes::routes();
    }

}
