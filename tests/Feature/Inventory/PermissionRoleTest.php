<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_role_can_access_inventory_routes(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get(route('inventory.products.index', absolute: false))
            ->assertOk();
    }

    public function test_staff_role_cannot_access_admin_only_inventory_routes(): void
    {
        $this->actingAs(User::factory()->staff()->create());

        $this->get(route('inventory.products.index', absolute: false))
            ->assertForbidden();

        $this->get(route('inventory.stock.movement', absolute: false))
            ->assertForbidden();

        $this->get(route('inventory.stock.adjustment', absolute: false))
            ->assertForbidden();
    }

    public function test_guests_are_redirected_from_inventory_routes(): void
    {
        $this->get(route('inventory.stock.movement', absolute: false))
            ->assertRedirect(route('login', absolute: false));
    }
}
