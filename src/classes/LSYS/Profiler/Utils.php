<?php
/**
 * lsys core
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Profiler;
use LSYS\Profiler\Handler\Html;
use LSYS\Profiler\Handler\Markdown;
class Utils{
	/**
	 * render profiler data
	 * @param Handler $handler
	 * @return boolean
	 */
	public static function render(Handler $handler=null){
		if ($handler===null){
		    $handler= PHP_SAPI==='cli'?new Markdown():new Html();
		}
		$render=new Render(\LSYS\Profiler\DI::get()->profiler());
		return $render->render($handler);
	}
}

