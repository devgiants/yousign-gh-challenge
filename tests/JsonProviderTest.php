<?php

namespace App\Tests;

use App\Provider\JsonDataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class JsonProviderTest
 * @package App\Tests
 */
class JsonProviderTest extends TestCase
{

    /**
     * Test GS file gathering
     */
    public function testFileGathering()
    {
        // Unit test are framework agnostic. No service injection here
        $jsonProvider = new JsonDataProvider();
        $dataChain = 'https://data.gharchive.org/2015-01-01-15.json.gz';

        // Execute service
        $jsonProvider->retrieveGzipData($dataChain);

        $this->assertFileExists(JsonDataProvider::TEMP_GZ_FILE_PATH);
    }

    /**
     * Test JSON extraction and verify lines number
     * @depends testFileGathering
     */
    public function testFileExtraction()
    {
        // Unit test are framework agnostic. No service injection here
        $jsonProvider = new JsonDataProvider();
        // Extract JSON data
        $jsonProvider->extractJsonData();

        // Check gzip file gone
        $this->assertFileNotExists(JsonDataProvider::TEMP_GZ_FILE_PATH);

        // Check json here
        $this->assertFileExists(JsonDataProvider::CURRENT_JSON_FILE_PATH);

        // Test line number
        $linesCount = 0;
        $handle = fopen(JsonDataProvider::CURRENT_JSON_FILE_PATH, "r");
        while (!feof($handle)) {
            fgets($handle);
            $linesCount++;
        }
        fclose($handle);

        $this->assertEquals(11352, $linesCount, "JSON file lines count does not match");
    }
}
