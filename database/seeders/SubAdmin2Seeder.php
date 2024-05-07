<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubAdmin2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
                    [1, 'LK.38.43', 'LK', 'LK.38', '{"en":"Vavuniya District"}', 5, 1],
                    [2, 'LK.37.53', 'LK', 'LK.37', '{"en":"Trincomale District"}', 8, 1],
                    [3, 'LK.33.91', 'LK', 'LK.33', '{"en":"Ratnapura District"}', NULL, 1],
                    [4, 'LK.32.62', 'LK', 'LK.32', '{"en":"Puttalam District"}', 11, 1],
                    [5, 'LK.30.72', 'LK', 'LK.30', '{"en":"Polonnaruwa District"}', 12, 1],
                    [6, 'LK.29.23', 'LK', 'LK.29', '{"en":"Nuwara Eliya District"}', 17, 1],
                    [7, 'LK.38.44', 'LK', 'LK.38', '{"en":"Mullaitivu District"}', NULL, 1],
                    [8, 'LK.35.82', 'LK', 'LK.35', '{"en":"Moneragala District"}', NULL, 1],
                    [9, 'LK.34.32', 'LK', 'LK.34', '{"en":"Matara District"}', 24, 1],
                    [10, 'LK.29.22', 'LK', 'LK.29', '{"en":"Matale District"}', 25, 1],
                    [11, 'LK.38.42', 'LK', 'LK.38', '{"en":"Mannar District"}', NULL, 1],
                    [12, 'LK.32.61', 'LK', 'LK.32', '{"en":"Kurunegala District"}', 27, 1],
                    [13, 'LK.38.45', 'LK', 'LK.38', '{"en":"Kilinochchi District"}', 32, 1],
                    [14, 'LK.33.92', 'LK', 'LK.33', '{"en":"Kegalle District"}', 34, 1],
                    [15, 'LK.29.21', 'LK', 'LK.29', '{"en":"Kandy District"}', 37, 1],
                    [16, 'LK.36.13', 'LK', 'LK.36', '{"en":"Kalutara District"}', 39, 1],
                    [17, 'LK.38.41', 'LK', 'LK.38', '{"en":"Jaffna District"}', 41, 1],
                    [18, 'LK.34.33', 'LK', 'LK.34', '{"en":"Hambantota District"}', NULL, 1],
                    [19, 'LK.36.12', 'LK', 'LK.36', '{"en":"Gampaha District"}', 49, 1],
                    [20, 'LK.34.31', 'LK', 'LK.34', '{"en":"Galle District"}', 50, 1],
                    [21, 'LK.36.11', 'LK', 'LK.36', '{"en":"Colombo District"}', 54, 1],
                    [22, 'LK.37.51', 'LK', 'LK.37', '{"en":"Batticaloa District"}', 58, 1],
                    [23, 'LK.35.81', 'LK', 'LK.35', '{"en":"Badulla District"}', 60, 1],
                    [24, 'LK.30.71', 'LK', 'LK.30', '{"en":"Anuradhapura District"}', 61, 1],
                    [25, 'LK.37.52', 'LK', 'LK.37', '{"en":"Ampara District"}', 62, 1],
                ];

        foreach ($data as $record) {
            $id = $record[0];
            $code = $record[1];
            $country_code = $record[2];
            $subadmin1_code = $record[3];
            $name = $record[4];
            $district_id_city = $record[5];
            $active = $record[6];

            DB::table('subadmin2')->where('id', $id)->updateOrInsert(
                [
                    'id' => $id,
                ], [
                    'code' => $code,
                    'country_code' => $country_code,
                    'subadmin1_code' => $subadmin1_code,
                    'name' => $name,
                    'district_id_city' => $district_id_city,
                    'active' => $active,
                ]);
        }
    }
}
