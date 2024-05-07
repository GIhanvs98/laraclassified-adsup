<?php

namespace App\Classes;

use App\Models\Picture;
use Imagick;

class Image
{
    public $image;

    public $imageLocation;

    public $imageWidth;
    public $imageHeight;

    public $watermarkWidth;
    public $watermarkHeight;

    public $dbImageId = null;

    public function __construct($image)
    {
        $this->image = $image;
    }

    public function resize($width, $height)
    {
        $width = config('pictures.default.width');

        $height = config('pictures.default.height');

        $AspectRatio = $width / $height;

        $currentR = $this->imageWidth / $this->imageHeight;

        if ($currentR <= $AspectRatio) {

            $this->image->scaleImage($currentR * $width, $height, Imagick::FILTER_LANCZOS);
        } else {

            $this->image->scaleImage($width, $width / $currentR, Imagick::FILTER_LANCZOS);
        }
    }

    public function imageBackground($format, $color)
    {
        $this->image->setImageFormat($format);

        $this->image->setImageBackgroundColor($color);

        $this->image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);

        $this->image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
    }

    public function compress($quality)
    {
        $size = $this->image->getImageSize() / 1024; // In KB

        if ($size > 100) {

            $this->image->setImageCompression(true);

            $this->image->setImageCompression(Imagick::COMPRESSION_JPEG);

            $this->image->setImageCompressionQuality($quality);

        }
    }

    public function addWatermark($watermarkLocation)
    {

        $this->image = new Imagick();

        $this->image->readImage($this->imageLocation);

        $watermark = new Imagick();

        $watermark->readImage($watermarkLocation);

        $this->imageWidth = $this->image->getImageWidth();

        $this->imageHeight = $this->image->getImageHeight();

        $watermarkWidth = $watermark->getImageWidth();

        $watermarkHeight = $watermark->getImageHeight();

        if ($this->imageHeight < $watermarkHeight || $this->imageWidth < $watermarkWidth) {

            // resize the watermark
            $watermark->scaleImage($this->imageWidth * 3 / 2, $this->imageHeight * 3 / 2);

            // new watermark width
            $watermarkWidth = $watermark->getImageWidth();

            // new watermark height
            $watermarkHeight = $watermark->getImageHeight();
        }

        // calculate the position
        $x = ($this->imageWidth - $watermarkWidth) / 2;

        $y = ($this->imageHeight - $watermarkHeight) / 2;

        $this->image->compositeImage($watermark, Imagick::COMPOSITE_OVER, $x, $y);

        $this->image->writeImage($this->imageLocation);
    }

    public function save($post, $position)
    {
        $db = new Picture;

        $db->post()->associate($post);

        $db->filename = $this->image->store(config('pictures.local.ads') . '/' . $post->id, 'public');

        $db->mime_type = $this->image->getMimeType();

        $db->position = $position;

        $db->save();

        $this->dbImageId = $db->id;

        // Adjust Image

        $this->image = new Imagick();

        $this->imageLocation = config('pictures.default.image_location') . '/' . $db->filename;

        $this->image->readImage($this->imageLocation);

        $this->imageWidth = $this->image->getImageWidth();

        $this->imageHeight = $this->image->getImageHeight();

        $this->resize(config('pictures.default.width'), config('pictures.default.height'));

        $this->imageBackground('jpeg', 'white');

        $this->compress(60);

        // Save changes to the image

        $this->image->writeImage($this->imageLocation);
    }


}