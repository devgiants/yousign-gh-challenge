<?php


namespace App\Helper;

/**
 * Class FileLinesGenerator
 * @package App\Helper
 */
class FileLinesGenerator
{
    /**
     * @param string $filePath
     * @return \Generator
     */
    public function __invoke(string $filePath)
    {
        $f = fopen($filePath, 'r');

        // TODO add test around resource
        try {
            while ($line = fgets($f)) {
                yield $line;
            }
        } finally {
            fclose($f);
        }
    }
}
