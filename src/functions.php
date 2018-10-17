<?php
/**
 * Функция ord() для мультибайтовы строк
 *
 * @param string $c символ utf-8
 * @return int код символа
 */
function uniord($c)
{
    $h = ord($c{0});
    if ($h <= 0x7F) {
        return $h;
    } else {
        if ($h < 0xC2) {
            return false;
        } else {
            if ($h <= 0xDF) {
                return ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
            } else {
                if ($h <= 0xEF) {
                    return ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6
                        | (ord($c{2}) & 0x3F);
                } else {
                    if ($h <= 0xF4) {
                        return ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12
                            | (ord($c{2}) & 0x3F) << 6
                            | (ord($c{3}) & 0x3F);
                    } else {
                        return false;
                    }
                }
            }
        }
    }
}

/**
 * Функция chr() для мультибайтовы строк
 *
 * @param int $c код символа
 * @return string символ utf-8
 */
function unichr($c)
{
    if ($c <= 0x7F) {
        return chr($c);
    } else {
        if ($c <= 0x7FF) {
            return chr(0xC0 | $c >> 6) . chr(0x80 | $c & 0x3F);
        } else {
            if ($c <= 0xFFFF) {
                return chr(0xE0 | $c >> 12) . chr(0x80 | $c >> 6 & 0x3F)
                    . chr(0x80 | $c & 0x3F);
            } else {
                if ($c <= 0x10FFFF) {
                    return chr(0xF0 | $c >> 18) . chr(0x80 | $c >> 12 & 0x3F)
                        . chr(0x80 | $c >> 6 & 0x3F)
                        . chr(0x80 | $c & 0x3F);
                } else {
                    return false;
                }
            }
        }
    }
}
