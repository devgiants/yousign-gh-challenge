<?php


namespace App\Provider;


class JsonDataProvider
{
    // Placeholder constants
    public const TEMP_GZ_FILE_PATH = '/tmp/data.gz';
    public const CURRENT_JSON_FILE_PATH = '/tmp/data.json';

    /**
     * @param string $finalDataChain
     */
    public function retrieveGzipData(string $finalDataChain)
    {
        // Get data
        file_put_contents(static::TEMP_GZ_FILE_PATH, file_get_contents($finalDataChain));

    }

    public function extractJsonData()
    {
        // Extraction
        $gzFp = gzopen(static::TEMP_GZ_FILE_PATH, 'rb');
        $jsonFp = fopen(static::CURRENT_JSON_FILE_PATH, 'wb');
        while (!gzeof($gzFp)) {
            fwrite($jsonFp, gzread($gzFp, 4096));
        }
        gzclose($gzFp);
        unlink(static::TEMP_GZ_FILE_PATH);
        fclose($jsonFp);
    }
}
