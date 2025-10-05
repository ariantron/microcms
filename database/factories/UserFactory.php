<?php

namespace Database\Factories;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = '123456';

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'mobile' => fake()->unique()->numerify('91########'),
            'password' => static::$password ??= Hash::make('password'),
            'profile_image_path' => $this->generateProfileImage(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Generate a profile image and return the path.
     */
    private function generateProfileImage(): ?string
    {
        try {
            $directory = 'profile-images';
            Storage::disk('public')->makeDirectory($directory);
            $filename = 'profile_' . Str::uuid()->toString() . '.jpg';
            $filePath = $directory . '/' . $filename;

            // Create a simple colored square image using GD
            $imageContent = $this->createSimpleAvatar();

            if ($imageContent) {
                Storage::disk('public')->put($filePath, $imageContent);
                return $filePath;
            }

            return null;
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Create a simple avatar image using GD library.
     */
    private function createSimpleAvatar(): ?string
    {
        try {
            // Create a 200x200 image
            $image = imagecreatetruecolor(200, 200);

            // Generate random colors
            $bgColor = imagecolorallocate($image,
                fake()->numberBetween(50, 200),
                fake()->numberBetween(50, 200),
                fake()->numberBetween(50, 200)
            );

            $textColor = imagecolorallocate($image, 255, 255, 255);

            // Fill background
            imagefill($image, 0, 0, $bgColor);

            // Add a simple circle
            $circleColor = imagecolorallocate($image,
                fake()->numberBetween(100, 255),
                fake()->numberBetween(100, 255),
                fake()->numberBetween(100, 255)
            );
            imagefilledellipse($image, 100, 100, 150, 150, $circleColor);

            // Add initials or a simple pattern
            $initials = strtoupper(substr(fake()->firstName(), 0, 1) . substr(fake()->lastName(), 0, 1));
            $fontSize = 5;
            $textX = 100 - (strlen($initials) * 10);
            $textY = 100;

            imagestring($image, $fontSize, $textX, $textY, $initials, $textColor);

            // Output as JPEG
            ob_start();
            imagejpeg($image, null, 90);
            $imageData = ob_get_contents();
            ob_end_clean();

            // Clean up
            imagedestroy($image);

            return $imageData;
        } catch (Exception) {
            return null;
        }
    }
}
