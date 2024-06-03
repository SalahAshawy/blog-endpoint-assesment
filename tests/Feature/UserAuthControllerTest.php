<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\UserAuthController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAuthControllerTest extends TestCase
{
    use RefreshDatabase;

   
    public function test_user_registration()
    {
      
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'number' => '12345678901',
        ];

        $response = $this->post('api/register', $userData);

        
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => "User Created"
                 ]);

       
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }

    
    public function test_user_login()
    {
      
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'number'=>'12345678910',
            'role'=>'admin'
        ]);

      
        $response = $this->post('api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        
        ]);

      
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Login successful'
                 ]);
    }

    /** @test */
    public function test_user_logout()
    {
       
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'number'=>'12345678910',
            'name'=>'testing',
            'role'=>'admin'
        ]);

      
        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

  
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
                         ->post('api/logout');

        
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Logged out successfully'
                 ]);
    }
}
