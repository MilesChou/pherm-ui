<?php

use Illuminate\Container\Container;
use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\PhermUI;

include_once __DIR__ . '/../vendor/autoload.php';

$cui = new PhermUI(new Terminal(new Container()));

$cui->createSelectView('view1', 10, 10, 40, 5)
    ->addItem('Hello')
    ->addItem('World')
    ->addItem('中文')
    ->addItem('Item 4')
    ->addItem('Item 5')
    ->addItem('Item 6');

$cui->run();

$cui->cursor()->bottom();

sleep(1);
