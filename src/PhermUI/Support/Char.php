<?php

namespace MilesChou\PhermUI\Support;

class Char
{
    /**
     * Transform the mbstring to an array
     *
     * @param string $str
     * @return array
     */
    public static function charsToArray(string $str): array
    {
        if ('' === $str) {
            return [];
        }

        $arr = [];
        $len = mb_strlen($str);

        for ($i = 0; $i < $len; $i++) {
            $arr[] = mb_substr($str, $i, 1);
        }

        return array_reduce($arr, function ($carry, $char) {
            $carry[] = $char;

            // Workaround for CJP char
            // See http://www.unicode.org/Public/5.0.0/ucd/Blocks.txt
            $order = mb_ord($char);
            if ($order >= 0x4e00 && $order <= 0x9fff) {
                $carry[] = null;
            }

            return $carry;
        }, []);
    }
}
