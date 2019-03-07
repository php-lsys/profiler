<?php
namespace LSYS\Profiler;
/**
 * @method \LSYS\Profiler profiler()
 */
class DI extends \LSYS\DI{
    /**
     * @return static
     */
    public static function get(){
        $di=parent::get();
        !isset($di->profiler)&&$di->profiler(new \LSYS\DI\SingletonCallback(function (){
            return (new \LSYS\Profiler())->appInit(LSYS_START_TIME,LSYS_PEAK_MEMORY);
        }));
        return $di;
    }
}