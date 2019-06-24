<?php

include_once __DIR__ . '/../vendor/autoload.php';

$cui = (new \MilesChou\PhermUI\Builder())->build();

$cui->createView('view1', 10, 10, 50, 15)->title('Hello');
$cui->createView('view2', 5, 7, 30, 13)->title('ねこ');
$cui->createView('view3', 1, 1, 20, 3)->title('中文')->useAsciiBorder();

$cui->run();

$cui->move()->bottom();

sleep(1);
