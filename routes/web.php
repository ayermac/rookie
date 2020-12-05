<?php

$router->get('/hello', function () {
    return '你在访问hello';
});

$router->get('/config', function () {
    echo App::getContainer()->get('config')->get('database.connections.mysql_one.driver') . '<hr/>';
    App::getContainer()->get('config')->set('database.connections.mysql_one.driver', 'mysql set');
    echo App::getContainer()->get('config')->get('database.connections.mysql_one.driver');
});

$router->get('/db', function () {
    $id = 1;
    var_export(
        App::getContainer()->get('db')->select('select * from users where id = ?', [$id])
    );
});

$router->get('/query', function () {
    var_export(
        App::getContainer()->get('db')->table('users')->where('id', '=', 1)->get()
    );
});

$router->get('/model', function () {
    $users = \App\User::where('id', '=', 1)->get();
    foreach ($users as $user) {
        print_r($user);
        echo $user->id;
        echo $user->SayHello();
    }
});

$router->get('/controller', 'UserController@index');

$router->get('view/blade', function () {
    $str = '这是blade模板引擎';

    return view('blade.index', compact('str'));
});

$router->get('log/stack', function () {
    App::getContainer()->get('log')->debug('debug');
});
