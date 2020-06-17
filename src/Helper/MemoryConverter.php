<?php


namespace App\Helper;


class MemoryConverter
{
    public function __invoke(int $size)
    {
        $unit = array('B', 'kB', 'MB', 'GB', 'TB', 'PB');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }
}
