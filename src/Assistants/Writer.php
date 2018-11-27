<?php

namespace Handscube\Assistants;

use Handscube\Kernel\Exceptions\InvalidException;

class Writer
{

    public static function open(string $file, string $mode, bool $use_include_path = false, resource $context = null)
    {
        return fopen($file, $mode, $use_include_path, $context);
    }

    public static function write(string $file, string $content, int $length = null)
    {
        $fp = self::open($file, 'r');
        if ($fp) {
            \fwrite($fp, $content);
            fclose($fp);
        } else {
            throw new InvalidException("File $file open failed!");
        }
    }

}
