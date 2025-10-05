<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'mobile' => '9123456789',
        'password' => bcrypt('password123'),
    ]);
});

it('can login with valid credentials', function () {
    $response = $this->postJson('/api/auth/login', [
        'mobile' => '9123456789',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'user',
                'token'
            ]
        ])
        ->assertJson(['success' => true]);
});

it('fails to login with invalid credentials', function () {
    $response = $this->postJson('/api/auth/login', [
        'mobile' => '9123456789',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJson(['success' => false]);
});

it('fails to login with non-existent mobile', function () {
    $response = $this->postJson('/api/auth/login', [
        'mobile' => '9123456789',
        'password' => 'password123',
    ]);

    $response->assertStatus(401)
        ->assertJson(['success' => false]);
});

it('validates required fields for login', function () {
    $response = $this->postJson('/api/auth/login', []);

    $response->assertStatus(200)
        ->assertJson(['success' => false])
        ->assertJsonStructure(['errors' => ['mobile', 'password']]);
});

it('validates mobile number format', function () {
    $response = $this->postJson('/api/auth/login', [
        'mobile' => 'invalid-mobile',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJson(['success' => false])
        ->assertJsonStructure(['errors' => ['mobile']]);
});

it('validates password minimum length', function () {
    $response = $this->postJson('/api/auth/login', [
        'mobile' => '9123456789',
        'password' => '123',
    ]);

    $response->assertStatus(200)
        ->assertJson(['success' => false])
        ->assertJsonStructure(['errors' => ['password']]);
});
