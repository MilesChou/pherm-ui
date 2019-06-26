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
     * @param int $x
     * @param int $y
     * @param int $sizeX
     * @param int $sizeY
     * @return View
     */
    public function createView(string $name, int $x, int $y, int $sizeX, int $sizeY): View
    {
        $view = new View($this->terminal, $x, $y, $sizeX, $sizeY);

        $this->views[$name] = $view;

        return $view;
    }
}
