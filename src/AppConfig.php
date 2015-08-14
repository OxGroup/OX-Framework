<?php
/**
 * Created by OxProfitGroup.
 * User: aliaxander
 * Date: 21.05.15
 * Time: 16:23
 */

/**
 * Class AppConfig
 */
namespace Ox;
class AppConfig
{
    public $route;

    function __construct()
    {
        global $proj_url, $proj_global_cache_time, $debug;
    }

}