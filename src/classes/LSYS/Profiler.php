<?php
/**
 * lsys core
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS;
class Profiler{
	//array 
	protected $_marks = array();
	//array 
	protected $_mark_app;
	/**
	 * profiler app
	 */
	public function __construct() {
		$this->_mark_app = array
		(
			'start_time'   => microtime(TRUE)*1000,
			'start_peak_memory' => memory_get_peak_usage(),
			// Set the stop keys without values
			'stop_time'    => FALSE,
			'stop_peak_memory' => FALSE,
		);
	}
	/**
	 * init app data
	 * @param float $init_time
	 * @param int $init_peak_memory
	 */
	public function appInit($init_time,$init_peak_memory){
		$this->_mark_app=array_merge($this->_mark_app,array(
			'start_time'   =>$init_time*1000,
			'start_peak_memory' =>$init_peak_memory,
		));
		return $this;
	}
	/**
	 * end app call
	 */
	public function appEnd(){
		$this->_mark_app['stop_time']   = microtime(TRUE)*1000;
		$this->_mark_app['stop_peak_memory'] =  memory_get_peak_usage();
		return $this;
	}
	/**
	 * app profiler data
	 * @return number[]
	 */
	public function appTotal(){
		if ($this->_mark_app['stop_time'] === FALSE)
		{
			$this->appEnd();
		}
		return array
		(
			$this->_mark_app['stop_time'] - $this->_mark_app['start_time'],//总耗时
			$this->_mark_app['stop_peak_memory'] - $this->_mark_app['start_peak_memory'],//运行过程中申请内存
			$this->_mark_app['stop_peak_memory'],//运行过程内存峰值
		);
	}
	/**
	 * start mark of profiler 
	 * @param string $group
	 * @param string $name
	 * @return string
	 */
	public function start($group, $name)
	{
		static $counter = 0;
		$token = base_convert($counter++, 10, 32);
		$this->_marks[$token] = array
		(
				'group' => strtolower($group),
				'name'  => (string) $name,
				// Start the benchmark
				'start_time'   => microtime(TRUE)*1000,
				'start_memory' => memory_get_usage(),
				// Set the stop keys without values
				'stop_time'    => FALSE,
				'stop_memory'  => FALSE,
				'stop_peak_memory' => FALSE,
		);
		return $token;
	}
	/**
	 * end mark of profiler
	 * @param int $token
	 */
	public function stop($token)
	{
		if (!isset($this->_marks[$token]))return false;
		$this->_marks[$token]['stop_time']   = microtime(TRUE)*1000;
		$this->_marks[$token]['stop_memory'] = memory_get_usage();
		$this->_marks[$token]['stop_peak_memory'] = memory_get_peak_usage();
		return true;
	}
	/**
	 * delete mark of profiler
	 * @param int $token
	 */
	public function delete($token)
	{
		unset($this->_marks[$token]);
		return true;
	}
	/**
	 * group mark data
	 * @param string $group
	 * @param string $name
	 * @return array
	 */
	public function groups($group=null,$name=null)
	{
		$groups = array();
		foreach ($this->_marks as $token => $mark)
		{
			if ($group!==null){
				if($group!=$mark['group'])continue;
				if ($name!==null&&$name!=$mark['name'])continue;
			}
			// Sort the tokens by the group and name
			$groups[$mark['group']][$mark['name']][] = $token;
		}
		if (isset($groups[$group][$name]))$groups[$group][$name]=array();
		return $groups;
	}
	public function total($token)
	{
		$mark = $this->_marks[$token];
		if ($mark['stop_time'] === FALSE)
		{
			$mark['stop_time']   = microtime(TRUE)*1000;
			$mark['stop_memory'] = memory_get_usage();
			$mark['stop_peak_memory'] =  memory_get_peak_usage();
		}
		return array
		(
				$mark['stop_time'] - $mark['start_time'],
				$mark['stop_memory'] - $mark['start_memory'],
				$mark['stop_peak_memory'],
		);
	}
}