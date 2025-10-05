<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    $this->posts = Post::factory()->count(3)->create(['user_id' => $this->user->id]);
    
    // Create a JWT token for authentication using AuthService
    $authService = app(\App\Services\AuthService::class);
    $this->token = $authService->generateToken($this->user);
});

it('can get user posts with valid UUID', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson("/api/users/{$this->user->id}/posts");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'name',
                'mobile',
                'total_posts',
                'total_views',
                'posts' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'view_count',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]
        ])
        ->assertJson(['success' => true]);
});

it('returns 404 for non-existent user posts', function () {
    $fakeUuid = '01234567-89ab-7def-0123-456789abcdef';
    
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson("/api/users/{$fakeUuid}/posts");

    $response->assertStatus(404);
});

it('can update profile image with valid image file', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->image('profile.jpg', 200, 200)->size(100); // 100KB
    
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/profile/image', [
        'profile_image' => $file
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data'
        ])
        ->assertJson(['success' => true]);
    
    // Verify file was stored (check if any file exists in the directory)
    $files = Storage::disk('public')->files('profile-images');
    expect($files)->not->toBeEmpty();
});

it('validates profile image file type', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->create('document.pdf', 1000);
    
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/profile/image', [
        'profile_image' => $file
    ]);

    $response->assertStatus(200)
        ->assertJson(['success' => false])
        ->assertJsonStructure(['errors' => ['profile_image']]);
});

it('validates profile image file size', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->image('large.jpg')->size(6000); // 6MB (exceeds 5MB limit)
    
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/profile/image', [
        'profile_image' => $file
    ]);

    $response->assertStatus(200)
        ->assertJson(['success' => false])
        ->assertJsonStructure(['errors' => ['profile_image']]);
});

it('can delete profile image', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->deleteJson('/api/profile/image');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data'
        ])
        ->assertJson(['success' => true]);
});

it('requires authentication for protected endpoints', function () {
    $response = $this->getJson("/api/users/{$this->user->id}/posts");
    $response->assertStatus(401);
    
    $response = $this->postJson('/api/profile/image');
    $response->assertStatus(401);
    
    $response = $this->deleteJson('/api/profile/image');
    $response->assertStatus(401);
});
