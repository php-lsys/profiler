<?php
/**
 * lsys profiler
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
//请在你的入口头部定义这两个常量
defined('LSYS_PEAK_MEMORY')||define('LSYS_PEAK_MEMORY',memory_get_peak_usage());
defined('LSYS_START_TIME')||define('LSYS_START_TIME',microtime(TRUE));