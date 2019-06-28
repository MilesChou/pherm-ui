<?php

namespace MilesChou\PhermUI\View;

interface ViewInterface
{
    /**
     * @return array
     */
    public function frameSize(): array;

    /**
     * @param int $key
     * @return string
     */
    public function getBorderChar(int $key): string;

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
    public function hasBorder(): bool;

    /**
     * @return bool
     */
    public function hasTitle(): bool;

    /**
     * @return array
     */
    public function position(): array;

    /**
     * @param string $title
     * @return static
     */
    public function setTitle(string $title);

    /**
     * @return array
     */
    public function size(): array;

    /**
     * @param int $y
     * @param int $x
     * @param string|null $char
     */
    public function writeBuffer(int $x, int $y, $char): void;
}
