<?php

namespace MilesChou\PhermUI\View;

use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\Support\Char;
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
    private $title;

    /**
     * @var string
     */
    private $content = '';

    /**
     * @var Terminal
     */
    private $terminal;

    /**
     * @param Terminal $terminal
     * @param int $x
     * @param int $y
     * @param int $sizeX
     * @param int $sizeY
     */
    public function __construct(Terminal $terminal, int $x, int $y, int $sizeX, int $sizeY)
    {
        $this->terminal = $terminal;
        $this->setPosition($x, $y);
        $this->setSize($sizeX, $sizeY);

        $this->resetBuffer();
    }

    public function flush(): void
    {
        foreach ($this->buffer as $y => $columns) {
            foreach ($columns as $x => $cell) {
                if ($cell[0] !== null && $this->isDisplayable($y, $x)) {
                    $this->terminal->writeCursor($this->positionY + $y, $this->positionX + $x, $cell[0]);
                }
            }
        }
    }

    public function draw(): void
    {
        $this->clearContent();

        if ($this->hasBorder()) {
            $this->drawEdges();
            $this->drawCorners();

            if ($this->title) {
                $this->drawTitle();
            }
        }

        $this->drawContent();

        if (!$this->instantRender) {
            $this->flush();
        }
    }

    private function clearFrame(): void
    {
        [$sizeX, $sizeY] = $this->frameSize();

        for ($y = 0; $y < $sizeY; $y++) {
            for ($x = 0; $x < $sizeX; $x++) {
                $this->write($y, $x, ' ');
            }
        }
    }

    private function clearContent(): void
    {
        [$sizeX, $sizeY] = $this->size();

        for ($y = 0; $y < $sizeY; $y++) {
            for ($x = 0; $x < $sizeX; $x++) {
                $this->write($y + 1, $x + 1, ' ');
            }
        }
    }

    private function drawEdges(): void
    {
        [$sizeX, $sizeY] = $this->size();

        if ($sizeX > 0) {
            foreach (range(1, $sizeX) as $x) {
                $this->write(0, (int)$x, $this->getBorderChar(0));
                $this->write($sizeY + 1, (int)$x, $this->getBorderChar(0));
            }
        }

        if ($sizeY > 0) {
            foreach (range(1, $sizeY) as $y) {
                $this->write((int)$y, 0, $this->getBorderChar(1));
                $this->write((int)$y, $sizeX + 1, $this->getBorderChar(1));
            }
        }
    }

    private function drawCorners(): void
    {
        [$sizeX, $sizeY] = $this->size();

        $this->write(0, 0, $this->getBorderChar(2));
        $this->write(0, $sizeX + 1, $this->getBorderChar(3));
        $this->write($sizeY + 1, 0, $this->getBorderChar(4));
        $this->write($sizeY + 1, $sizeX + 1, $this->getBorderChar(5));
    }

    private function drawTitle(): void
    {
        $this->write(0, 1, ' ' . $this->title . ' ');
    }

    private function drawContent(): void
    {
        [$sizeX, $sizeY] = $this->size();

        $chars = Char::charsToArray($this->content);

        $y = 0;

        foreach ($chars as $i => $char) {
            if ($y === $sizeY) {
                break;
            }

            $x = $i % $this->sizeX;

            $this->write($y + (int)$this->border, $x + (int)$this->border, [$char]);

            if ($x === $this->sizeX - 1) {
                ++$y;
            }
        }
    }

    /**
     * @param string $title
     * @return View
     */
    public function title(string $title): View
    {
        $this->title = $title;

        return $this;
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

    /**
     * @param int $y
     * @param int $x
     * @param string|array $chars
     */
    private function write(int $y, int $x, $chars)
    {
        if (is_string($chars)) {
            $chars = Char::charsToArray($chars);
        }

        foreach ($chars as $i => $char) {
            $this->buffer[$y][$x + $i] = [$char];

            if ($this->instantRender && $char !== null && $this->isDisplayable($y, $x)) {
                $this->terminal->writeCursor($this->positionY + $y, $this->positionX + $x + $i, $char);
            }
        }
    }

    /**
     * @param int $y Relative position Y in view
     * @param int $x Relative position X in view
     * @return bool
     */
    private function isDisplayable(int $y, int $x): bool
    {
        $y += $this->positionY;
        $x += $this->positionX;

        if ($y < 0 || $y > $this->terminal->height()) {
            return false;
        }

        if ($x < 0 || $x > $this->terminal->width()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return static
     */
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }
}
