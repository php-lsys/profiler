<?php
defined('LSYS_PEAK_MEMORY')||define('LSYS_PEAK_MEMORY',memory_get_peak_usage());
defined('LSYS_START_TIME')||define('LSYS_START_TIME',microtime(TRUE));
include_once __DIR__."/../vendor/autoload.php";
//开发环境定义