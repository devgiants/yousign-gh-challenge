<?php

namespace App\Tests;

use App\Helper\MemoryConverter;
use PHPUnit\Framework\TestCase;

/**
 * Class ConvertMemoryTest
 * @package App\Tests
 */
class MemoryConverterTest extends TestCase
{
    /**
     *
     */
    public function testCheckConversion()
    {
        $memoryConverter = new MemoryConverter();

        $this->assertEquals('1.51 kB', $memoryConverter(1545));
        $this->assertEquals('14.74 MB', $memoryConverter(15454545));
        $this->assertEquals('16.67 GB', $memoryConverter(17895454354));
    }
}
