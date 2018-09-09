<?php
/**
 * lsys core
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Profiler;
use LSYS\Profiler;

class Render{
	protected $_profile;
	protected $_handlers=array();
	public function __construct(Profiler $profile=null){
	    if ($profile===null)$profile=\LSYS\Profiler\DI::get()->profiler();
		$this->_profile=$profile;
	}
	public function pushHandler(Handler $handler){
		$this->_handlers[]=$handler;
		return $this;
	}
	
	protected $_stat_filter=array();
	public function add_stat_filter($group,$name=null){
		$this->_stat_filter[]=array($group,$name);
		return $this;
	}
	
	protected function _in_filter($group,$name=null){
		if ($name===null){
			foreach ($this->_stat_filter as $v){
				if ($v[0]==$group&&$v[1]===null) return true;
			}
		}else{
			foreach ($this->_stat_filter as $v){
				if($v[0]==$group&&$v[1]==$name) return true;
			}
		}
		return false;
	}
    public static function format_time($time) {
        if ($time > 1000 && $time < 60000) {
            return sprintf('%02.2f', $time / 1000).' s';
        }

        if ($time > 60000) {
            return sprintf('%02.2f', $time / 60000).' m';
        }

        return sprintf('%02.2f', $time).' ms';
    }
    public static function format_size($size) {
    	$s=$size>=0?"":"-";
    	$size=abs($size);
        if ($size > 1024 * 1024) {
            return $s.sprintf('%02.2f', $size / (1024 * 1024)).' MB';
        }
        if ($size > 1024) {
            return $s.sprintf('%02.2f', $size / 1024).' KB';
        }
        return $s.$size.' B';
    }
	public function render($handler=null){
		$groups=$this->stats();
		$app_data=$this->_profile->app_total();		
		if ($handler===null){
			$out='';
			foreach ($this->_handlers as $v){
				$out.=$v->render($app_data,$groups);
			}
			return $out;
		}else return $handler->render($app_data,$groups);
	}
	public function name_stats($group,$name){
		$groups=$this->_profile->groups($group,$name);
		return $this->_tokens_stats($groups[$group][$name]);
	}
	public function group_stats($group){
		$groups=$this->_profile->groups($group);
		return $this->_group_tokens_stats($group,$groups[$group]);
	}
	public function stats(){
		$groups=$this->_profile->groups();
		$min_time=$max_time=array(
			'group'=>null,
			'time'=>null,
		);
		$peak_memory=null;
		$total_time=0;
		$stats = array();
		foreach ($groups as $group => $names)
		{
			$stat=$this->_group_tokens_stats($group,$names);
// 			'min'=>$min_time,//平均最小name执行时间
// 			'max'=>$max_time,//平均最大name执行时间
// 			'peak_memory'=>$peak_memory,//峰值内存
// 			'total_time'=>$total_time,//执行总耗时
// 			'stats'=>$stats//每次详细
			if ($peak_memory === NULL OR $stat['peak_memory'] > $peak_memory)
			{
				$peak_memory = $stat['peak_memory'];
			}
			$stats[]=array(
				'group'=>$group,
				'stats'=>$stat
			);
			if ($this->_in_filter($group)) continue;//过滤不参与计算数据
			if ($max_time['time'] === NULL OR $stat['total_time'] > $max_time['time'])
			{
				$max_time['time'] = $stat['total_time'];
				$max_time['group'] = $group;
			}
			if ($min_time['time'] === NULL OR $stat['total_time'] < $min_time['time'])
			{
				$min_time['time'] = $stat['total_time'];
				$min_time['group'] = $group;
			}
			$total_time+=$stat['total_time'];
		}
		return array(
			'min'=>$min_time,//平均最小name执行时间
			'max'=>$max_time,//平均最大name执行时间
			'peak_memory'=>$peak_memory,//峰值内存
			'total_time'=>$total_time,//执行总耗时
			'groups'=>$stats//每次详细
		);
	}
	protected function _group_tokens_stats($group,array $names)
	{
		//最小 最大 总计耗时
		$min_time=$max_time=array(
			'name'=>null,
			'time'=>null,
		);
		$peak_memory=null;
		$total_time=0;
		// All statistics
		$stats = array();
		foreach ($names as $name => $tokens)
		{
				$stat=$this->_tokens_stats($tokens);
// 				'min' => $min,//最小值
// 				'max' => $max,//最大值
// 				'average' => $average,//评价值
// 				'peak_memory' => isset($peak_memory)?$peak_memory:0,//峰值内存
// 				'total_time' => $total_time,//总计耗时
// 				'items'=>$items//每次详细
				if ($peak_memory === NULL OR $stat['peak_memory'] > $peak_memory)
				{
					$peak_memory = $stat['peak_memory'];
				}
				$stats[]=array(
					'name'=>$name,
					'stats'=>$stat
				);
				if ($this->_in_filter($group)||$this->_in_filter($group,$name)){
					continue;
				}
				if ($max_time['time'] === NULL OR $stat['total_time'] > $max_time['time'])
				{
					$max_time['time'] = $stat['total_time'];
					$max_time['name'] = $name;
				}
				if ($min_time['time'] === NULL OR $stat['total_time'] < $min_time['time'])
				{
					$min_time['time'] = $stat['total_time'];
					$min_time['name'] = $name;
				}
				$total_time+=$stat['total_time'];
		}
		
		
		
		return array(
			'min'=>$min_time,//平均最小name执行时间
			'max'=>$max_time,//平均最大name执行时间
			'peak_memory'=>$peak_memory,//峰值内存
			'total_time'=>$total_time,//执行总耗时
			'names'=>$stats//每次详细
		);
	}
	//
	protected function _tokens_stats(array $tokens)
	{
		// Min and max are unknown by default
		$min = $max = array(
			'time' => NULL,
			'memory' => NULL
		);
		// Total values are always integers
		$items=array();
		$total_memory=0;
		$total_time=0;
		foreach ($tokens as $token)
		{
			$items[]=$item=$this->_profile->total($token);
			// 耗时 使用内存 最高值内存
			list($time, $memory,$_peak_memory) =$item;
			if (!isset($peak_memory)||$_peak_memory>$peak_memory) $peak_memory=$_peak_memory;
			if ($max['time'] === NULL OR $time > $max['time'])
			{
				$max['time'] = $time;
			}
			if ($min['time'] === NULL OR $time < $min['time'])
			{
				// Set the minimum time
				$min['time'] = $time;
			}
			// Increase the total time
			$total_time += $time;
			if ($max['memory'] === NULL OR $memory > $max['memory'])
			{
				// Set the maximum memory
				$max['memory'] = $memory;
			}
			if ($min['memory'] === NULL OR $memory < $min['memory'])
			{
				// Set the minimum memory
				$min['memory'] = $memory;
			}
			// Increase the total memory
			$total_memory += $memory;
		}
		// Determine the number of tokens
		$count = count($tokens);
		// Determine the averages
		$average = array(
			'time' => $count>0?$total_time/$count:0,
			'memory' => $count>0?$total_memory/$count:0
		);
		return array(
			'min' => $min,//最小值
			'max' => $max,//最大值
			'average' => $average,//评价值
			'peak_memory' => isset($peak_memory)?$peak_memory:0,//峰值内存
			'total_time' => $total_time,//总计耗时
			'tokens'=>$items//每次详细
		);
	}
}