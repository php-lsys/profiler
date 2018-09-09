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
class Html implements Handler{
	protected $_return;
	public function __construct($return=false){
		$this->_return=$return;
	}
	public function render(array $app_data,array $data){
		if (!$this->_return) $this->_render($app_data, $data);
		else {
			ob_start();
			$this->_render($app_data, $data);
			$d=ob_get_contents();
			ob_end_clean();
			return $d;
		}
	}
	protected function _render(array $app_data,array $data){
		list($app_time,$app_memory,$app_peak_memory)=$app_data;
		$h3="<b>分析:</b>
		  	<span>总耗时:".Render::format_time($app_time)." </span>
		    <span>内存申请:".Render::format_size($app_memory)." </span>
		    <span>内存峰值:".Render::format_size($app_peak_memory)." </span>
		    ";
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
					$token="<span class='mark_token'>耗时:".Render::format_time($run_time);
					if ($memory>0)$token.=",增加内存:".Render::format_size($memory)."</span>";
					else $token.=",释放内存:".Render::format_size(-$memory)."</span>";;
					$tokens[]=$token;
				}
				$token_str="";
				$len=count($tokens);
				if ($len>1){
					$token_str=implode("", $tokens);
					$token_str="
						<div class='mark_tokens'>
							<div class='mark_totit'>执行次数:{$len}次</div>
							{$token_str}
						</div>
					";
					
				}
				
				if ($len>1){
					$token_info="
						内存 [
							峰值:".Render::format_size($v1['stats']['peak_memory'])."
							平均:".Render::format_size($v1['stats']['average']['memory'])."
							最大:".Render::format_size($v1['stats']['max']['memory'])."
							最小:".Render::format_size($v1['stats']['min']['memory'])."
							]
						耗时 [
							 总计:".Render::format_time($v1['stats']['total_time'])." 
					 		 平均:".Render::format_time($v1['stats']['average']['time'])."
							 最小:".Render::format_time($v1['stats']['min']['time'])."
							 最大:".Render::format_time($v1['stats']['max']['time'])."
							 ]
					";
				}else{
					$token_info="
						内存峰值:".Render::format_size($v1['stats']['peak_memory'])."
						耗时:".Render::format_time($v1['stats']['total_time'])."
						内存:".Render::format_size($v1['stats']['average']['memory'])."
					";
				}
				$v1['name']=htmlspecialchars($v1['name']);
				$item="<li>
							<div class='makr_item'>
								<div class='mark_name'>{$v1['name']}</div>
								<div class='mark_info'>{$token_info}</div>
							</div>
							{$token_str}
						</li>
					";
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
				$item_str="
					<div class='mark_list'>
					 	<ul class='mark_names'>
				          {$item_str}
				        </ul>
					</div>
				";
			}
			$high=$low="";
			if($data['max']['group']==$v['group']) $high="mark_item_high";
			else if($data['min']['group']==$v['group']) $low="mark_item_low";
			$v['group']=ucwords($v['group']);
			$v['group']=htmlspecialchars($v['group']);
			$mark="
					<div class='mark_item  {$low} {$high}'>	
						<h5 class='mark_tit'>
						  	{$v['group']}
						</h5>
						{$item_str}
				   		<div class='mark_foot'>
				   			此组内存峰值:".Render::format_size($v['stats']['peak_memory'])." 
				   			".($v['stats']['total_time']==0?'':'此组耗时:'.Render::format_time($v['stats']['total_time']))."
				   		</div>
			   		</div>
				 ";
			$marks[]=$mark;
		}
		
		$marks_items=implode("", $marks);
		
		$marks_info='';
		if (count($marks)>0){
			$marks_info="
				<div class='marks'>
					<div class='mark_items'>{$marks_items}</div>
		  			<div class='marks_right'>
		  			总组内存峰值:".Render::format_size($data['peak_memory']).
					",总组耗时:".Render::format_time($data['total_time'])."
					</div>
				</div>
			";
		}
		echo self::$css;
		echo <<<HTML
		<div class='profiler_box'>
		 <h3>{$h3}</h3> 
	  	 {$marks_info}
		 </div>
HTML;
	}
	protected static  $css = <<<CSS
		<style>
		.profiler_box {
		    border: 1px solid #a6c2ff;
		    color: #5f789e;
		    font-size: 13px;
			clear: both;
		}
		.profiler_box h3 {
		    margin: 0;
		    background: #c7d0ff;
		    padding: 5px;
		    font-size: 15px;
		    color: #369;
		    line-height: 30px;
		    font-weight: 100;
		}
	
		.profiler_box h5 {
		    margin: 0;
		    padding-left: 10px;
		    line-height: 32px;
		    background: #dae0fd;
		    font-size: 14px;
		}
		.profiler_box .mark_item_high h5 {
		    background: #fddbdb;
		}
		.profiler_box .mark_item_low h5 {
		    background: #c6ffcc;
		}
	
		.profiler_box .mark_items {
		    margin: 7px;
		    border-radius: 5px;
		    margin-bottom: 0;
		}
	
		.profiler_box .marks_right {
		    text-align: right;
		    font-size: 12px;
		    line-height: 28px;
		    background: #dce2fd;
			padding-right: 17px;
		}
	
		.profiler_box .mark_names {
		    list-style: none;
		    margin: 10px;
		    padding: 0;
		    margin-bottom: 0;
		}
	
		.profiler_box .mark_foot {
		    border-top: 1px solid #dae0fd;
		    font-size: 13px;
		    text-align: right;
		    margin-top: 10px;
		    line-height: 35px;
		    padding-right: 10px;
		}
	
	
		.profiler_box .mark_item {
		    background: #ededf9;
		    margin-bottom: 5px;
		}
	
		.profiler_box .mark_item_high {
		    background: #ffeeee;
			border: 1px solid #fbdede;
		}
		.profiler_box .mark_item_low {
		   background: #e4ffe7;
			    border: 1px solid #d4ffc1;
		}	
			
		.profiler_box li {
		    border: 1px dotted #c8d0f7;
		    margin-bottom: 10px;
		    padding: 5px;
		}
		
		
		
		
		.profiler_box .mark_name {
		    line-height: 28px;
		    text-indent: 5px;
		    color: #999;
		    border-bottom: 1px dashed #ddf;
			word-break:break-all;
　　			word-wrap:break-word;
		}
	
		.profiler_box .mark_info {
		    line-height: 34px;
		    background: #f4f4f7;
		    text-indent: 5px;
		    margin: 5px 0;
		    color: #4d89ca;
		}
	
		.profiler_box .mark_token,.profiler_box .mark_totit {
		    background: #ffb566;
		    color: #fff;
		    padding: 5px;
		    display: block;
		    float: left;
			 margin-right: 5px;
		    border-radius: 5px;
		    margin-bottom: 5px;
		}
	
		.profiler_box .mark_tokens {
		    overflow: hidden;
		}
	
		.profiler_box .mark_token {
		    float: left;
		    background: #3496ff;
		    color: #fff;
		    padding: 5px;
		 
		}
	
	
		
	</style>
CSS;
}