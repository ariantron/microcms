<?php

namespace App\Services;

use App\Helpers\PaginationHelper;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostView;
use App\Responses\ServiceResponse;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PostService
{
    /**
     * Get paginated posts.
     */
    public function getPosts(int $perPage = 12, int $page = 1): ServiceResponse
    {
        try {
            // Validate pagination parameters using helper
            ['per_page' => $perPage, 'page' => $page] = PaginationHelper::validatePagination($perPage, $page);

            // Get paginated posts with user relationship
            $posts = Post::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(
                    perPage: $perPage,
                    page: $page,
                );

            // Create response data using helper
            $responseData = PaginationHelper::createPaginationResponse($posts, 'posts');
            $responseData['posts'] = PostResource::collection($posts);

            return ServiceResponse::success($responseData);
        } catch (Exception $e) {
            $message = 'Failed to retrieve posts';
            Log::error($message, [
                'exception' => $e->getMessage(),
            ]);
            return ServiceResponse::failed([$message]);
        }
    }

    /**
     * Get a single post.
     */
    public function getPost(Post $post, string $ip): ServiceResponse
    {
        try {
            DB::beginTransaction();
            $postView = PostView::where('post_id', $post->id)
                ->where('ip_address', $ip)
                ->first();
            if (!$postView) {
                PostView::create([
                    'post_id' => $post->id,
                    'ip_address' => $ip,
                ]);
                $post->incrementViewCount();
            }
            DB::commit();
            return ServiceResponse::success(['post' => new PostResource($post)]);
        } catch (Exception|Throwable $e) {
            $message = 'Failed to retrieve post';
            Log::error(
                $message,
                [
                    'post_id' => $post->id,
                    'ip_address' => $ip,
                    'exception' => $e->getMessage(),
                ]
            );
            return ServiceResponse::failed([$message]);
        }
    }
}
