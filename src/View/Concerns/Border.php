<?php

namespace MilesChou\PhermUI\View\Concerns;

use OutOfRangeException;

trait Border
{
    /**
     * @var array [horizontal, vertical, top-left, top-right, bottom-left, bottom-right]
     */
    private $border = [];

    /**
     * @param int $key
     * @return string
     */
    public function getBorder(int $key): string
    {
        if (isset($this->border[$key])) {
            return $this->border[$key];
        }

        return static::getBorderDefault($key);
    }

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
     * @param int|array $key
     * @param string $char
     * @return static
     */
    public function setBorder($key, $char = null)
    {
        if (is_array($key)) {
            $this->border = $key;
        } else {
            $this->border[$key] = $char;
        }

        return $this;
    }


    /**
     * @return static
     */
    public function useAsciiBorder()
    {
        return $this->setBorder(['-', '|', '+', '+', '+', '+']);
    }

    /**
     * @return static
     */
    public function useDefaultBorder()
    {
        return $this->setBorder([]);
    }
}
