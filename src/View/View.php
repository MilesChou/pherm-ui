<?php

namespace MilesChou\PhermUI\View;

use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\View\Concerns\Border;

class View
{
    use Border;

    /**
     * @var int
     */
    private $x0;

    /**
     * @var int
     */
    private $x1;

    /**
     * @var int
     */
    private $y0;

    /**
     * @var int
     */
    private $y1;

    /**
     * @var array
     */
    private $buffer = [];

    /**
     * @var bool
     */
    private $instantOutput = false;

    /**
     * @var string
     */
    private $title;

    /**
     * @var Terminal
     */
    private $terminal;

    /**
     * @param Terminal $terminal
     * @param int $x0
     * @param int $y0
     * @param int $x1
     * @param int $y1
     */
    public function __construct(Terminal $terminal, int $x0, int $y0, int $x1, int $y1)
    {
        $this->x0 = $x0;
        $this->y0 = $y0;
        $this->x1 = $x1;
        $this->y1 = $y1;
        $this->terminal = $terminal;

        $this->init();
    }

    public function flush(): void
    {
        foreach ($this->buffer as $y => $columns) {
            foreach ($columns as $x => $cell) {
                if ($cell[0] !== null && $this->isDisplayable($y, $x)) {
                    $this->terminal->writeCursor($this->y0 + $y, $this->x0 + $x, $cell[0]);
                }
            }
        }
    }

    public function draw(): void
    {
        $this->clearFrame();
        $this->drawEdges();
        $this->drawCorners();

        if ($this->title) {
            $this->drawTitle();
        }

        if (!$this->instantOutput) {
            $this->flush();
        }

        $this->terminal->cursor()->move($this->y1, $this->x1 + 1);
    }

    private function clearFrame(): void
    {
        [$maxX, $maxY] = $this->frameSize();

        for ($y = 0; $y < $maxY; $y++) {
            for ($x = 0; $x < $maxX; $x++) {
                $this->write($y, $x, ' ');
            }
        }
    }

    private function drawEdges(): void
    {
        [$sizeY, $sizeX] = $this->size();

        if ($sizeX > 0) {
            foreach (range(1, $sizeX) as $x) {
                $this->write(0, (int)$x, $this->getBorder(0));
                $this->write($sizeY + 1, (int)$x, $this->getBorder(0));
            }
        }

        if ($sizeY > 0) {
            foreach (range(1, $sizeY) as $y) {
                $this->write((int)$y, 0, $this->getBorder(1));
                $this->write((int)$y, $sizeX + 1, $this->getBorder(1));
            }
        }
    }

    private function drawCorners(): void
    {
        [$sizeY, $sizeX] = $this->size();

        $this->write(0, 0, $this->getBorder(2));
        $this->write(0, $sizeX + 1, $this->getBorder(3));
        $this->write($sizeY + 1, 0, $this->getBorder(4));
        $this->write($sizeY + 1, $sizeX + 1, $this->getBorder(5));
    }

    public function frameSize(): array
    {
        return [$this->frameSizeX(), $this->frameSizeY()];
    }

    public function frameSizeX(): int
    {
        return $this->x1 - $this->x0 + 1;
    }

    public function frameSizeY(): int
    {
        return $this->y1 - $this->y0 + 1;
    }

    public function size(): array
    {
        return [$this->sizeY(), $this->sizeX()];
    }

    public function sizeX(): int
    {
        return $this->frameSizeX() - 2;
    }

    public function sizeY(): int
    {
        return $this->frameSizeY() - 2;
    }

    private function drawTitle(): void
    {
        $this->write(0, 2, ' ' . $this->title . ' ');
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

    private function init(): void
    {
        [$sizeX, $sizeY] = $this->frameSize();

        // $buffer[$y] = []
        $this->buffer = array_fill(0, $sizeY, null);

        // $buffer[$y][$x] = []
        $this->buffer = array_map(function () use ($sizeX) {
            return array_fill(0, $sizeX, [' ']);
        }, $this->buffer);
    }

    private function write(int $y, int $x, string $chars)
    {
        foreach ($this->charsToArray($chars) as $i => $char) {
            $this->buffer[$y][$x + $i] = [$char];

            if ($this->instantOutput && $char !== null && $this->isDisplayable($y, $x)) {
                $this->terminal->writeCursor($this->y0 + $y, $this->x0 + $x, $char);
            }
        }
    }

    private function charsToArray($chars)
    {
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
        $y += $this->y0;
        $x += $this->x0;

        if ($y < 0 || $y > $this->terminal->height()) {
            return false;
        }

        if ($x < 0 || $x > $this->terminal->width()) {
            return false;
        }

        return true;
    }
}
