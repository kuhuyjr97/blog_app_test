<?php

namespace Tests\Feature;

use Tests\TestCase;
use Faker\Generator;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\AuthControllerTest;
class BlogControllerTest extends AuthControllerTest
{
    //-----------initial----------------//
    use RefreshDatabase;
    
    //-----------test index----------------//
    public function testViewWithouLogin(){
        $response =  $this->getJson('/api/blogs');
        $response->assertStatus(401);
    }
    public function testViewWithLogin(){
        $user = $this->registerRandomUser();
        $loginResponse = $this->postJson('/api/login', $user);
        $token = $loginResponse->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/blogs');
        $response->assertStatus(200);
    }

    //-----------test store----------------//
    public function testStoreWithoutLogin(){
        $response = $this->postJson('/api/blogs', [
            'name' => 'test',
            'description' => 'test',
        ]);
        $response->assertStatus(401);
    }
    public function testStoreAfterLogin(){
        $user = $this->registerRandomUser();
        $loginResponse = $this->postJson('/api/login', $user);
        $token = $loginResponse->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/blogs',['name' => 'test',
                                'descripton' => 'test description',]);
        $response->assertStatus(200);
    }
    


    
}
