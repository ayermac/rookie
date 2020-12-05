<?php
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app.php';

App::getContainer()->bind('str', function () {
    return 'hello str';
});

//echo App::getContainer()->get('str');

//echo hello();

App::getContainer()->bind(\core\request\RequestInterface::class, function () {
    return \core\request\Request::create($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $_SERVER);
});

//echo App::getContainer()->get(\core\request\RequestInterface::class)->getMethod();
//echo App::getContainer()->get(\core\request\RequestInterface::class)->getUri();
//var_export(App::getContainer()->get(\core\request\RequestInterface::class)->getHeader());

App::getContainer()->get('response')->setContent(
    App::getContainer()->get('router')->dispatch(
        App::getContainer()->get(\core\request\RequestInterface::class)
    )
)->send();
