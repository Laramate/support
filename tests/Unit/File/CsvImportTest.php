<?php

namespace Laramate\Support\Tests\Unit\File;

use Laramate\Support\File\CsvImport;
use Laramate\Support\Tests\TestCase;

class CsvImportTest extends TestCase
{
    public function test_example()
    {
        $importer = CsvImport::make(
            dirname(__FILE__).'/../../Mocks/Files/test.csv',
            true
        );

        $data = $importer->handle();

        $this->assertCount(3, $data);
        $this->assertEquals('Vorschriften', $data[0]['name']);
        $this->assertEquals('2024-02-21 14:32:15', $data[0]['created_at']);
        $this->assertEquals('2024-02-21 14:32:15', $data[0]['updated_at']);
        $this->assertNull($data[0]['deleted_at']);
    }
}
