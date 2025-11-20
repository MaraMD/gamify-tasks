<?php

namespace App\Support;

class Avatar
{
    /**
     * Get the absolute base path for avatar assets.
     *
     * @return string
     */
    public static function base(): string
    {
        return public_path(trim(config('avatar.base_path'), '/'));
    }

    /**
     * Get the first valid asset file in a layer folder.
     *
     * Returns relative path to base_path (e.g., 'skin/skin_01.png')
     * or null if no files found.
     *
     * Uses the folder mapping from config to find the actual Kenney pack folder.
     *
     * @param string $layer Logical layer name (body, eyes, hair, top, bottom, acc)
     * @return string|null Relative path to asset or null
     */
    public static function firstIn(string $layer): ?string
    {
        // Get the actual folder name from the mapping
        $folder = config("avatar.folders.$layer", $layer);
        $dir = self::base() . DIRECTORY_SEPARATOR . $folder;

        if (!is_dir($dir)) {
            return null;
        }

        // Look for PNG files first, then SVG
        $files = glob($dir . '/*.{png,svg}', GLOB_BRACE);

        if (!$files || empty($files)) {
            return null;
        }

        // Return relative path to base_path (e.g., 'skin/skin_01.png')
        return $folder . '/' . basename($files[0]);
    }

    /**
     * Get all available asset files for a specific layer.
     *
     * Returns an array of relative paths (e.g., ['skin/tint1_head.png', 'skin/tint1_neck.png'])
     *
     * @param string $layer Logical layer name (body, eyes, hair, top, bottom, acc)
     * @return array Array of relative paths to assets
     */
    public static function allIn(string $layer): array
    {
        $folder = config("avatar.folders.$layer", $layer);
        $dir = self::base() . DIRECTORY_SEPARATOR . $folder;

        if (!is_dir($dir)) {
            return [];
        }

        $files = glob($dir . '/*.{png,svg}', GLOB_BRACE);

        if (!$files || empty($files)) {
            return [];
        }

        // Map to relative paths
        return array_map(function($file) use ($folder) {
            return $folder . '/' . basename($file);
        }, $files);
    }

    /**
     * Get a friendly display name for a file.
     *
     * @param string $filename Full filename with extension
     * @return string Display-friendly name
     */
    public static function displayName(string $filename): string
    {
        // Remove extension and clean up the name
        $name = pathinfo($filename, PATHINFO_FILENAME);

        // Convert underscores to spaces and capitalize
        $name = str_replace('_', ' ', $name);
        $name = ucwords($name);

        return $name;
    }
}
