<?php

namespace Laramate\Support\Tests\Unit\Mapper;

use InvalidArgumentException;
use Laramate\Support\Mapper\Mapper;
use Laramate\Support\Tests\TestCase;

enum TestStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}

class TestMapper extends Mapper
{
    protected array $attributes = [
        'name',
        'email',
        'city',
        'full_name',
        'status',
        'age',
        'newsletter',
        'created_at',
        'country',
        'source',
        'note',
    ];

    protected array $map = [
        'email' => 'mail_address',
        'city' => 'address.city',
    ];

    protected array $defaults = [
        'country' => 'DE',
        'source' => null,
    ];

    protected array $casts = [
        'age' => 'int',
        'newsletter' => 'bool',
        'status' => TestStatus::class,
        'created_at' => 'datetime',
    ];

    public function map(): array
    {
        // Closures are not allowed in property initializers
        return array_merge($this->map, [
            'full_name' => fn (array $data) => trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? '')) ?: null,
        ]);
    }
}

class MapperTest extends TestCase
{
    protected function data(): array
    {
        return [
            'name' => 'Laramate',
            'mail_address' => 'coding@laramate.de',
            'address' => ['city' => 'Hamburg'],
            'first_name' => 'Tobi',
            'last_name' => 'Kivelip',
            'status' => 'active',
            'age' => '42',
            'newsletter' => '1',
            'created_at' => '2026-06-12 10:00:00',
        ];
    }

    public function test_std_class_input()
    {
        $data = json_decode('{"name": "Laramate", "mail_address": "coding@laramate.de", "address": {"city": "Hamburg"}}');

        $result = TestMapper::convert($data);

        $this->assertEquals('Laramate', $result['name']);
        $this->assertEquals('coding@laramate.de', $result['email']);
        $this->assertEquals('Hamburg', $result['city']);
    }

    public function test_eloquent_model_input()
    {
        $model = new class extends \Illuminate\Database\Eloquent\Model
        {
            protected $guarded = [];
        };

        $model->forceFill(['name' => 'Laramate', 'mail_address' => 'coding@laramate.de']);

        $result = TestMapper::convert($model);

        $this->assertEquals('Laramate', $result['name']);
        $this->assertEquals('coding@laramate.de', $result['email']);
    }

    public function test_collection_input()
    {
        $result = TestMapper::convert(collect($this->data()));

        $this->assertEquals('Laramate', $result['name']);
    }

    public function test_direct_attribute_mapping()
    {
        $result = TestMapper::convert($this->data());

        $this->assertEquals('Laramate', $result['name']);
    }

    public function test_name_map()
    {
        $result = TestMapper::convert($this->data());

        $this->assertEquals('coding@laramate.de', $result['email']);
    }

    public function test_dot_notation_map()
    {
        $result = TestMapper::convert($this->data());

        $this->assertEquals('Hamburg', $result['city']);
    }

    public function test_closure_map()
    {
        $result = TestMapper::convert($this->data());

        $this->assertEquals('Tobi Kivelip', $result['full_name']);
    }

    public function test_map_method()
    {
        $mapper = new class extends Mapper
        {
            protected array $attributes = ['note'];

            protected function mapNote(): string
            {
                return 'Name: '.$this->get('name');
            }
        };

        $this->assertEquals('Name: Laramate', $mapper::convert($this->data())['note']);
    }

    public function test_map_method_for_dotted_attribute()
    {
        $mapper = new class extends Mapper
        {
            protected array $attributes = ['user.name'];

            protected function mapUserName(): string
            {
                return ucfirst($this->get('user.name'));
            }
        };

        $this->assertEquals('Tobi', $mapper::convert(['user' => ['name' => 'tobi']])['user.name']);
    }

    public function test_map_method_for_snake_case_attribute()
    {
        $mapper = new class extends Mapper
        {
            protected array $attributes = ['first_name'];

            protected function mapFirstName(): string
            {
                return ucfirst($this->get('first_name'));
            }
        };

        $this->assertEquals('Tobi', $mapper::convert(['first_name' => 'tobi'])['first_name']);
    }

    public function test_map_method_has_priority_over_map_and_direct_key()
    {
        $mapper = new class extends Mapper
        {
            protected array $attributes = ['name'];

            protected array $map = ['name' => 'other'];

            protected function mapName(): string
            {
                return 'method';
            }
        };

        $this->assertEquals('method', $mapper::convert(['name' => 'direct', 'other' => 'mapped'])['name']);
    }

    public function test_default_method_has_priority_over_defaults_array()
    {
        $mapper = new class extends Mapper
        {
            protected array $attributes = ['country'];

            protected array $defaults = ['country' => 'DE'];

            protected function defaultCountry(): string
            {
                return 'AT';
            }
        };

        $this->assertEquals('AT', $mapper::convert([])['country']);
    }

    public function test_default_method_for_dotted_attribute()
    {
        $mapper = new class extends Mapper
        {
            protected array $attributes = ['user.role'];

            protected function defaultUserRole(): string
            {
                return 'guest';
            }
        };

        $this->assertEquals('guest', $mapper::convert([])['user.role']);
    }

    public function test_default_is_used_for_missing_attribute()
    {
        $result = TestMapper::convert($this->data());

        $this->assertEquals('DE', $result['country']);
        $this->assertNull($result['source']);
    }

    public function test_explicit_null_is_not_overridden_by_default()
    {
        $result = TestMapper::convert(
            array_merge($this->data(), ['country' => null])
        );

        $this->assertNull($result['country']);
    }

    public function test_default_method()
    {
        $mapper = new class extends Mapper
        {
            protected array $attributes = ['country'];

            protected function defaultCountry(): string
            {
                return 'AT';
            }
        };

        $this->assertEquals('AT', $mapper::convert([])['country']);
    }

    public function test_closure_default()
    {
        $mapper = new class extends Mapper
        {
            protected array $attributes = ['country'];

            public function defaults(): array
            {
                return ['country' => fn (array $data) => $data['fallback']];
            }
        };

        $this->assertEquals('CH', $mapper::convert(['fallback' => 'CH'])['country']);
    }

    public function test_casts()
    {
        $result = TestMapper::convert($this->data());

        $this->assertSame(42, $result['age']);
        $this->assertTrue($result['newsletter']);
        $this->assertSame(TestStatus::Active, $result['status']);
        $this->assertEquals('2026-06-12 10:00:00', $result['created_at']->format('Y-m-d H:i:s'));
    }

    public function test_null_is_never_cast()
    {
        $result = TestMapper::convert(
            array_merge($this->data(), ['age' => null])
        );

        $this->assertNull($result['age']);
    }

    public function test_closure_cast()
    {
        $mapper = new class extends Mapper
        {
            protected array $attributes = ['name'];

            public function casts(): array
            {
                return ['name' => fn ($value) => trim($value)];
            }
        };

        $this->assertEquals('Laramate', $mapper::convert(['name' => '  Laramate  '])['name']);
    }

    public function test_invalid_cast_throws()
    {
        $mapper = new class extends Mapper
        {
            protected array $attributes = ['name'];

            protected array $casts = ['name' => 'nonsense'];
        };

        $this->expectException(InvalidArgumentException::class);

        $mapper::convert(['name' => 'Laramate']);
    }

    public function test_unmapped_attribute_without_default_is_null()
    {
        $result = TestMapper::convert($this->data());

        $this->assertNull($result['note']);
    }

    public function test_convert_many()
    {
        $result = TestMapper::convertMany([
            $this->data(),
            array_merge($this->data(), ['name' => 'Support']),
        ]);

        $this->assertCount(2, $result);
        $this->assertEquals('Laramate', $result[0]['name']);
        $this->assertEquals('Support', $result[1]['name']);
    }
}
