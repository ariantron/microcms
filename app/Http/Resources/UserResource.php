<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'profile_image_path' => $this->profile_image_path,
            'profile_image_url' => $this->profile_image_path ? asset('storage/' . $this->profile_image_path) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'total_posts' => $this->whenLoaded('posts', fn() => $this->total_posts),
            'total_views' => $this->whenLoaded('posts', fn() => $this->total_views),
            'posts' => $this->whenLoaded('posts', function () {
                return PostResource::collection($this->posts);
            }),
        ];
    }
}
