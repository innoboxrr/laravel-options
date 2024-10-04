<?php

namespace Innoboxrr\LaravelOptions\Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Innoboxrr\LaravelOptions\Tests\TestCase;

class OptionEndpointsTest extends TestCase
{

    use RefreshDatabase,
        WithFaker;

    public function test_option_policies_endpoint()
    {

        $option = \Innoboxrr\LaravelOptions\Models\Option::factory()->create();
        
        $headers = [
            'Authorization' => config('test.token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];  

        $payload = [
            'id' => $option->id
        ];

        $this->json('GET', '/api/innoboxrr/laraveloptions/option/policies', $payload, $headers)
            ->assertStatus(200);

    }

    public function test_option_policy_endpoint()
    {
        $headers = [
            'Authorization' => config('test.token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];  

        $payload = [
            'policy' => 'index'
        ];

        $this->json('GET', '/api/innoboxrr/laraveloptions/option/policy', $payload, $headers)
            ->assertJsonStructure([
                'index'
            ])
            ->assertStatus(200);

    }

    public function test_option_index_auth_endpoint()
    {

        $headers = [
            'Authorization' => config('test.token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];  

        $payload = [
            'managed' => true
        ];

        $this->json('GET', '/api/innoboxrr/laraveloptions/option/index', $payload, $headers)
            ->assertStatus(200);

    }

    public function test_option_index_guest_endpoint()
    {

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];  

        $payload = [
            'managed' => true
        ];

        $this->json('GET', '/api/innoboxrr/laraveloptions/option/index', $payload, $headers)
            ->assertStatus(401);
            
    }
    
    public function test_option_show_auth_endpoint()
    {

        $option = \Innoboxrr\LaravelOptions\Models\Option::latest()->first();

        $headers = [
            'Authorization' => config('test.token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];  

        $payload = [
            'option_id' => $option->id
        ];

        $this->json('GET', '/api/innoboxrr/laraveloptions/option/show', $payload, $headers)
            ->assertStatus(200);
            
    }

    public function test_option_show_guest_endpoint()
    {

        $option = \Innoboxrr\LaravelOptions\Models\Option::latest()->first();

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];  

        $payload = [
            'option_id' => $option->id
        ];

        $this->json('GET', '/api/innoboxrr/laraveloptions/option/show', $payload, $headers)
            ->assertStatus(401);
            
    }

    public function test_option_create_endpoint()
    {

        $user = \Innoboxrr\LaravelOptions\Models\User::first();

        $headers = [
            'Authorization' => config('test.token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];  

        $payload = \Innoboxrr\LaravelOptions\Models\Option::factory()->make()->getAttributes();

        $this->json('POST', '/api/innoboxrr/laraveloptions/option/create', $payload, $headers)
            ->assertStatus(201);
            
    }

    public function test_option_update_endpoint()
    {

        $option = \Innoboxrr\LaravelOptions\Models\Option::factory()->create();

        $headers = [
            'Authorization' => config('test.token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];  

        $payload = [
            ...\Innoboxrr\LaravelOptions\Models\Option::factory()->make()->getAttributes(),
            'option_id' => $option->id
        ];

        $this->json('PUT', '/api/innoboxrr/laraveloptions/option/update', $payload, $headers)
            ->assertStatus(200);
            
    }

    public function test_option_delete_endpoint()
    {

        $option = \Innoboxrr\LaravelOptions\Models\Option::latest()->first();

        $headers = [
            'Authorization' => config('test.token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];  

        $payload = [
            'option_id' => $option->id
        ];

        $this->json('DELETE', '/api/innoboxrr/laraveloptions/option/delete', $payload, $headers)
            ->assertStatus(200);
            
    }

    public function test_option_restore_endpoint()
    {

        $option = \Innoboxrr\LaravelOptions\Models\Option::first();

        $headers = [
            'Authorization' => config('test.token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];  

        $payload = [
            'option_id' => $option->id
        ];

        $this->json('POST', '/api/innoboxrr/laraveloptions/option/restore', $payload, $headers)
            ->assertStatus(200);
            
    }

    public function test_option_force_delete_endpoint()
    {

        $option = \Innoboxrr\LaravelOptions\Models\Option::latest()->first();

        $headers = [
            'Authorization' => config('test.token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];  

        $payload = [
            'option_id' => $option->id
        ];

        $this->json('DELETE', '/api/innoboxrr/laraveloptions/option/force-delete', $payload, $headers)
            ->assertStatus(403);
            
    }

    public function test_option_export_endpoint()
    {   

        $headers = [
            'Authorization' => config('test.token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];  

        $payload = [
            //
        ];

        $this->json('POST', '/api/innoboxrr/laraveloptions/option/export', $payload, $headers)
            ->assertStatus(200);
            
    }

}
