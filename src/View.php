<?php
/**
 * Created by OxGroup.
 * User: Александр
 * Date: 31.05.2015
 * Time: 21:15
 */
namespace Ox;

use Aptoma\Twig\Extension\MarkdownEngine\MichelfMarkdownEngine;
use Aptoma\Twig\Extension\MarkdownExtension;

/**
 * Class View
 *
 * @package Ox
 */
class View
{
    public static $settings = array();
    public static $twig;
    protected static $data = array();
    public static $cache = false;

    /**
     * View constructor.
     *
     * @param bool $cache
     */
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

    /**
     * @param array $array
     */
    public static function setSettings($array = array())
    {
        self::$settings += $array;
    }

    /**
     * @param $key
     * @param $val
     */
    public static function addKey($key, $val)
    {
        self::$data[$key] = $val;
    }

    /**
     * @param       $tpl
     * @param array $keys
     *
     * @throws \Exception
     */
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
        echo self::render($tpl);

    }

    /**
     * @param $tpl
     *
     * @return string
     * @throws \Exception
     */
    protected static function render($tpl)
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../../views');
        self::$twig = new \Twig_Environment($loader, self::$settings);
        self::$twig->addExtension(new \Twig_Extension_Debug());
        self::$twig->addExtension(new \Twig_Extension_Escaper());
        self::$twig->addExtension(new \Twig_Extension_Optimizer());
        self::$twig->addExtension(new \Twig_Extension_StringLoader());
        $engine = new MichelfMarkdownEngine();
        self::$twig->addExtension(new MarkdownExtension($engine));
        try {
            return self::$twig->render($tpl . '.tpl.php', self::$data);
        } catch (\RuntimeException $e) {
            throw new \Exception($e);
        }

    }
}