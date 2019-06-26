<?php

namespace MilesChou\PhermUI;

use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\Support\Char;
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
                    $this->terminal->writeCursor($positionY + $y, $positionX + $x, $cell[0]);
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
                $this->write($view, $y, $x, ' ');
            }
        }
    }

    private function clearContent(View $view): void
    {
        [$sizeX, $sizeY] = $view->size();

        for ($y = 0; $y < $sizeY; $y++) {
            for ($x = 0; $x < $sizeX; $x++) {
                $view->writeBuffer($y + 1, $x + 1, ' ');
            }
        }
    }

    private function drawEdges(View $view): void
    {
        [$sizeX, $sizeY] = $view->size();

        if ($sizeX > 0) {
            foreach (range(1, $sizeX) as $x) {
                $view->writeBuffer(0, (int)$x, $view->getBorderChar(0));
                $view->writeBuffer($sizeY + 1, (int)$x, $view->getBorderChar(0));
            }
        }

        if ($sizeY > 0) {
            foreach (range(1, $sizeY) as $y) {
                $view->writeBuffer((int)$y, 0, $view->getBorderChar(1));
                $view->writeBuffer((int)$y, $sizeX + 1, $view->getBorderChar(1));
            }
        }
    }

    private function drawCorners(View $view): void
    {
        [$sizeX, $sizeY] = $view->size();

        $view->writeBuffer(0, 0, $view->getBorderChar(2));
        $view->writeBuffer(0, $sizeX + 1, $view->getBorderChar(3));
        $view->writeBuffer($sizeY + 1, 0, $view->getBorderChar(4));
        $view->writeBuffer($sizeY + 1, $sizeX + 1, $view->getBorderChar(5));
    }

    private function drawTitle(View $view): void
    {
        $this->write($view, 0, 1, ' ' . $view->getTitle() . ' ');
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

                $view->writeBuffer($y + (int)$view->hasBorder(), $x + (int)$view->hasBorder(), $char);

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
    private function write(View $view, int $y, int $x, $chars): void
    {
        [$positionX, $positionY] = $view->position();

        if (is_string($chars)) {
            $chars = Char::charsToArray($chars);
        }

        foreach ($chars as $i => $char) {
            $view->writeBuffer($y, $x + $i, $char);

            if ($char !== null && $view->isInstantRender() && $this->isDisplayable($view, $y, $x)) {
                $this->terminal->writeCursor($positionY + $y, (int)$positionX + $x + $i, $char);
            }
        }
    }

    /**
     * @param View $view
     * @param int $y Relative position Y in view
     * @param int $x Relative position X in view
     * @return bool
     */
    private function isDisplayable(View $view, int $y, int $x): bool
    {
        [$positionX, $positionY] = $view->position();

        $y += $positionY;
        $x += $positionX;

        if ($y < 0 || $y > $this->terminal->height()) {
            return false;
        }

        if ($x < 0 || $x > $this->terminal->width()) {
            return false;
        }

        return true;
    }
}
