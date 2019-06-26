<?php

namespace MilesChou\PhermUI;

use MilesChou\Pherm\Terminal;
use MilesChou\Pherm\Support\Char;
use MilesChou\PhermUI\View\View;

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

    public function draw(View $view): void
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

        if (!$view->isInstantRender()) {
            $this->flush($view);
        }
    }

    public function flush(View $view): void
    {
        [$positionX, $positionY] = $view->position();

        foreach ($view->getBuffer() as $y => $columns) {
            foreach ($columns as $x => $cell) {
                if ($cell[0] !== null && $this->isDisplayable($view, $y, $x)) {
                    $this->terminal->moveCursor($positionX + $x, $positionY + $y)->write($cell[0]);
                }
            }
        }
    }

    /**
     * @param View $view
     */
    public function clearFrame(View $view): void
    {
        [$sizeX, $sizeY] = $view->frameSize();

        for ($y = 0; $y < $sizeY; $y++) {
            for ($x = 0; $x < $sizeX; $x++) {
                $this->write($view, $x, $y, ' ');
            }
        }
    }

    private function clearContent(View $view): void
    {
        [$sizeX, $sizeY] = $view->size();

        for ($y = 0; $y < $sizeY; $y++) {
            for ($x = 0; $x < $sizeX; $x++) {
                $view->writeBuffer($x + 1, $y + 1, ' ');
            }
        }
    }

    private function drawEdges(View $view): void
    {
        [$sizeX, $sizeY] = $view->size();

        if ($sizeX > 0) {
            foreach (range(1, $sizeX) as $x) {
                $view->writeBuffer((int)$x, 0, $view->getBorderChar(0));
                $view->writeBuffer((int)$x, $sizeY + 1, $view->getBorderChar(0));
            }
        }

        if ($sizeY > 0) {
            foreach (range(1, $sizeY) as $y) {
                $view->writeBuffer(0, (int)$y, $view->getBorderChar(1));
                $view->writeBuffer($sizeX + 1, (int)$y, $view->getBorderChar(1));
            }
        }
    }

    /**
     * @param View $view
     */
    private function drawCorners(View $view): void
    {
        [$sizeX, $sizeY] = $view->size();

        $view->writeBuffer(0, 0, $view->getBorderChar(2));
        $view->writeBuffer($sizeX + 1, 0, $view->getBorderChar(3));
        $view->writeBuffer(0, $sizeY + 1, $view->getBorderChar(4));
        $view->writeBuffer($sizeX + 1, $sizeY + 1, $view->getBorderChar(5));
    }

    private function drawTitle(View $view): void
    {
        $this->write($view, 1, 0, ' ' . $view->getTitle() . ' ');
    }

    private function drawContent(View $view): void
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

                $view->writeBuffer($x + (int)$view->hasBorder(), $y + (int)$view->hasBorder(), $char);

                if ($x === $sizeX - 1) {
                    ++$y;
                }
            }

            ++$y;
        }
    }

    /**
     * @param View $view
     * @param int $y
     * @param int $x
     * @param string|array $chars
     */
    private function write(View $view, int $x, int $y, $chars): void
    {
        [$positionX, $positionY] = $view->position();

        if (is_string($chars)) {
            $chars = Char::charsToArray($chars);
        }

        foreach ($chars as $i => $char) {
            $view->writeBuffer($x + $i, $y, $char);

            if ($char !== null && $view->isInstantRender() && $this->isDisplayable($view, $y, $x)) {
                $this->terminal->moveCursor((int)$positionX + $x + $i, $positionY + $y)->write($char);
            }
        }
    }

    /**
     * @param View $view
     * @param int $y Relative position Y in view
     * @param int $x Relative position X in view
     * @return bool
     */
    private function isDisplayable(View $view, int $x, int $y): bool
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
