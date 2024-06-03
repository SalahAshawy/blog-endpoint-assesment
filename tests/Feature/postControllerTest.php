<?php

// tests/Feature/PostControllerTest.php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_all_posts()
    {
        // Create some posts with associated users and categories
        $posts = Post::factory()->count(5)->create();

        // Call the index endpoint
        $response = $this->getJson('/api/posts');

        // Assert response status and structure
        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data')
                 ->assertJsonStructure(['data' => [['id', 'title', 'body', 'user', 'category']]]);
    }

    /** @test */
    public function it_stores_a_post()
    {
        // Create a user
        $user = User::factory()->create([
            'role' => 'admin', // Ensure this user is an admin
        ]);

        // Create a category
        $category = Category::factory()->create();

        // Set up post data
        $postData = [
            'title' => 'Test Post',
            'body' => 'Lorem ipsum dolor sit amet',
            'category_id' => $category->id,
        ];

        // Authenticate as the user
        $this->actingAs($user);

        // Call the store endpoint
        $response = $this->postJson('/api/posts', $postData);

        // Assert response status and structure
        $response->assertStatus(201)
                 ->assertJsonStructure(['data' => ['id', 'title', 'body', 'user', 'category']]);

        // Check if the post was saved to the database
        $this->assertDatabaseHas('posts', $postData);
    }

    /** @test */
    public function it_updates_a_post()
    {
        // Create a user
        $user = User::factory()->create([
            'role' => 'admin', // Ensure this user is an admin
        ]);

        // Create a post with associated category
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        // New data for the post
        $updatedPostData = [
            'title' => 'Updated Title',
            'body' => 'Updated Body',
            'category_id' => $category->id,
        ];

        // Authenticate as the user
        $this->actingAs($user);

        // Call the update endpoint
        $response = $this->putJson("/api/posts/{$post->id}", $updatedPostData);

        // Assert response status and structure
        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['id', 'title', 'body', 'user', 'category']]);

        // Check if the post was updated in the database
        $this->assertDatabaseHas('posts', $updatedPostData);
    }

    /** @test */
    public function it_deletes_a_post()
    {
        // Create a user
        $user = User::factory()->create([
            'role' => 'admin', // Ensure this user is an admin
        ]);

        // Create a post with associated category
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        // Authenticate as the user
        $this->actingAs($user);

        // Call the destroy endpoint
        $response = $this->deleteJson("/api/posts/{$post->id}");

        // Assert response status
        $response->assertStatus(204);

        // Check if the post was deleted from the database
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
