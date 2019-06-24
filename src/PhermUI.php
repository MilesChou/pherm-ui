<?php

namespace MilesChou\PhermUI;

use BadMethodCallException;
use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\View\View;

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
     * @var View[]
     */
    private $views = [];

    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
        $this->terminal->bootstrap();
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this->terminal, $method)) {
            return $this->terminal->{$method}(...$arguments);
        }

        throw new BadMethodCallException("Invalid method name '$method'");
    }

    public function run(): void
    {
        $this->terminal->bootstrap();
        $this->terminal->disableCanonicalMode();
        $this->terminal->clear();

        foreach ($this->views as $view) {
            $view->draw();
        }
    }

    /**
     * @param string $name
     * @param int $x0
     * @param int $y0
     * @param int $x1
     * @param int $y1
     * @return View
     */
    public function createView(string $name, int $x0, int $y0, int $x1, int $y1): View
    {
        $view = new View($this->terminal, $x0, $y0, $x1, $y1);

        $this->views[$name] = $view;

        return $view;
    }
}
