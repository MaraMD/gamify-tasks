<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class AvatarInventoryService
{
    /**
     * Get all available avatar assets organized by layer.
     *
     * @return array
     */
    public function all(): array
    {
        $basePath = public_path(trim(config('avatar.base_path'), '/'));
        $folders = self::folders();
        $inventory = [];

        foreach ($folders as $layer => $folder) {
            $inventory[$layer] = $this->getAssetsForFolder($basePath, $folder, $layer);
        }

        return $inventory;
    }

    /**
     * Get the mapping of layers to actual folder names.
     *
     * @return array
     */
    public static function folders(): array
    {
        return [
            'skin' => 'skin',
            'face' => 'face',
        ];
    }

    /**
     * Get assets for a specific folder.
     *
     * @param string $basePath
     * @param string $folder
     * @param string $layer
     * @return array
     */
    protected function getAssetsForFolder(string $basePath, string $folder, string $layer): array
    {
        $folderPath = $basePath . DIRECTORY_SEPARATOR . $folder;
        $assets = [];

        if (!is_dir($folderPath)) {
            return $assets;
        }

        $files = File::files($folderPath);

        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());
            $filename = $file->getFilename();

            if (in_array($extension, ['png', 'svg'])) {
                // Filter: for 'skin' layer, only include files with '_head' in the name
                if ($layer === 'skin' && !str_contains($filename, '_head')) {
                    continue;
                }

                $relativePath = $folder . '/' . $filename;
                $assets[] = [
                    'key' => $relativePath,
                    'url' => asset(trim(config('avatar.base_path'), '/') . '/' . $relativePath),
                    'label' => $this->generateLabel($folder, $file->getFilenameWithoutExtension()),
                ];
            }
        }

        return $assets;
    }

    /**
     * Generate a human-readable label from folder and filename.
     *
     * @param string $folder
     * @param string $filename
     * @return string
     */
    protected function generateLabel(string $folder, string $filename): string
    {
        // Special handling for skin tints
        if ($folder === 'skin' && preg_match('/^tint(\d+)_head$/', $filename, $matches)) {
            return 'Tono de Piel ' . $matches[1];
        }

        // Convert underscores to spaces
        $name = str_replace('_', ' ', $filename);

        // Capitalize words
        $name = ucwords($name);

        return $name;
    }
}
