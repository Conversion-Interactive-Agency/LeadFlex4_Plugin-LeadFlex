<?php

namespace conversionia\leadflex\helpers;

use conversionia\leadflex\Leadflex;

/**
 * Common Formatting Utility Functions
 */
class SubmissionHelper
{
    // Public Methods
    // =========================================================================
    /**
     * Removes all non-numeric characters and leading "1" (if it exists).
     *
     * @param string $phone
     * @return string
     */
    public static function cleanPhone(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^\d]/', '', $phone);

        // If longer than 10 digits
        if (strlen($phone) > 10) {
            // Remove leading "1" (if it exists)
            $phone = preg_replace('/^1?/', '', $phone);
        }
        // Return clean phone number
        return $phone;
    }
}
