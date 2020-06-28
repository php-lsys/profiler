<?php
/**
 * lsys core
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Profiler;
interface Handler{
    /**
     * 渲染
     * @param array $app_data
     * @param array $data
     * @return string|void
     */
	public function render(array $app_data,array $data);
}