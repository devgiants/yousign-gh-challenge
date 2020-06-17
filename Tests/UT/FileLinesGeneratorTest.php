<?php

namespace App\Tests\UT;

use App\Helper\FileLinesGenerator;
use App\Helper\MemoryConverter;
use PHPUnit\Framework\TestCase;

/**
 * Class ConvertMemoryTest
 * @package App\Tests
 */
class FileLinesGeneratorTest extends TestCase
{
    /**
     *
     */
    public function testFileLinesGenerator()
    {
        $fileLinesGenerator = new FileLinesGenerator();

        $this->assertFileExists(__DIR__ . '/data/line_test.txt', 'Testing file not exsiting');

        foreach ($fileLinesGenerator(__DIR__ . '/data/line_test.txt') as $i => $line) {
            $this->assertEquals("line {$i}" . PHP_EOL, $line);
        }
    }
}
