<?php

namespace MilesChou\PhermUI\View;

use InvalidArgumentException;
use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\View\Concerns\Configuration;
use MilesChou\PhermUI\View\Concerns\Frame;

class SelectView implements ViewInterface
{
    use ViewAwareTrait;

    /**
     * @var array
     */
    private $items = [];

    /**
     * @var array
     */
    private $checked = [];

    public function __construct(int $x, int $y, int $sizeX, int $sizeY, $items = [])
    {
        $this->baseView = new View($x, $y, $sizeX, $sizeY);
        $this->items = $items;
    }

    public function addItem(string $item)
    {
        $this->items[] = $item;

        return $this;
    }

    public function removeItem(int $index)
    {
        unset($this->items[$index]);

        return $this;
    }

    public function getContent(): string
    {
        $content = '';

        foreach ($this->items as $item) {
            $content .= '  ' . $item . "\n";
        }

        return '  ' . trim($content);
    }
}
