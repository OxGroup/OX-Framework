<?php
/**
 * Created by OxCRM.
 * User: ���������
 * Date: 11.07.2015
 * Time: 22:26
 */

namespace Ox\core;


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

        $config=new \Ox\core\Config();
        ini_set("session.cookie_domain",".".$config->domain);

        session_start();

        new \Ox\controllers\Routes;

    }

}