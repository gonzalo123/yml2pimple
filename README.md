Pimple/Container builder
======
[![Build Status](https://travis-ci.org/gonzalo123/yml2pimple.svg?branch=master)](https://travis-ci.org/gonzalo123/yml2pimple)

Simple tool build pimple containers from a configuration file


Imagine this simple application:

```php
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
```

We define the dependencies with code. But we want to define dependencies using a yml file for example:

```
parameters:
  name: Gonzalo

services:
  App:
    class:     App
    arguments: [@Proxy, %name%]
  Proxy:
    class:     Proxy
    arguments: [@Curl]
  Curl:
    class:     Curl
```

With this library we can create a pimple container from this yaml file (similar syntax than Symfony's Dependency Injection Container)

```php
use Pimple\Container;
use G\Yaml2Pimple\ContainerBuilder;
use G\Yaml2Pimple\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

$container = new Container();

$builder = new ContainerBuilder($container);
$locator = new FileLocator(__DIR__);
$loader = new YamlFileLoader($builder, $locator);
$loader->load('services.yml');

$app = $container['App'];
echo $app->hello();
```
