<?php

function hello()
{
    return "world";
}

if (!function_exists('response')) {
    function response()
    {
        return App::getContainer()->get('response');
    }
}

function app($name = null)
{
    // 如果选择了具体实例
    if ($name) {
        return App::getContainer()->get($name);
    }
    return App::getContainer();
}

function endView()
{
    $time = microtime(true) - FRAME_START_TIME;
    $memory = memory_get_usage() - FRAME_START_MEMORY;

    echo '<br/><br/><br/><br/><br/><hr/>';
    echo "运行时间: " . round($time * 1000, 2) . 'ms<br/>';
    echo "消耗内存: " . round($memory / 1024 / 1024, 2) . 'm';
}


function config($key = null)
{
    if ($key) {
        return App::getContainer()->get('config')->get($key);
    }
    return App::getContainer()->get('config');
}

function view($path, $params = [])
{
    return App::getContainer()->get(\core\view\ViewInterface::class)->render($path, $params);
}
