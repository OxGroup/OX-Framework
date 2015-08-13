<?php
/**
 * Created by OxCRM.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 10:39
 */
$proj_global_cache_time = "300";//sec
$debug = true;
if ($debug == true) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}
date_default_timezone_set('Europe/Moscow');
header('Content-type: text/html; charset=utf-8');
