<?php

use Illuminate\Container\Container;
use MilesChou\PhermUI\PhermUI;
use Phoole\Event\Dispatcher;
use Phoole\Event\Provider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

include_once __DIR__ . '/../vendor/autoload.php';

$listenerProvider = new Provider();

$container = new Container();
$container->bind(ListenerProviderInterface::class, Provider::class);
$container->bind(EventDispatcherInterface::class, Dispatcher::class);

$cui = new PhermUI($container);

$cui->viewFactory()
    ->createView('view1', 10, 10, 40, 5)
    ->title('Hello');

$cui->viewFactory()
    ->createView('view2', 5, 7, 25, 6)
    ->title('ねこ')
    ->enableBorder()
    ->setContent('world! world! world! world! world! world!');

$cui->viewFactory()
    ->createView('view3', 1, 1, 20, 3)
    ->title('中文')
    ->setContent('Hello 世界 world,Hello 世界 world,Hello 世界 world,Hello 世界 world,');

$cui->run();

sleep(1);
