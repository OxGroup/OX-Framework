<?php
/**
 * Created by oxCRM.
 * User: aliaxander
 * Date: 21.05.15
 * Time: 16:23
 */

/**
 * Class AppConfig
 */
namespace ox\core;
class AppConfig
{
    public $route;

    function __construct()
    {
        global $proj_url, $proj_global_cache_time, $debug;
    }

}