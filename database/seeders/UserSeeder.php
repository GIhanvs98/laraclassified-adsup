<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $membership_id = 2;

        User::factory()
            ->count(1)
            ->hasPosts(1, function (array $attributes, User $user) {
                return [
                    'contact_name' => $user->contact_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'phone_national' => $user->phone_national,
                    'phone_alternate_1' => $user->phone_alternate_1,
                    'phone_alternate_2' => $user->phone_alternate_2,
                    'whatsapp_number' => $user->whatsapp_number,
                        
                    'category_id' => 137,
                    'sub_category_group_id' => 1,
                    'city_id' => 54,
                ];
            })
            ->create([
                'membership_id' => $membership_id,  // Business Lite
            ]);

/*
        $membership_id = 3;

        User::factory()
            ->count(1)
            ->hasPosts(1, function (array $attributes, User $user) {
                return [
                    'contact_name' => $user->contact_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'phone_national' => $user->phone_national,
                    'phone_alternate_1' => $user->phone_alternate_1,
                    'phone_alternate_2' => $user->phone_alternate_2,
                    'whatsapp_number' => $user->whatsapp_number,
                ];
            })
            ->hasTransactions(1, function (array $attributes, User $user) use ($membership_id) {
                return [
                    'user_id' => $user->id,
                    'membership_id' => $membership_id,
                ];
            })
            ->create([
                'membership_id' => $membership_id,  // Business Lite
            ]);

            */
    }
}
