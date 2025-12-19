<?php

namespace Tests\Unit\Models;

use App\Models\Type;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_type_has_fillable_attributes(): void
    {
        $type = new Type();

        $this->assertEquals(['api_id', 'name'], $type->getFillable());
    }

    public function test_type_can_be_created(): void
    {
        $type = Type::create([
            'api_id' => 1,
            'name' => 'normal',
        ]);

        $this->assertDatabaseHas('types', [
            'name' => 'normal',
            'api_id' => 1,
        ]);
    }

    public function test_type_has_pokemon_relationship(): void
    {
        $type = new Type();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsToMany::class,
            $type->pokemon()
        );
    }

    public function test_type_has_moves_relationship(): void
    {
        $type = new Type();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $type->moves()
        );
    }
}
