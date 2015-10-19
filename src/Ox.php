<?php
/**
 * Created by OxProfitGroup.
 * User: ���������
 * Date: 11.07.2015
 * Time: 22:26
 */

namespace Ox;


class Ox
{
    public static function start(){

        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);

        $whoops->pushHandler(function ($exception, $inspector, $run) {
            $inspector->getFrames()->map(function ($frame) {
                if ($function = $frame->getFunction()) {
                    $frame->addComment("This frame is within function '$function'", 'cpt-obvious');
                }
                return $frame;
            });
        });

        $whoops->register();


        ini_set("session.cookie_domain",".".Config::$domain);

        session_start();
print_r($_COOKIE);
        new \OxApp\controllers\Routes;

    }

}