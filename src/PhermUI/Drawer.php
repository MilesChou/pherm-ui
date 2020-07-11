<?php

namespace MilesChou\PhermUI;

use MilesChou\Pherm\Support\Char;
use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\View\ViewInterface;

class Drawer
{
    /**
     * @var Terminal
     */
    private $terminal;

    /**
     * @param Terminal $terminal
     */
    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }

    /**
     * @param ViewInterface $view
     */
    public function draw(ViewInterface $view): void
    {
        if ($view->hasBorder()) {
            $this->drawEdges($view);
            $this->drawCorners($view);

            if ($view->hasTitle()) {
                $this->drawTitle($view);
            }
        }

        $this->clearContent($view);
        $this->drawContent($view);

        $this->flush($view);
    }

    public function flush(ViewInterface $view): void
    {
        [$positionX, $positionY] = $view->position();

        foreach ($view->getBuffer() as $y => $columns) {
            foreach ($columns as $x => $cell) {
                if ($cell[0] !== null && $this->isDisplayable($view, $x, $y)) {
                    $this->terminal->moveCursor($positionX + $x, $positionY + $y)->write($cell[0]);
                }
            }
        }
    }

    /**
     * @param ViewInterface $view
     */
    public function clearFrame(ViewInterface $view): void
    {
        [$sizeX, $sizeY] = $view->frameSize();

        for ($y = 0; $y < $sizeY; $y++) {
            for ($x = 0; $x < $sizeX; $x++) {
                $this->write($view, $x, $y, ' ');
            }
        }
    }

    /**
     * @param ViewInterface $view
     */
    private function clearContent(ViewInterface $view): void
    {
        [$sizeX, $sizeY] = $view->size();

        for ($y = 0; $y < $sizeY; $y++) {
            for ($x = 0; $x < $sizeX; $x++) {
                $view->write($x + 1, $y + 1, ' ');
            }
        }
    }

    private function drawEdges(ViewInterface $view): void
    {
        [$sizeX, $sizeY] = $view->size();

        if ($sizeX > 0) {
            foreach (range(1, $sizeX) as $x) {
                $view->write((int)$x, 0, $view->getBorderChar(0));
                $view->write((int)$x, $sizeY + 1, $view->getBorderChar(0));
            }
        }

        if ($sizeY > 0) {
            foreach (range(1, $sizeY) as $y) {
                $view->write(0, (int)$y, $view->getBorderChar(1));
                $view->write($sizeX + 1, (int)$y, $view->getBorderChar(1));
            }
        }
    }

    /**
     * @param ViewInterface $view
     */
    private function drawCorners(ViewInterface $view): void
    {
        [$sizeX, $sizeY] = $view->size();

        $view->write(0, 0, $view->getBorderChar(2));
        $view->write($sizeX + 1, 0, $view->getBorderChar(3));
        $view->write(0, $sizeY + 1, $view->getBorderChar(4));
        $view->write($sizeX + 1, $sizeY + 1, $view->getBorderChar(5));
    }

    private function drawTitle(ViewInterface $view): void
    {
        $this->write($view, 1, 0, ' ' . $view->getTitle() . ' ');
    }

    private function drawContent(ViewInterface $view): void
    {
        [$sizeX, $sizeY] = $view->size();

        $lines = explode("\n", $view->getContent());

        $y = 0;

        foreach ($lines as $line) {
            $chars = Char::charsToArray($line);

            foreach ($chars as $i => $char) {
                if ($y === $sizeY) {
                    break;
                }

                $x = $i % $sizeX;

                $view->write($x + (int)$view->hasBorder(), $y + (int)$view->hasBorder(), $char);

                if ($x === $sizeX - 1) {
                    ++$y;
                }
            }

            ++$y;
        }
    }

    /**
     * @param ViewInterface $view
     * @param int $y
     * @param int $x
     * @param string|array<string> $chars
     */
    private function write(ViewInterface $view, int $x, int $y, $chars): void
    {
        [$positionX, $positionY] = $view->position();

        if (is_string($chars)) {
            $chars = Char::charsToArray($chars);
        }

        foreach ($chars as $i => $char) {
            $view->write($x + $i, $y, $char);

            if ($char !== null && $this->isDisplayable($view, $x, $y)) {
                $this->terminal->moveCursor((int)$positionX + $x + $i, $positionY + $y)->write($char);
            }
        }
    }

    /**
     * @param ViewInterface $view
     * @param int $y Relative position Y in view
     * @param int $x Relative position X in view
     * @return bool
     */
    private function isDisplayable(ViewInterface $view, int $x, int $y): bool
    {
        [$positionX, $positionY] = $view->position();

        $x += $positionX;
        $y += $positionY;

        if ($x < 0 || $x > $this->terminal->width()) {
            return false;
        }

        if ($y < 0 || $y > $this->terminal->height()) {
            return false;
        }

        return true;
    }
}
