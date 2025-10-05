<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Get paginated posts.
     */
    public function index(Request $request)
    {
        // Extract pagination parameters using helper
        ['per_page' => $perPage, 'page' => $page] = PaginationHelper::getPaginationFromRequest($request);

        $response = $this->postService->getPosts($perPage, $page);

        return $this->respond(
            $response,
            $response->success ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    /**
     * Get a single post.
     */
    public function show(Post $post, Request $request)
    {
        $response = $this->postService->getPost($post, $request->ip());

        return $this->respond(
            $response,
            $response->success ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
