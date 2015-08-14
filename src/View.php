<?php

/**
 * Created by OxCRM.
 * User: Александр
 * Date: 31.05.2015
 * Time: 21:15
 */
namespace oxCore;
class View
{
    protected static $data = array();

    public static function addKey($key, $val)
    {
        self::$data[$key] = $val;
    }

    private static function addTlp($tpl)
    {
        if (!empty(self::$data)) {
            foreach (self::$data as $key => $val) {
                $$key = $val;
            }
        }
        ob_start();
        $tpl = __DIR__ . "/../views/" . $tpl . ".tpl.php";
        if (file_exists($tpl)) {
            require_once $tpl;

        } else {
            die("View not found " . $tpl);
        }
        $content = ob_get_contents();
        ob_clean();
        return $content;
    }

    public static function build($tpl,$keys=array())
    {
        if(!empty($keys)){
            foreach($keys as $key=>$val){
                self::$data[$key] = $val;
            }
        }
        echo self::addTlp($tpl);
    }
}

