<?php

namespace MilesChou\PhermUI\View;

interface ViewInterface
{
    /**
     * Clear view to default
     *
     * @return ViewInterface
     */
    public function clear(): ViewInterface;

    /**
     * @return array<int>
     */
    public function frameSize(): array;

    /**
     * @param int $key
     * @return string
     */
    public function getBorderChar(int $key): string;

    /**
     * @return array<int, mixed>
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
     * @return array<int>
     */
    public function position(): array;

    /**
     * @param string $title
     * @return ViewInterface
     */
    public function setTitle(string $title): ViewInterface;

    /**
     * @return array<int>
     */
    public function size(): array;

    /**
     * @param int $y
     * @param int $x
     * @param string|null $char
     */
    public function write(int $x, int $y, $char): void;
}
