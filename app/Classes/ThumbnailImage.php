<?php

namespace App\Classes;

use \App\Models\ThumbnailImage as Image;
use Imagick;

class ThumbnailImage
{
    public $image;
    public $imageLocation;

    public function __construct($image)
    {
        $this->image = $image;
    }

    public function crop($width, $height)
    {
        $this->image->cropThumbnailImage($width, $height);
    }

    public function compress($quality)
    {
        $this->image->setImageCompression(true);

        $this->image->setImageCompression(Imagick::COMPRESSION_JPEG);

        $this->image->setImageCompressionQuality($quality);
    }

    public function imageBackground($format, $color)
    {
        $this->image->setImageFormat($format);

        $this->image->setImageBackgroundColor($color);

        $this->image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);

        $this->image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
    }

    public function saveImage($post, $picture)
    {
        $db = new Image;

        $db->post()->associate($post);

        $db->picture()->associate($picture);

        $db->filename = $this->image->store(config('pictures.local.ads') . '/' . $post->id. '/' . config('pictures.thumbnail_image.group_name'), config('pictures.thumbnail_image.visibility'));

        $db->mime_type = $this->image->getMimeType();

        $db->save();

        // Adjust Thumbnail image

        $db_width = config('pictures.thumbnail_image.width');

        $db_height = config('pictures.thumbnail_image.height');

        $this->imageLocation = config('pictures.thumbnail_image.image_location') . '/' . $db->filename;

        $this->image = new Imagick();

        $this->image->readImage($this->imageLocation);

        $this->crop($db_width, $db_height);

        $this->imageBackground('jpeg', 'white');

        $this->compress(60);

        // Save image

        $this->image->writeImage($this->imageLocation);

    }

}