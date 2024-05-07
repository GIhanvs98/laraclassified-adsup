<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!option_exists('allow-image-compression')) {

            option(['allow-image-compression' => true]);
        }

        if (!option_exists('allow-image-watermark')) {

            option(['allow-image-watermark' => true]);
        }

        if (!option_exists('allow-otp-verification')) {

            option(['allow-otp-verification' => true]);
        }

        if (!option_exists('allow-guest-ads')) {

            option(['allow-guest-ads' => false]);
        }
    }
}
