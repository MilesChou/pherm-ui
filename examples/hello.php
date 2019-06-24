<?php

include_once __DIR__ . '/../vendor/autoload.php';

$cui = (new \MilesChou\PhermUI\Builder())->build();

$cui->createView('view1', 10, 10, 40, 5)->title('Hello');
$cui->createView('view2', 5, 7, 25, 6)->title('ねこ');
$cui->createView('view3', 1, 1, 20, 3)->title('中文')->setContent('Hello 世界 world,Hello 世界 world,Hello 世界 world,Hello 世界 world,');

$cui->run();

$cui->move()->bottom();

sleep(1);
