<?php

use Illuminate\Container\Container;
use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\PhermUI;

include_once __DIR__ . '/../vendor/autoload.php';

$cui = new PhermUI(new Terminal(new Container()));

$cui->createView('view1', 10, 10, 40, 5)->title('Hello');
$cui->createView('view2', 5, 7, 25,
    6)->title('ねこ')->enableBorder()->setContent('world! world! world! world! world! world!');
$cui->createView('view3', 1, 1, 20,
    3)->title('中文')->setContent('Hello 世界 world,Hello 世界 world,Hello 世界 world,Hello 世界 world,');

$cui->run();

$cui->cursor()->bottom();

sleep(1);
