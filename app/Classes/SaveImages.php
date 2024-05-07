<?php

namespace App\Classes;

use App\Models\Picture;
use Kenura\Imagick\ImageProcessor;


class SaveImages
{

    public $post;

    public array $selectedImages = [];

    public function __construct(object $post)
    {
        $this->post = $post;
    }

    public static function process(object $post, array $order, array $new, array $old)
    {

        // Create an instance of the class
        $instance = new self($post);

        // Call the instance method
        $instance->loop($order, $new, $old);

        // Delete unselected images. Only related to old images.
        $instance->deleteImages($order);
    }

    public function loop(array $order, array $new, array $old)
    {

        if (!empty($order) && (!empty($new) || !empty($old))) {

            foreach ($order as $key => $imageToken) {

                # $imageToken = ['type' => 'old' | 'new', 'key' => 'old:sequance_of_images' | 'new:file_key' ]

                if ($imageToken['type'] == "new") {

                    # new:file_key - this is from the js `FileList` object.

                    $fileKey = $imageToken['key'];

                    $newImage = $new[$fileKey];

                    $this->newImg($newImage, $key);
                }

                if ($imageToken['type'] == "old") {

                    # old:sequance_of_images

                    $sequanceOfImage = $imageToken['key'];

                    $oldImage = $old[$sequanceOfImage];

                    $this->oldImg($oldImage, $key);
                }

            }

        }

    }

    public function newImg($image, string $position)
    {
        /*

        1) Upload slide image

        2) Store slide image data in db

        3) Upload thumbnail image

        4) Store thumbnail image data in db

        */

        # Store slide image data in db

        // Save in databse and store in filestorage a basic copy.

        $db = new Picture;

        $db->post()->associate($this->post);

        $db->filename = $image->store(config('pictures.local.ads') . '/' . $this->post->id, 'public');

        $db->mime_type = $image->getMimeType();

        $db->position = $position;

        $db->save();

        # Upload slide image

        // -------------------------------------------------------------------------------

        // Adjust Image

        $processor = new ImageProcessor();

        // ------------------- Compress parameters -----------------------

        $maxSize = config('pictures.default.max_size');

        $quality = config('pictures.default.quality');

        // ------------------- Resize parameters -----------------------

        $maxWidth = config('pictures.default.width');

        $maxHeight = config('pictures.default.height');

        // ------------------- Watermark parameters -----------------------

        // diagonal lenght of the wallpaper as a percentage of the parent.
        $scalePercent = config('pictures.watermark.scale');

        // Position of the watermark.
        $watermarkPosition = config('pictures.watermark.position');

        // Watermark path.
        $watermarkPath = config('pictures.watermark.location');

        // ------------------- File paths -----------------------

        // Read file path
        $filePath = config('pictures.default.image_location') . '/' . $db->filename;

        // Resize the image and save to the output folder
        $processor->resizeImage($filePath, $filePath, $maxWidth, $maxHeight);

        $watermark_images = option('allow-image-watermark', true) ? 'true' : 'false';
        $compress_images = option('allow-image-compression', true) ? 'true' : 'false';

        if (auth()->check()) {

            $watermark_images = auth()->user()->watermark_images;
            $compress_images = auth()->user()->compress_images;

            if ($watermark_images === "system_default") {
                $watermark_images = option('allow-image-watermark', true) ? 'true' : 'false';
            }

            if ($compress_images === "system_default") {
                $compress_images = option('allow-image-compression', true) ? 'true' : 'false';
            }
        }

        if ($watermark_images === 'true') {
            // Add watermark.
            $processor->addWatermark($filePath, $filePath, $watermarkPath, $watermarkPosition, $scalePercent);
        }

        if ($compress_images === 'true') {
            // Compress the image to JPG and save to the output folder
            $processor->compressToJpg($filePath, $filePath, $maxSize, $quality);
        }

        // -------------------------------------------------------------------------------

        # Upload thumbnail image

        # Store thumbnail image data in db

        // Storing the thumpnail image.

        $setThumbnailImage = new ThumbnailImage($image);

        $setThumbnailImage->saveImage($this->post, $db);

        // Store picture id.
        $this->selectedImages[] = $db->id;
    }

    public function oldImg($image, string $position)
    {

        /*

        1) Get slide image data in db

        */

        # Get slide image data in db

        Picture::find($image['id'])->update(['position' => $position]);

        $this->selectedImages[] = $image['id'];
    }

    public function deleteImages(array $order)
    {

        if ($this->post->pictures()->exists()) {

            Picture::whereBelongsTo($this->post, 'post')->whereNotIn('id', $this->selectedImages)->delete();
        }
    }

}
