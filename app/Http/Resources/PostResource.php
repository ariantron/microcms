<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Properties to exclude from the user when included in posts.
     */
    protected static array $excludedUserProperties = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'view_count' => $this->view_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'user' => $this->whenLoaded('user', function () {
                return $this->getFilteredUserData();
            }),
            'views' => $this->whenLoaded('views', function () {
                return PostViewResource::collection($this->views);
            }),
            'total_views' => $this->whenLoaded('views', fn() => $this->views_count),
            'unique_views' => $this->whenLoaded('views', fn() => $this->unique_views),
        ];
    }

    /**
     * Get filtered user data excluding specified properties.
     */
    protected function getFilteredUserData(): array
    {
        $userData = $this->user->toArray();

        // Remove excluded properties
        foreach (static::$excludedUserProperties as $property) {
            unset($userData[$property]);
        }

        // Add computed properties
        $userData['profile_image_url'] = $this->user->profile_image_path
            ? asset('storage/' . $this->user->profile_image_path)
            : null;

        return $userData;
    }
}
