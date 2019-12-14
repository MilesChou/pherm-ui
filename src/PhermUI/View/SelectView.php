<?php

namespace MilesChou\PhermUI\View;

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

    /**
     * @inheritDoc
     */
    public function clear(): ViewInterface
    {
        $this->items = [];

        return $this;
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
            $content .= trim($item) . "\n";
        }

        return trim($content);
    }
}
