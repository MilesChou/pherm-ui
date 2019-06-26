<?php

namespace MilesChou\PhermUI\View\Concerns;

use OutOfRangeException;

/**
 * The layout is include the border and position
 *
 * @package MilesChou\PhermUI\View\Concerns
 */
trait Frame
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
     * @var array Invoke when frame data change
     */
    private $frameChangeCallback = [];

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
     * @param callable $callback
     */
    public function addFrameChangeCallback($callback): void
    {
        $this->frameChangeCallback[] = $callback;
    }

    /**
     * @return static
     */
    public function disableBorder()
    {
        $this->border = false;

        $this->fireFrameChangeCallback('border');

        return $this;
    }

    /**
     * @return static
     */
    public function enableBorder()
    {
        $this->border = true;

        $this->fireFrameChangeCallback('border');

        return $this;
    }

    /**
     * @param string $event
     */
    public function fireFrameChangeCallback(string $event): void
    {
        foreach ($this->frameChangeCallback as $callable) {
            $callable($this, $event);
        }
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
        return $this->sizeX + 2;
    }

    /**
     * @return int
     */
    public function frameSizeY(): int
    {
        return $this->sizeY + 2;
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
     * @return array
     */
    public function position(): array
    {
        return [$this->positionX, $this->positionY];
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

        $this->fireFrameChangeCallback('border');

        return $this;
    }

    /**
     * @param int $x
     * @param int $y
     */
    public function setPosition(int $x, int $y): void
    {
        $this->positionX = $x;
        $this->positionY = $y;

        $this->fireFrameChangeCallback('position');
    }

    /**
     * @param int $x
     * @param int $y
     */
    public function setSize(int $x, int $y): void
    {
        $this->sizeX = $x;
        $this->sizeY = $y;

        $this->fireFrameChangeCallback('size');
    }

    /**
     * @return array
     */
    public function size(): array
    {
        return [$this->sizeX(), $this->sizeY()];
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
