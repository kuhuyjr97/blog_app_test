<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory;
use Faker\Generator;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    //-----------------initial -----------------//
    // public function createFaker(): Generator
    // {
    //     return Factory::create();
    // }
    // public function createRandomUser(): array
    // {
    //     $faker = $this->createFaker();
    //     return [
    //         'name' => $faker->name,
    //         'email' => $faker->unique()->safeEmail(),
    //         'password' => 'password',
    //         'password_confirmation' => 'password'
    //     ];
    // }
    // public function registerRandomUser(): array
    // {
    //     $user = $this->createRandomUser();
    //     $response = $this->postJson('/api/register', $user);
    //     $response->assertStatus(201);
    //     return $user;
    // }
    //-----------------test register---------------------------//

    public function testRegisterSuccessfully(): void
    {
        $params = $this->createUserParams();

        $this->assertDatabaseEmpty('users');

        $response = $this->postJson('/api/register', $params);
        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'name' => $params['name'],
            'email' => $params['email']
        ]);
    }

    //create test register fail
   public function testRegisterWithExistedEmailFail(): void
    {
        $params = $this->createUserParams();

        User::factory()->create([
            'email' => $params['email']
        ]);

        $this->assertDatabaseCount('users', 1);

        $response = $this->postJson('/api/register', $params);
        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                 'email' => [
                    'The email has already been taken.'
                ]
            ]
        ]);

        $this->assertDatabaseCount('users', 1);
    }

    // create function to check password if less than 6 characters
    public function testRegisterWithPasswordLessThan6Characters(): void
{
    $params = $this->createUserParams();
    $params['password'] = 'pass';
    $params['password_confirmation'] = 'pass';

    $response= $this->postJson('/api/register', $params);
    $response->assertStatus(422)
        ->assertJson([
            'errors' => [
                'password' => [
                    'The password field must be at least 6 characters.'
                ]
            ]
        ]);

    $this->assertDatabaseCount('users', 0);
}

//     //create function to check if password and password confirmation are the same
//     public function testRegisterWithPasswordAndConfirmationNotMatch(): void
//     {
//         $faker = $this->createFaker();
//         $user = [
//             'name' => $faker->name,
//             'email' => $faker->unique()->safeEmail(),
//             'password' => 'password',
//             'password_confirmation' => 'pass'
//         ];

//         $response = $this->postJson('/api/register', $user);
//         $response->assertStatus(422) //401 
//         ->assertJson([
//             'errors' => [
//                 'password' => [
//                     'The password field confirmation does not match.'
//                 ]
//             ]
//         ]);            
//     }

//     //create function test if missing any field
//     public function testRegisterWithMissingField(): void
//     {

//         $response = $this->postJson('/api/register', []);
//         $response->assertStatus(422); //401
//     }

//     ///-----------------test login-----------------///
//     public function testLoginSuccessfully(): void
//     {
//         $user = $this->registerRandomUser();

//         $loginResponse = $this->postJson('/api/login', $user);
//         $token = $loginResponse->json('token');
//         $loginResponse->assertStatus(201)
//             ->assertJson([
//                 'user' => [
//                     'name' => $user['name'],
//                     'email' => $user['email'],
//                 ],
//                 'token' => $token
//             ]);
//     }
//     //create function with wrong password
//     public function testLoginWithWrongPassword(): void
//     {
//         $user = $this->registerRandomUser();

//         $loginResponse = $this->postJson('/api/login', [
//             'email' => $user['email'],
//             'password' => 'wrong password'
//         ]);
//         $loginResponse->assertStatus(401);
//     }
  
 
//     public function testLoginWithWrongToken(): void
//     {
//         $user = $this->registerRandomUser();

//         $loginResponse = $this->postJson('/api/login', $user);
//         $token = $loginResponse->json('token');
//         $invalidToken = $token . 'invalid';

//         $loginWithInvalidToken = $this->withHeaders([
//             'Authorization' => 'Bearer ' . $invalidToken
//         ])->postJson('/api/login', [
//             'email' => $user['email'],
//             'password' => 'wrong password'
//         ]);

//         $loginWithInvalidToken->assertStatus(401);
//     }
//     //-----------------test logout-----------------//
//     //create log out test function
//     public function testLogoutSuccessfully(): void
//     {
//         $user = $this->registerRandomUser();

//         $loginResponse = $this->postJson('/api/login', $user);
//         $token = $loginResponse->json('token');
      

//         // $logoutResponse = $this->postJson('/api/logout', [], [
//         //     'Authorization' => 'Bearer ' . $token
//         // ]);
//         $lougoutResponse = $this->withHeaders([
//             'Authorization' => 'Bearer ' .$token
//         ])->postJson('api/logout', []);
//         $lougoutResponse->assertStatus(200);
//     }

//     //create unsuccessful log out test function
//     public function testLogoutUnSuccessfullyWithWrongToken(): void
//     {
//         $user = $this->registerRandomUser();

//         $loginResponse = $this->postJson('/api/login', $user);
//         $token = $loginResponse->json('token');
//         $invalidToken = $token . 'invalid';

//         $logoutResponse = $this->withHeaders([
//             'Authorization ' => 'Bearer ' .$invalidToken
//         ])->postJson('api/logout',[]);
       
//         $logoutResponse->assertStatus(401);
//     }

    private function createUserParams(): array
    {
        return [
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
    }
}
