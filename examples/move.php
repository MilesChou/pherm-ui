<?php

use Amp\Delayed;
use Amp\Loop;
use Illuminate\Container\Container;
use MilesChou\Pherm\Control;
use MilesChou\PhermUI\Events\ViewChange;
use MilesChou\PhermUI\PhermUI;
use Phoole\Event\Dispatcher;
use Phoole\Event\Provider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

include_once __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$container->singleton(Provider::class);
$container->singleton(ListenerProviderInterface::class, Provider::class);
$container->singleton(EventDispatcherInterface::class, Dispatcher::class);

$cui = new PhermUI($container);

$view = $cui->viewFactory()
    ->createView('view1', 10, 10, 40, 5)
    ->setContent('Move');

/** @var Provider $provider */
$provider = $container->get(ListenerProviderInterface::class);
$provider->attach(function (ViewChange $viewChange) use ($cui) {
    $cui->drawer()->draw($viewChange->view);
});

$cui->run();

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
