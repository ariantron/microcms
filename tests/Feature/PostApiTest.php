<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->post = Post::factory()->create(['user_id' => $this->user->id]);
    
    // Create a JWT token for authentication
    $authService = app(\App\Services\AuthService::class);
    $this->token = $authService->generateToken($this->user);
});

it('can show a post with valid UUID', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson("/api/posts/{$this->post->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'post' => [
                    'id',
                    'title',
                    'content',
                    'view_count',
                    'created_at',
                    'updated_at'
                ]
            ]
        ])
        ->assertJson(['success' => true]);
});

it('returns 404 for non-existent post', function () {
    $fakeUuid = '01234567-89ab-7def-0123-456789abcdef';
    
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson("/api/posts/{$fakeUuid}");

    $response->assertStatus(404);
});

it('returns 404 for invalid UUID format', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson('/api/posts/invalid-uuid');

    $response->assertStatus(404);
});

it('increments view count when post is viewed', function () {
    $initialViewCount = $this->post->view_count;
    
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson("/api/posts/{$this->post->id}");
    
    $response->assertStatus(200);
    
    $this->post->refresh();
    expect($this->post->view_count)->toBe($initialViewCount + 1);
});

it('tracks unique IP addresses for post views', function () {
    // First view from IP 1
    $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson("/api/posts/{$this->post->id}", [
        'REMOTE_ADDR' => '192.168.1.1'
    ]);
    
    // Second view from same IP (should not increment unique views)
    $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson("/api/posts/{$this->post->id}", [
        'REMOTE_ADDR' => '192.168.1.1'
    ]);
    
    // View from different IP (should increment unique views)
    $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson("/api/posts/{$this->post->id}", [
        'REMOTE_ADDR' => '192.168.1.2'
    ]);
    
    $this->post->refresh();
    expect($this->post->unique_views)->toBe(2);
});
