<?php

namespace MilesChou\PhermUI\View;

use MilesChou\PhermUI\PhermUI;
use Psr\Container\ContainerInterface;

class Factory
{
    /**
     * @var PhermUI
     */
    private $phermUI;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     * @param PhermUI $phermUI
     */
    public function __construct(ContainerInterface $container, PhermUI $phermUI)
    {
        $this->container = $container;
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
        $view = new View($this->container);
        $view->setPosition($x, $y);
        $view->setSize($sizeX, $sizeY);
        $view->clear();

        $this->phermUI->addView($name, $view);

        return $view;
    }
}
