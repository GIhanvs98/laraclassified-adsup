<?php

namespace App\Console\Commands;

use App\Models\Picture;
use App\Models\ThumbnailImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupOrphanedRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:orphans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove thumbnail-images without pictures and pictures without posts';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        try {

            // Remove Pictures without Posts
            $picturesWithoutPosts = Picture::whereDoesntHave('post')->get();
            $this->deleteRecords($picturesWithoutPosts);

            // Remove ThumbnailImages without Pictures
            $thumbnailImagesWithoutPictures = ThumbnailImage::whereDoesntHave('picture')->get();
            $this->deleteRecords($thumbnailImagesWithoutPictures);

            $directoryPath = storage_path('app/public/'.config('pictures.local.ads'));
            $this->removeEmptyFolders($directoryPath);

            $this->info('Orphaned records cleanup complete.');

        } catch (\Throwable $th) {
            // Return error.

            $this->error('Cleanup Error: ' . $th->getMessage());
        }
    }

    private function deleteRecords($records)
    {
        foreach ($records as $record) {

            // Delete file from storage
            $path = $record->filename;

            // Assuming $path contains something like 'app/public/uploads/images/example.jpg'
            $filePath = storage_path(config('pictures.default.image_location') . '/' . $path);

            if ($path && File::exists($filePath)) {
                unlink($filePath);
            }

            // Delete the record from the database
            $record->delete();
        }
    }

    private function removeEmptyFolders($directory)
    {
        $directories = glob($directory . '/*', GLOB_ONLYDIR);

        foreach ($directories as $dir) {
            $this->removeEmptyFolders($dir);
        }

        if (count(glob($directory . '/*')) === 0) {
            rmdir($directory);
        }
    }
}
