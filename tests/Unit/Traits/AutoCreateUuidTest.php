<?php

namespace Laramate\Support\Tests\Unit\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laramate\Support\Tests\TestCase;
use Ramsey\Uuid\Uuid;

class UuidModel extends Model
{
    use \Laramate\Support\Traits\AutoCreateUuid;

    protected $table = 'uuid_models';

    protected $guarded = [];

    public $timestamps = false;
}

class CustomUuidColumnModel extends Model
{
    use \Laramate\Support\Traits\AutoCreateUuid;

    protected $table = 'custom_uuid_models';

    protected $guarded = [];

    public $timestamps = false;

    protected $uuid_column = 'external_id';
}

class AutoCreateUuidTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('uuid_models', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
        });

        Schema::create('custom_uuid_models', function (Blueprint $table) {
            $table->id();
            $table->uuid('external_id')->nullable();
        });
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
    }

    public function test_it_generates_a_uuid_on_creation(): void
    {
        $model = UuidModel::create();

        $this->assertNotEmpty($model->uuid);
        $this->assertTrue(Uuid::isValid($model->uuid));
    }

    public function test_it_keeps_a_valid_uuid(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        $model = UuidModel::create(['uuid' => $uuid]);

        $this->assertSame($uuid, $model->uuid);
    }

    public function test_it_replaces_an_invalid_uuid(): void
    {
        $model = UuidModel::create(['uuid' => 'not-a-uuid']);

        $this->assertNotSame('not-a-uuid', $model->uuid);
        $this->assertTrue(Uuid::isValid($model->uuid));
    }

    public function test_it_generates_unique_uuids(): void
    {
        $first = UuidModel::create();
        $second = UuidModel::create();

        $this->assertNotSame($first->uuid, $second->uuid);
    }

    public function test_uuid_is_persisted(): void
    {
        $model = UuidModel::create();

        $this->assertSame($model->uuid, $model->fresh()->uuid);
    }

    public function test_it_uses_the_default_uuid_column(): void
    {
        $this->assertSame('uuid', (new UuidModel)->getUuidColumn());
    }

    public function test_it_supports_a_custom_uuid_column(): void
    {
        $model = CustomUuidColumnModel::create();

        $this->assertSame('external_id', $model->getUuidColumn());
        $this->assertTrue(Uuid::isValid($model->external_id));
    }

    public function test_renew_uuid_sets_a_new_uuid(): void
    {
        $model = UuidModel::create();
        $original = $model->uuid;

        $result = $model->renewUuid();

        $this->assertSame($model, $result);
        $this->assertNotSame($original, $model->uuid);
        $this->assertTrue(Uuid::isValid($model->uuid));
    }
}
