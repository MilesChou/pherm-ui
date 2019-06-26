<?php

namespace MilesChou\PhermUI\View;

use InvalidArgumentException;
use MilesChou\Pherm\Terminal;
use MilesChou\PhermUI\View\Concerns\Configuration;
use MilesChou\PhermUI\View\Concerns\Frame;

interface ViewInterface
{
    /**
     * @return array
     */
    public function getBuffer(): array;

    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return bool
     */
    public function hasTitle(): bool;

    /**
     * @param string $title
     * @return View
     */
    public function setTitle(string $title): View;

    /**
     * @param int $y
     * @param int $x
     * @param string|null $char
     */
    public function writeBuffer(int $x, int $y, $char): void;
}
