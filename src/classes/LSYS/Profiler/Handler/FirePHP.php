<?php
/**
 * lsys core
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Profiler\Handler;
use LSYS\Exception;
class FirePHP extends Markdown{
	public function render(array $app_data,array $data){
		if (!class_exists('\FirePHP')) throw new Exception("you need run [ composer require firephp/firephp-core ]");
		$data=$this->_render($app_data, $data);
		$datas=explode("\n", $data);
		$firephp = \FirePHP::getInstance(true);
		foreach ($datas as $v){
			$firephp->fb("//".$v);
		}
	}
}