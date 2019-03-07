<?php
/**
 * lsys core
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Profiler\Handler;
use LSYS\Profiler\Handler;
use LSYS\Profiler\Render;
class Markdown implements Handler{
//#### `Run Time:`:126.86 ms `Memory Request:`2.99 MB `Memory Peak:` 3.20 MB
//-----------------------------------------------------------------------------
//+ 数据库 [`Memory Peak :`3.20 MB `Time:`97.94 ms]
//
//	- 运行名称
//	> `Memory Peak :`3.20 MB  
//	> `Time:` 97.94 ms 
//	> `+Memory :`2.15 MB 
//	> *  *`Run Total:`* 1 
//	> * `Time:` 97.94 ms  `+Memory :` 2.15 MB
//	> * `Time:` 97.94 ms  `-Memory :` 2.15 MB
//	
//	- 运行名称
//	> `Memory Peak :`3.20 MB  `Time:` 97.94 ms `+Memory :`2.15 MB
//	> * `Time:` 97.94 ms  `+Memory :` 2.15 MB
//	> * `Time:` 97.94 ms  `-Memory :` 2.15 MB
//
//> **Summary:** `Memory Peak:`:3.20 MB,`Total Time:`97.94 ms
	public function render(array $app_data,array $data){
		echo $this->_render($app_data, $data);
	}
	protected function _render(array $app_data,array $data){
		list($app_time,$app_memory,$app_peak_memory)=$app_data;
		$h3="#### `Run Time:`:".Render::formatTime($app_time).
			" `Memory Request:`".Render::formatSize($app_memory).
			" `Memory Peak:`".Render::formatSize($app_peak_memory).
			"\n-----------------------------------------------------------------------------\n";
		$marks=array();
		// $data;
		// 'min'=>$min_time,//平均最小name执行时间
		// 'max'=>$max_time,//平均最大name执行时间
		// 'peak_memory'=>$peak_memory,//峰值内存
		// 'total_time'=>$total_time,//执行总耗时
		// 'groups'=>$stats//每次详细
		foreach ($data['groups'] as $v){
			// $v
			// 'group'=>$group,
			// 'stats'=>$stat
			
			// $v['stats'];
			// 'min'=>$min_time,//平均最小name执行时间 
			// 'max'=>$max_time,//平均最大name执行时间
			// 'peak_memory'=>$peak_memory,//峰值内存
			// 'total_time'=>$total_time,//执行总耗时
			// 'names'=>$stats//每次详细
			$items=array();
			foreach ($v['stats']['names'] as $v1){
				// $v1;
				// 'name'=>$name,
				// 'stats'=>$stat
				
				// $v1['stats']
				// 'min' => $min,//最小值 'time' 'memory'
				// 'max' => $max,//最大值 'time' 'memory'
				// 'average' => $average,//评价值 'time' 'memory'
				// 'peak_memory' => isset($peak_memory)?$peak_memory:0,//峰值内存
				// 'total_time' => $total_time,//总计耗时
				// 'tokens'=>$items//每次详细
				$tokens=array();
				foreach ($v1['stats']['tokens'] as $v2){
					// $v2;
					// $mark['stop_time'] - $mark['start_time'],
					// $mark['stop_memory'] - $mark['start_memory'],
					// $mark['stop_peak_memory'],
					list($run_time,$memory)=$v2;
					//	> *  *`Run Total:`* 1 
					//	> * `Time:` 97.94 ms  `+Memory :` 2.15 MB
					$token="\t> * `Time:`".Render::formatTime($run_time);
					if ($memory>0)$token.=" `+Memory :`".Render::formatSize($memory)."\n";
					else $token.=" `-Memory :`".Render::formatSize(-$memory)."\n";;
					$tokens[]=$token;
				}
				
				$len=count($tokens);
				$token_str="";
				if ($len>1){
					$token_str=implode("", $tokens);
					$token_str="\t> `Run Total:` {$len}\n{$token_str}";
// 					>  `Memory`  *`Min:`* 3.20 MB  *`Max:`* 3.20 MB
// 					> `Time:` *`Min:`* 3.20 MB  *`Max:`* 3.20 MB
// 					> `Run Total:` 1
// 					> * `Time:` 97.94 ms  `+Memory :` 2.15 MB
// 					> * `Time:` 97.94 ms  `-Memory :` 2.15 MB
					$token_info="\t> `Memory` *`Peak:`* ".Render::formatSize($v1['stats']['peak_memory']).
								" *`Average:`* ".Render::formatSize($v1['stats']['average']['memory']).
								" *`Min:`* ".Render::formatSize($v1['stats']['min']['memory']).
								" *`Max:`* ".Render::formatSize($v1['stats']['max']['memory'])."\n".
								"\t> `Time` *`Total:`* ".Render::formatTime($v1['stats']['total_time']).
								" *`Average:`* ".Render::formatTime($v1['stats']['average']['time']).
								" *`Min:`* ".Render::formatTime($v1['stats']['min']['time']).
								" *`Max:`* ".Render::formatTime($v1['stats']['max']['time'])."\n"
					;
				}else{
					//	- 运行名称
					//	> `Memory Peak :`3.20 MB
					//	> `Time:` 97.94 ms
					//	> `+Memory :`2.15 MB
					$token_info="\t> `Memory Peak :` ".Render::formatSize($v1['stats']['peak_memory'])."\n".
						"\t> `Time:`".Render::formatTime($v1['stats']['total_time'])."\n".
						"\t> `+Memory :`".Render::formatSize($v1['stats']['average']['memory'])."\n";
				}
				//	- 运行名称
				$v1['name']=str_replace("\n", " ", $v1['name']);
				$item="\n\t- {$v1['name']}\n".$token_info.$token_str;
				$items[]=$item;
			}
			
			
			// $v
			// 'group'=>$group,
			// 'stats'=>$stat
				
			// $v['stats'];
			// 'min'=>$min_time,//平均最小name执行时间  'name' 'time'
			// 'max'=>$max_time,//平均最大name执行时间 'name' 'time'
			// 'peak_memory'=>$peak_memory,//峰值内存
			// 'total_time'=>$total_time,//执行总耗时
			// 'names'=>$stats//每次详细
			
			$item_str="";
			$len=count($items);
			if ($len>0){
				$item_str=implode("", $items);
			}
			$high=$low="";
			if($data['max']['group']==$v['group']) $high="`Max` ";
			else if($data['min']['group']==$v['group']) $low="`Min` ";
			$v['group']=str_replace("\n", " ", $v['group']);
			$mark="+ {$high}{$low}{$v['group']} [`Memory Peak :`".Render::formatSize($v['stats']['peak_memory']).
					" `Time:`".Render::formatTime($v['stats']['total_time'])."]\n".$item_str;
			$marks[]=$mark;
		}
		
		$marks_items=implode("\n\n", $marks);
		
		$marks_info='';
		if (count($marks)>0){
			$marks_info=$marks_items."\n> **PS:** `Memory Peak:`:".Render::formatSize($data['peak_memory']).
					" `Total Time:` ".Render::formatTime($data['total_time'])."\n";
		}
		
		return PHP_SAPI==='cli'?$h3.$marks_info:rtrim($h3.$marks_info);
	}
}