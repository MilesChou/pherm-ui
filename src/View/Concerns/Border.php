<?php

namespace MilesChou\PhermUI\View\Concerns;

use OutOfRangeException;

trait Border
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
