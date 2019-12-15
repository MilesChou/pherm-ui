<?php

namespace MilesChou\PhermUI;

use BadMethodCallException;
use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\View\Factory as ViewFactory;
use MilesChou\PhermUI\View\ViewInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * @mixin Terminal
 */
class PhermUI implements EventDispatcherInterface, ListenerProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Drawer
     */
    private $drawer;

    /**
     * @var Terminal
     */
    private $terminal;

    /**
     * @var ViewInterface[]
     */
    private $views = [];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->terminal = new Terminal($container);
        $this->terminal->bootstrap();

        $this->drawer = new Drawer($this->terminal);
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this->terminal, $method)) {
            return $this->terminal->{$method}(...$arguments);
        }

        throw new BadMethodCallException("Invalid method name '$method'");
    }

    /**
     * @param string $name
     * @param ViewInterface $view
     */
    public function addView(string $name, ViewInterface $view): void
    {
        $this->views[$name] = $view;
    }

    /**
     * @return Drawer
     */
    public function drawer(): Drawer
    {
        return $this->drawer;
    }

    /**
     * @return Terminal
     */
    public function getTerminal(): Terminal
    {
        return $this->terminal;
    }

    public function run(): void
    {
        $tty = $this->terminal->control()->tty();
        $tty->disableCanonicalMode();
        $tty->disableEchoBack();

        $this->terminal->disableCursor();
        $this->terminal->clear();

        foreach ($this->views as $view) {
            $this->drawer->draw($view);
        }

        $this->terminal->flush();
    }

    /**
     * @return ViewFactory
     */
    public function viewFactory(): ViewFactory
    {
        return new ViewFactory($this->container, $this);
    }

    /**
     * @inheritDoc
     */
    public function dispatch(object $event)
    {
        // TODO: Implement dispatch() method.
    }

    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        // TODO: Implement getListenersForEvent() method.
    }
}
