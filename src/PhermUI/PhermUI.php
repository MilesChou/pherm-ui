<?php

namespace MilesChou\PhermUI;

use BadMethodCallException;
use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\View\Factory as ViewFactory;
use MilesChou\PhermUI\View\ViewInterface;

/**
 * @mixin Terminal
 */
class PhermUI
{
    /**
     * @var Terminal
     */
    private $terminal;

    /**
     * @var ViewInterface[]
     */
    private $views = [];

    /**
     * @var Drawer
     */
    private $drawer;

    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
        $this->terminal->bootstrap();

        $this->drawer = new Drawer($terminal);
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this->terminal, $method)) {
            return $this->terminal->{$method}(...$arguments);
        }

        throw new BadMethodCallException("Invalid method name '$method'");
    }

    /**
     * @param ViewInterface $view
     */
    public function addView(ViewInterface $view): void
    {
        $this->views[] = $view;
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
        return new ViewFactory($this);
    }
}
