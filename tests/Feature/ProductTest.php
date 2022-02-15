<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseMigrations;


    /** @test */
    public function authenticated_users_can_create_a_new_product()
    {
        //Given we have an authenticated user
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        //And a product object
        $product = Product::factory()->make();;
        //When user submits post request to create product endpoint
        $this->post('/api/product', $product->toArray());
        //It gets stored in the database
        $this->assertEquals(1, Product::all()->count());
    }

    /** @test */
    public function authorized_user_can_update_the_product(){
        //Given we have a signed in user
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        //And a task which is created by the user
        $product = Product::factory()->create();;
        $product->name = "Updated name";
        $product->price = 55555;
        //When the user hit's the endpoint to update the task
        $this->put('/api/product/' . $product->id, $product->toArray());
        //The task should be updated in the database.
        $this->assertDatabaseHas('products',['id'=> $product->id , 'name' => $product->name, 'price' => $product->price]);
    }
}
