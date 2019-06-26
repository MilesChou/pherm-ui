<?php

namespace MilesChou\PhermUI\View;

use InvalidArgumentException;
use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\View\Concerns\Configuration;
use MilesChou\PhermUI\View\Concerns\Frame;

class View
{
    use Configuration;
    use Frame;

    /**
     * @var array
     */
    private $buffer = [];

    /**
     * @var string
     */
    private $content = '';

    /**
     * @var Terminal
     */
    private $terminal;

    /**
     * @var string
     */
    private $title = '';

    /**
     * @param int $x
     * @param int $y
     * @param int $sizeX
     * @param int $sizeY
     */
    public function __construct(int $x, int $y, int $sizeX, int $sizeY)
    {
        $this->setPosition($x, $y);
        $this->setSize($sizeX, $sizeY);
        $this->resetBuffer();
    }

    /**
     * @return array
     */
    public function getBuffer(): array
    {
        return $this->buffer;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return bool
     */
    public function hasTitle(): bool
    {
        return $this->title !== '';
    }

    /**
     * @param string $content
     * @return View
     */
    public function setContent(string $content): View
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param string $title
     * @return View
     */
    public function setTitle(string $title): View
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Alias for setTitle()
     *
     * @param string $title
     * @return View
     */
    public function title(string $title): View
    {
        return $this->setTitle($title);
    }

    /**
     * @param int $y
     * @param int $x
     * @param string|null $char
     */
    public function writeBuffer(int $y, int $x, $char): void
    {
        if (!isset($this->buffer[$y][$x])) {
            throw new InvalidArgumentException("Invalid x '$x' or y '$y'");
        }

        $this->buffer[$y][$x] = [$char];
    }

    private function resetBuffer(): void
    {
        [$frameSizeX, $frameSizeY] = $this->frameSize();

        // $buffer[$y] = []
        $this->buffer = array_fill(0, $frameSizeY, null);

        // $buffer[$y][$x] = []
        $this->buffer = array_map(function () use ($frameSizeX) {
            return array_fill(0, $frameSizeX, [' ']);
        }, $this->buffer);
    }
}
