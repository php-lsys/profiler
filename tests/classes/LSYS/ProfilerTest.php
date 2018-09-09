<?php
namespace LSYS;
use PHPUnit\Framework\TestCase;
use LSYS\Profiler\Render;
use LSYS\Profiler\Handler\Html;
final class ProfilerTest extends TestCase
{
    public function testProfiler()
    {
        $p=\LSYS\Profiler\DI::get()->profiler();
        $token=$p->start("a","b");
        for($i=0;$i<1000;$i++){}
        $p->stop($token);
        $this->assertTrue(count($p->app_total())==3);
        $render=new Render($p);
        $data=$render->stats();
        $this->assertArrayHasKey('min', $data);
        $this->assertArrayHasKey('max', $data);
        $this->assertArrayHasKey('groups', $data);
        $this->assertArrayHasKey('peak_memory', $data);
        $this->assertArrayHasKey('total_time', $data);
        $this->assertTrue(count($data['groups'])==1);
        $html=$render->render(new Html(true));
        $this->assertNotEmpty($html);
    }
}