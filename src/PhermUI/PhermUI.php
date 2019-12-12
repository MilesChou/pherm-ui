<?php

namespace MilesChou\PhermUI;

use BadMethodCallException;
use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\View\SelectView;
use MilesChou\PhermUI\View\View;
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
     * @param string $name
     * @param int $x
     * @param int $y
     * @param int $sizeX
     * @param int $sizeY
     * @return View
     */
    public function createView(string $name, int $x, int $y, int $sizeX, int $sizeY): View
    {
        $view = new View($x, $y, $sizeX, $sizeY);

        $this->views[$name] = $view;

        return $view;
    }

    /**
     * @param string $name
     * @param int $x
     * @param int $y
     * @param int $sizeX
     * @param int $sizeY
     * @param array $items
     * @return SelectView
     */
    public function createSelectView(string $name, int $x, int $y, int $sizeX, int $sizeY, $items = []): SelectView
    {
        $view = new SelectView($x, $y, $sizeX, $sizeY, $items);

        $this->views[$name] = $view;

        return $view;
    }

    /**
     * @return Drawer
     */
    public function getDrawer(): Drawer
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
        $this->terminal->enableInstantOutput();
        $this->terminal->clear();

        foreach ($this->views as $view) {
            $this->drawer->draw($view);
        }
    }
}
