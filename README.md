代码执行性能分析
===

[![Build Status](https://travis-ci.com/lsys/profiler.svg?branch=next_version)](https://travis-ci.com/lsys/profiler)
[![Coverage Status](https://coveralls.io/repos/github/lsys/profiler/badge.svg?branch=next_version)](https://coveralls.io/github/lsys/profiler?branch=next_version)

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