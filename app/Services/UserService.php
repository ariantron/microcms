<?php

namespace App\Services;

use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Responses\ServiceResponse;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Update user profile image.
     */
    public function updateProfileImage(User $user, UploadedFile $file): ServiceResponse
    {
        try {
            // Delete old profile image if exists
            $this->deleteOldProfileImage($user);

            // Generate unique filename
            $filename = $this->generateFilename($user, $file);
            $directory = 'profile-images';
            $filePath = $directory . '/' . $filename;

            // Create directory if it doesn't exist
            Storage::disk('public')->makeDirectory($directory);

            // Store the file
            Storage::disk('public')->put($filePath, file_get_contents($file->getPathname()));

            // Update user profile image path
            $user->update(['profile_image_path' => $filePath]);

            return ServiceResponse::success([
                'message' => 'Profile image updated successfully'
            ]);
        } catch (Exception $e) {
            $message = 'Failed to update profile image';
            Log::error($message, [
                'user_id' => $user->id,
                'exception' => $e->getMessage(),
            ]);
            return ServiceResponse::failed([$message]);
        }
    }

    /**
     * Delete old profile image from storage.
     */
    private function deleteOldProfileImage(User $user): void
    {
        if ($user->profile_image_path && Storage::disk('public')->exists($user->profile_image_path)) {
            Storage::disk('public')->delete($user->profile_image_path);
        }
    }

    /**
     * Generate unique filename for profile image.
     */
    private function generateFilename(User $user, UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->timestamp;
        $randomString = Str::random(8);

        return "profile_{$user->id}_{$timestamp}_{$randomString}.{$extension}";
    }

    /**
     * Delete user profile image.
     */
    public function deleteProfileImage(User $user): ServiceResponse
    {
        try {
            $this->deleteOldProfileImage($user);
            $user->update(['profile_image_path' => null]);

            return ServiceResponse::success([
                'message' => 'Profile image deleted successfully'
            ]);
        } catch (Exception) {
            $message = 'Failed to delete profile image';
            Log::error($message, [
                'user_id' => $user->id,
            ]);
            return ServiceResponse::failed([$message]);
        }
    }

    /**
     * Get user posts.
     */
    public function getUserPosts(User $user): ServiceResponse
    {
        try {
            $user->load('posts');

            return ServiceResponse::success(new UserResource($user));
        } catch (Exception $e) {
            $message = 'Failed to retrieve user posts';
            Log::error($message, [
                'user_id' => $user->id,
                'exception' => $e->getMessage(),
            ]);
            return ServiceResponse::failed([$message]);
        }
    }
}
