代码执行性能分析
===

[![Build Status](https://travis-ci.com/php-lsys/profiler.svg?branch=master)](https://travis-ci.com/php-lsys/profiler)
[![Coverage Status](https://coveralls.io/repos/github/php-lsys/profiler/badge.svg?branch=master)](https://coveralls.io/github/php-lsys/profiler?branch=master)

> 查看代码执行时间及运行时的内存使用情况

1. 添加分析代码
```
$p=\LSYS\Profiler\DI::get()->profiler();
$token=$p->start("组1","测试1");
//这里写你的代码
isset($token)&&$p->stop($token);
```

2. 输出分析结果,支持控制台输出,HTML输出等.已实现输出方式 参阅:src/classes/LSYS/Profiler/Handler
```
$hander= new Html(true);
$html=Utils::render($hander);
```

使用示例参阅:dome/