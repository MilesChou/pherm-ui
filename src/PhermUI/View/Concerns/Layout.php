<?php

namespace MilesChou\PhermUI\View\Concerns;

use OutOfRangeException;

trait Layout
{
    /**
     * @var bool
     */
    private $border = true;

    /**
     * @var array [horizontal, vertical, top-left, top-right, bottom-left, bottom-right]
     */
    private $borderChars = [];

    /**
     * @var int
     */
    private $positionX;

    /**
     * @var int
     */
    private $positionY;

    /**
     * @var int
     */
    private $sizeX;

    /**
     * @var int
     */
    private $sizeY;

    /**
     * @param int $key
     * @return string
     */
    public static function getBorderDefault(int $key): string
    {
        static $border = ['─', '│', '┌', '┐', '└', '┘'];

        if (array_key_exists($key, $border)) {
            return $border[$key];
        }

        throw new OutOfRangeException("Illegal index '$key'");
    }

    /**
     * @return static
     */
    public function disableBorder()
    {
        $this->border = false;
        return $this;
    }

    /**
     * @return static
     */
    public function enableBorder()
    {
        $this->border = true;
        return $this;
    }

    /**
     * @return array
     */
    public function frameSize(): array
    {
        return [$this->frameSizeX(), $this->frameSizeY()];
    }

    /**
     * @return int
     */
    public function frameSizeX(): int
    {
        return $this->sizeX + ($this->border ? 2 : 0);
    }

    /**
     * @return int
     */
    public function frameSizeY(): int
    {
        return $this->sizeY + ($this->border ? 2 : 0);
    }

    /**
     * @param int $key
     * @return string
     */
    public function getBorderChar(int $key): string
    {
        return $this->borderChars[$key] ?? static::getBorderDefault($key);
    }

    /**
     * @return bool
     */
    public function hasBorder(): bool
    {
        return $this->border;
    }

    /**
     * @param int|array $key
     * @param string $char
     * @return static
     */
    public function setBorderChar($key, $char = null)
    {
        if (is_array($key)) {
            $this->borderChars = $key;
        } else {
            $this->borderChars[$key] = $char;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function size(): array
    {
        return [$this->sizeX, $this->sizeY];
    }

    /**
     * @return int
     */
    public function sizeX(): int
    {
        return $this->sizeX;
    }

    /**
     * @return int
     */
    public function sizeY(): int
    {
        return $this->sizeY;
    }


    /**
     * @return static
     */
    public function useAsciiBorder()
    {
        return $this->setBorderChar(['-', '|', '+', '+', '+', '+']);
    }

    /**
     * @return static
     */
    public function useDefaultBorder()
    {
        return $this->setBorderChar([]);
    }
}
