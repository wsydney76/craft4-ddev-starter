<?php

namespace modules\main\helpers;

use Craft;
use craft\fs\Local;
use Exception;
use function count;
use function glob;
use function is_dir;
use function is_readable;
use function rmdir;
use function scandir;
use const DIRECTORY_SEPARATOR;

/**
 * Class FileHelper
 * This class extends Craft's FileHelper and adds functionality for file cleanup.
 *
 * @package modules\main\helpers
 */
class FileHelper extends \craft\helpers\FileHelper
{
    /**
     * Function to clean up transform directories.
     * It goes through all the volumes and checks if they are local.
     * If local, it proceeds to remove any empty subfolders in them.
     *
     * @return void
     */
    public static function cleanupTransformDirectories(): void
    {
        // Get all volumes
        $volumes = Craft::$app->volumes->getAllVolumes();

        // Iterate over all volumes
        foreach ($volumes as $volume) {
            // Get filesystem object for this volume
            /** @var Local $fs */
            $fs = $volume->getTransformFs();

            // Check if the filesystem is of type Local
            if ($fs instanceof Local) {
                // Get root path
                $path = $fs->rootPath;
                // Append the transform subpath if it exists
                if ($volume->transformSubpath) {
                    $path .= DIRECTORY_SEPARATOR . $volume->transformSubpath;
                }

                try {
                    // Remove empty subfolders for the path
                    static::removeEmptySubFolders($path);
                } catch (Exception $e) {
                    // Log error message
                    Craft::error($e->getMessage(), 'main');
                }
            }
        }
    }

    /**
     * Function to recursively remove empty subfolders for a given path.
     * Source: https://stackoverflow.com/questions/1833518/remove-empty-subfolders-with-php
     *
     * @param string $path The base path for which to remove empty subfolders.
     * @return bool True if the folder is empty and removed, false otherwise.
     */
    public static function removeEmptySubFolders($path): bool
    {
        // Start by assuming the directory is empty
        $empty = true;

        // Iterate over all entities in the directory
        foreach (glob($path . DIRECTORY_SEPARATOR . "*") as $file) {
            // Check if entity is a directory, if so recursively check it
            $empty &= is_dir($file) && static::removeEmptySubFolders($file);
        }

        // Check if directory is empty (only '.', '..' remain) and remove it if so
        return $empty && (is_readable($path) && count(scandir($path)) == 2) && rmdir($path);
    }
}
