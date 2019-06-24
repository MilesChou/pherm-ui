<?php

namespace MilesChou\PhermUI\View;

use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\Support\Char;
use MilesChou\PhermUI\View\Concerns\Configuration;
use MilesChou\PhermUI\View\Concerns\Layout;

class View
{
    use Configuration;
    use Layout;

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
        $this->positionX = $x;
        $this->positionY = $y;
        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;

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
        $this->clearFrame();

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
        [$frameSizeX, $frameSizeY] = $this->frameSize();

        for ($y = 0; $y < $frameSizeY; $y++) {
            for ($x = 0; $x < $frameSizeX; $x++) {
                $this->write($y, $x, ' ');
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

    private function charsToArray($chars)
    {
        if (is_array($chars)) {
            return $chars;
        }

        $arr = [];
        $len = mb_strlen($chars);

        for ($i = 0; $i < $len; $i++) {
            $arr[] = mb_substr($chars, $i, 1);
        }

        return array_reduce($arr, function ($carry, $char) {
            $carry[] = $char;

            // Workaround for Chinese chars
            // See http://www.unicode.org/Public/5.0.0/ucd/Blocks.txt
            $order = mb_ord($char);
            if ($order >= 0x4e00 && $order <= 0x9fff) {
                $carry[] = null;
            }

            return $carry;
        }, []);
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
