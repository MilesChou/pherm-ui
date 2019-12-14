<?php

namespace MilesChou\PhermUI\View;

use MilesChou\PhermUI\PhermUI;

class Factory
{
    /**
     * @var PhermUI
     */
    private $phermUI;

    /**
     * @param PhermUI $phermUI
     */
    public function __construct(PhermUI $phermUI)
    {
        $this->phermUI = $phermUI;
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

        $this->phermUI->addView($view);

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

        $this->phermUI->addView($view);

        return $view;
    }
}
