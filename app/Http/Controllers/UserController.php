<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileImageRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get user posts.
     */
    public function posts(User $user)
    {
        $response = $this->userService->getUserPosts($user);

        return $this->respond(
            $response,
            $response->success ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    /**
     * Update user profile image.
     */
    public function updateProfileImage(UpdateProfileImageRequest $request)
    {
        $user = $request->user();
        $file = $request->file('profile_image');
        $response = $this->userService->updateProfileImage($user, $file);

        return $this->respond(
            $response,
            $response->success ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    /**
     * Delete user profile image.
     */
    public function deleteProfileImage(Request $request)
    {
        $user = $request->user();
        $response = $this->userService->deleteProfileImage($user);

        return $this->respond(
            $response,
            $response->success ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
