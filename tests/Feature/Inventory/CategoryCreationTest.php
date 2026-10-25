<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Livewire\Mechanisms\ComponentRegistry;
use Modules\Inventory\Http\Controllers\Categories\Create;
use Tests\TestCase;

class CategoryCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_category_from_the_livewire_form(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(Create::class)
            ->set('name', 'Stationery')
            ->set('description', 'Office supplies')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('inventory.categories.index', absolute: false));

        $this->assertDatabaseHas('categories', [
            'name' => 'Stationery',
            'slug' => 'stationery',
            'description' => 'Office supplies',
        ]);
    }

    public function test_admin_can_open_the_category_creation_page(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get(route('inventory.categories.create', absolute: false))
            ->assertOk()
            ->assertSee('Create category')
            ->assertSee('data-csrf', false);
    }

    public function test_category_page_component_is_registered_for_livewire_updates(): void
    {
        $this->assertSame(
            Create::class,
            app(ComponentRegistry::class)->getClass('modules.inventory.livewire.categories.create')
        );
    }
}
