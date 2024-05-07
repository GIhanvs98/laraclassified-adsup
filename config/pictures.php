<?php

return [

    'local' => [
        'shops' => 'shops',
        'ads' => 'files/lk' # Do not change this. Chnage in here only applied to modified pages.
    ],
    'default' => [
        'image_location' => 'storage',
        'width' => 620,
        'height' => 466,
        'max_size' => 100, # In KB
        'quality' => 80,
    ],
    'watermark' => [

        // diagonal lenght of the wallpaper as a percentage of the parent.
        'scale'=> 40,

        // Position of the watermark on the parent image.
        'position' => 'center',

        // Watermark file location.
        'location' => 'images/watermark.png',
    ],
    'thumbnail_image' => [
        'image_location' => 'storage',
        'group_name' => 'thumbnail-images',
        'width' => 142,
        'height' => 108,
        'visibility' => 'public',
    ],

];

