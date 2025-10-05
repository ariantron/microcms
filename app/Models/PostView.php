<?php

namespace App\Models;

use Database\Factories\PostViewFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $post_id
 * @property string $ip_address
 * @property string $viewed_at
 * @method static PostViewFactory factory($count = null, $state = [])
 * @method static Builder<static>|PostView newModelQuery()
 * @method static Builder<static>|PostView newQuery()
 * @method static Builder<static>|PostView query()
 * @method static Builder<static>|PostView whereId($value)
 * @method static Builder<static>|PostView whereIpAddress($value)
 * @method static Builder<static>|PostView wherePostId($value)
 * @method static Builder<static>|PostView whereViewedAt($value)
 * @property-read Post $post
 * @mixin Eloquent
 */
class PostView extends BaseModel
{
    /** @use HasFactory<PostViewFactory> */
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'post_id',
        'ip_address',
        'viewed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }

    /**
     * Get the post that owns the view.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
