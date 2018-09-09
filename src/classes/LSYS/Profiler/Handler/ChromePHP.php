<?php
/**
 * lsys core
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Profiler\Handler;
use LSYS\Exception;
class ChromePHP extends Markdown{
	public function render(array $app_data,array $data){
		if (!class_exists('\ChromePhp')) throw new Exception("you need run [ composer require ccampbell/chromephp ]");
		\ChromePhp::log($this->_render($app_data, $data));
	}
}