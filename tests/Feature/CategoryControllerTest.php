<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\User;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_all_categories()
    {
        // Authenticate a user
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'), // Hashed password
            'role' => 'user', // Regular user
            'number' => '01273878787'
        ]);
        $this->actingAs($user);

        // Create some categories
        $categories = Category::factory()->count(5)->create();

        // Call the index endpoint
        $response = $this->getJson('/api/categories');

        // Assert response status and structure
        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data')
                 ->assertJsonStructure(['data' => [['id', 'name']]]);
    }

    /** @test */
    public function it_stores_a_category()
    {
        // Create a user with admin role
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'), // Hashed password
            'role' => 'admin', // Assigning admin role
            'number' => '01273878787'
        ]);

        // Set up category data
        $categoryData = [
            'name' => 'Test Category'
        ];

        // Authenticate as the admin user
        $this->actingAs($user);

        // Call the store endpoint
        $response = $this->postJson('/api/categories', $categoryData);

        // Assert response status and structure
        $response->assertStatus(201)
                 ->assertJsonStructure(['data' => ['id', 'name']])
                 ->assertJson(['data' => $categoryData]);

        // Check if the category was saved to the database
        $this->assertDatabaseHas('categories', $categoryData);
    }

    /** @test */
    public function it_shows_a_category()
    {
        // Create a user
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'), // Hashed password
            'role' => 'user', // Regular user
            'number' => '01273878787'
        ]);
        $this->actingAs($user);

        // Create a category
        $category = Category::factory()->create();

        // Call the show endpoint
        $response = $this->getJson("/api/categories/{$category->id}");

        // Assert response status and structure
        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['id', 'name']])
                 ->assertJson(['data' => [
                     'id' => $category->id,
                     'name' => $category->name
                 ]]);
    }

    /** @test */
    public function it_updates_a_category()
    {
        // Create a user with admin role
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'), // Hashed password
            'role' => 'admin', // Assigning admin role
            'number' => '01273878787'
        ]);

        // Create a category
        $category = Category::factory()->create();

        // New data for the category
        $updatedCategoryData = [
            'name' => 'Updated Category Name'
        ];

        // Authenticate as the admin user
        $this->actingAs($user);

        // Call the update endpoint
        $response = $this->putJson("/api/categories/{$category->id}", $updatedCategoryData);

        // Assert response status and structure
        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['id', 'name']])
                 ->assertJson(['data' => [
                     'id' => $category->id,
                     'name' => $updatedCategoryData['name']
                 ]]);

        // Check if the category was updated in the database
        $this->assertDatabaseHas('categories', $updatedCategoryData);
    }

    /** @test */
    public function it_deletes_a_category()
    {
        // Create a user with admin role
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'), // Hashed password
            'role' => 'admin', // Assigning admin role
            'number' => '01273878787'
        ]);

        // Create a category
        $category = Category::factory()->create();

        // Authenticate as the admin user
        $this->actingAs($user);

        // Call the destroy endpoint
        $response = $this->deleteJson("/api/categories/{$category->id}");

        // Assert response status
        $response->assertStatus(204);

        // Check if the category was deleted from the database
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
