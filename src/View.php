<?php
/**
 * Created by OxProfitGroup.
 * User: Александр
 * Date: 31.05.2015
 * Time: 21:15
 */
namespace Ox;

use Aptoma\Twig\Extension\MarkdownEngine\MichelfMarkdownEngine;
use Aptoma\Twig\Extension\MarkdownExtension;

class View
{
    public static $settings = array();
    public static $twig;
    protected static $data = array();
    public static $cache = false;

    public function __construct($cache = false)
    {
        if ($cache == true) {
            self::$settings = array('cache' => __DIR__ . '/../../../../views/cache');
        } elseif ($cache == false) {
            self::$settings = array('cache' => false);
        } else {
            self::$settings = array('cache' => __DIR__ . '/../../../../' . $cache);
        }

    }

    public static function setSettings($array = array())
    {
        self::$settings += $array;
    }

    public static function addKey($key, $val)
    {
        self::$data[$key] = $val;
    }

    public static function build($tpl, $keys = array())
    {
        //set Settings:
        if (static::$cache == true) {
            self::$settings += array('cache' => __DIR__ . '/../../../../views/cache');
        } elseif (static::$cache == false) {
            self::$settings += array('cache' => false);
        } else {
            self::$settings += array('cache' => __DIR__ . '/../../../../' . static::$cache);
        }

        if (!empty($keys)) {
            foreach ($keys as $key => $val) {
                self::$data[$key] = $val;
            }
        }
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../../views');
        self::$twig = new \Twig_Environment($loader, self::$settings);
        self::$twig->addExtension(new \Twig_Extension_Debug());
        self::$twig->addExtension(new \Twig_Extension_Escaper());
        self::$twig->addExtension(new \Twig_Extension_Optimizer());
        self::$twig->addExtension(new \Twig_Extension_StringLoader());
        $engine = new MichelfMarkdownEngine();
        self::$twig->addExtension(new MarkdownExtension($engine));
        echo self::$twig->render($tpl . '.tpl.php', self::$data);
    }
}

