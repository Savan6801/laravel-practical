<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Admin;
use App\Models\Product;

class ProductCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product(): void
    {
        $admin = Admin::factory()->create();

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
            'category' => 'Test Category'
        ];

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), $productData);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_unauthenticated_user_cannot_create_product(): void
    {
        $response = $this->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'price' => 10
        ]);

        $response->assertRedirect(route('admin.login'));
    }
}
