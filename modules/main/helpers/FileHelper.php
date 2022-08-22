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

class FileHelper extends \craft\helpers\FileHelper
{

    public static function cleanupTransformDirectories(): void
    {
        $volumes = Craft::$app->volumes->getAllVolumes();

        foreach ($volumes as $volume) {
            /** @var Local $fs */
            $fs = $volume->getTransformFs();
            if ($fs instanceof Local) {
                $path = $fs->rootPath;
                if ($volume->transformSubpath) {
                    $path .= DIRECTORY_SEPARATOR . $volume->transformSubpath;
                }

                try {
                    static::removeEmptySubFolders($path);
                } catch (Exception $e) {
                    Craft::error($e->getMessage(), 'main');
                }
            }
        }
    }

    // https://stackoverflow.com/questions/1833518/remove-empty-subfolders-with-php
    public static function removeEmptySubFolders($path): bool
    {
        $empty = true;
        foreach (glob($path . DIRECTORY_SEPARATOR . "*") as $file) {
            $empty &= is_dir($file) && static::removeEmptySubFolders($file);
        }
        return $empty && (is_readable($path) && count(scandir($path)) == 2) && rmdir($path);
    }

}
