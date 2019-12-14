<?php

use Amp\Delayed;
use Amp\Loop;
use Illuminate\Container\Container;
use MilesChou\Pherm\Control;
use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\PhermUI;

include_once __DIR__ . '/../vendor/autoload.php';

$cui = new PhermUI(new Terminal(new Container()));

$view = $cui->viewFactory()
    ->createView('view1', 10, 10, 40, 5)
    ->setContent('Move');

$view->addFrameChangeCallback(static function ($v) use ($cui) {
    $cui->drawer()->draw($v);
});

$cui->run();

$cui->cursor()->bottom();

$terminal = $cui->getTerminal();

\Amp\asyncCall(static function () use ($cui, $terminal, $view) {
    $drawer = $cui->drawer();
    while (true) {
        $input = $terminal->read(4);

        if ($input === 'q') {
            $terminal->cursor()->bottom();
            return;
        }

        switch ($input) {
            case Control::CONTROL_SEQUENCES['CUU']:
                $drawer->flush($view->clear());

                [$x, $y] = $view->position();
                $view->setPosition($x, $y - 1);
                break;

            case Control::CONTROL_SEQUENCES['CUB']:
                $drawer->flush($view->clear());

                [$x, $y] = $view->position();
                $view->setPosition($x - 1, $y);
                break;

            case Control::CONTROL_SEQUENCES['CUD']:
                $drawer->flush($view->clear());

                [$x, $y] = $view->position();
                $view->setPosition($x, $y + 1);
                break;

            case Control::CONTROL_SEQUENCES['CUF']:
                $drawer->flush($view->clear());

                [$x, $y] = $view->position();
                $view->setPosition($x + 1, $y);
                break;
        }

        $terminal->flush();

        yield new Delayed(1);
    }
});

Loop::run();
