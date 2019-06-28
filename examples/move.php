<?php

use MilesChou\Pherm\Control;

include_once __DIR__ . '/../vendor/autoload.php';

$cui = (new \MilesChou\PhermUI\Builder())->build();

$view = $cui->createView('view1', 10, 10, 40, 5)->setContent('Move');
$view->addFrameChangeCallback(function ($v) use ($cui) {
    $cui->getDrawer()->draw($v);
});

$cui->run();

$cui->cursor()->bottom();

$terminal = $cui->getTerminal();

$key = $terminal->keyBinding();

$key->set(Control::CONTROL_SEQUENCES['CUU'], function () use ($view) {
    [$x, $y] = $view->position();

    $view->setPosition($x, $y - 1);
});

$key->set(Control::CONTROL_SEQUENCES['CUB'], function () use ($view) {
    [$x, $y] = $view->position();

    $view->setPosition($x - 1, $y);
});

$key->set(Control::CONTROL_SEQUENCES['CUD'], function () use ($view) {
    [$x, $y] = $view->position();

    $view->setPosition($x, $y + 1);
});

$key->set(Control::CONTROL_SEQUENCES['CUF'], function () use ($view) {
    [$x, $y] = $view->position();

    $view->setPosition($x + 1, $y);
});

$terminal->disableCanonicalMode();
$terminal->disableEchoBack();
$terminal->disableCursor();

while (true) {
    $input = $terminal->read(4);

    if ($input === 'q') {
        break;
    }

    $key->handle($input);
}
