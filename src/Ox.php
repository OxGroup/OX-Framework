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

        $whoops = new Run();
        $whoops->pushHandler(new PrettyPageHandler());

        $whoops->pushHandler(function ($exception, $inspector, $run) {
            $inspector->getFrames()->map(function ($frame) {
                if ($function = $frame->getFunction()) {
                    $frame->addComment("This frame is within function '$function'", 'cpt-obvious');
                }
                return $frame;
            });
        });

        $whoops->register();
        session_start();
        Routes::start();


    }

}
