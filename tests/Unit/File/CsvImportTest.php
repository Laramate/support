<?php

namespace Laramate\Support\Tests\Unit\File;

use Laramate\Support\File\CsvImport;
use Laramate\Support\Tests\TestCase;

class CsvImportTest extends TestCase
{
    protected function mockFile(string $name): string
    {
        return dirname(__FILE__).'/../../Mocks/Files/'.$name;
    }

    public function test_make_returns_instance()
    {
        $this->assertInstanceOf(
            CsvImport::class,
            CsvImport::make($this->mockFile('test.csv'))
        );
    }

    public function test_import_with_first_line_as_keys()
    {
        $data = CsvImport::make($this->mockFile('test.csv'), true)->handle();

        $this->assertCount(3, $data);
        $this->assertEquals('1', $data[0]['id']);
        $this->assertEquals('Vorschriften', $data[0]['name']);
        $this->assertEquals('2024-02-21 14:32:15', $data[0]['created_at']);
        $this->assertEquals('2024-02-21 14:32:15', $data[0]['updated_at']);
        $this->assertEquals('Gefährdungen / Schutzmaßnahmen / Mängel', $data[2]['name']);
    }

    public function test_import_with_numeric_keys()
    {
        $data = CsvImport::make($this->mockFile('test.csv'))->handle();

        // Without first_line_as_keys the header line is a regular data row.
        $this->assertCount(4, $data);
        $this->assertEquals(['id', 'name', 'created_at', 'updated_at', 'deleted_at'], $data[0]);
        $this->assertEquals('1', $data[1][0]);
        $this->assertEquals('Vorschriften', $data[1][1]);
    }

    public function test_null_strings_are_converted_to_null()
    {
        $data = CsvImport::make($this->mockFile('test.csv'), true)->handle();

        $this->assertNull($data[0]['deleted_at']);
        $this->assertNull($data[1]['deleted_at']);
        $this->assertNull($data[2]['deleted_at']);
    }

    public function test_import_with_custom_separator()
    {
        $data = CsvImport::make(
            uri: $this->mockFile('test-semicolon.csv'),
            first_line_as_keys: true,
            separator: ';',
        )->handle();

        $this->assertCount(2, $data);
        $this->assertEquals('Müller; Söhne', $data[0]['name']);
        $this->assertEquals('Berlin', $data[0]['city']);
        $this->assertEquals('Schmidt', $data[1]['name']);
    }

    public function test_missing_columns_are_padded_with_null()
    {
        $data = CsvImport::make($this->mockFile('test-missing-columns.csv'), true)->handle();

        $this->assertCount(2, $data);
        $this->assertEquals('alice@example.com', $data[0]['email']);
        $this->assertEquals('Bob', $data[1]['name']);
        $this->assertArrayHasKey('email', $data[1]);
        $this->assertNull($data[1]['email']);
    }
}
