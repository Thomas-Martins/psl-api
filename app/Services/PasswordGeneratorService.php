<?php

namespace App\Services;

class PasswordGeneratorService
{
    /**
     * Generate a secure random password
     *
     * @param int $length Length of the password
     * @return string
     */
    public function generate(int $length = 12): string
    {
        // Ensure minimum length for security
        $length = max(8, $length);

        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $specialChars = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $password = '';
        
        // Ensure at least one character from each set
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $specialChars[random_int(0, strlen($specialChars) - 1)];

        // Fill the rest with random characters
        $allChars = $uppercase . $lowercase . $numbers . $specialChars;
        $remainingLength = $length - strlen($password);

        for ($i = 0; $i < $remainingLength; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password to make it more random
        return str_shuffle($password);
    }
} 
