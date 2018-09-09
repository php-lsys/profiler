<?php
use LSYS\Profiler\Utils;
use LSYS\Profiler\Handler\Html;
include __DIR__."/Bootstarp.php";
//-------------------手动性能分析-------------------------
$p=\LSYS\Profiler\DI::get()->profiler();
$token=$p->start("组1","测试1");
//your code
sleep(1);
//your code
isset($token)&&$p->stop($token);


//如果AJAX请求,可以使用控制台输出,不影响程序运行 支持: ChromePHP FirePHP 
// $hander=new LSYS\Profiler\Handler\ChromePHP();
// Utils::render($hander);
//渲染输出分析结果


//如果你希望放到页面指定位置,参考如下
//<!--@PROFILER@--> 是 
$hander= new Html(true);
$html=Utils::render($hander);
?>
<html>
	<head>
		<title>test page</title>
	</head>
	<body>
		<h1>测试页面</h1>
		<p>测试页面</p>
		<?php echo $html;?>
	</body>
</html>
