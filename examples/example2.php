<?php

include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/src/App.php';
include __DIR__ . '/src/Curl.php';
include __DIR__ . '/src/Proxy.php';

use Pimple\Container;

$container         = new Container();
$container['name'] = 'Gonzalo';

$container['Curl']  = function () {
    return new Curl();
};
$container['Proxy'] = function ($c) {
    return new Proxy($c['Curl']);
};

$container['App'] = function ($c) {
    return new App($c['Proxy'], $c['name']);
};

$app = $container['App'];
echo $app->hello();






