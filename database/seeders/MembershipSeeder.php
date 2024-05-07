<?php

namespace Database\Seeders;

use App\Models\Membership;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		
        $membership = Membership::create([
			'name'   => 'Non Member',
			'description' => 'New users will be considered as non members.',
			'icon' => '',
			'allowed_ads' => '5',
			'allowed_pictures' => '5',
		]);

        $membership = Membership::create([
			'name'   => 'Member',
			'description' => 'Buy a Business Premium Membership to not only get a special badge but also a higher ad limit and other benefits.',
			'icon' => '<svg viewBox="0 0 18 18" class="svg-wrapper--8ky9e"><g fill="none" fill-rule="evenodd"><circle fill="#FFC800" cx="9" cy="9" r="8.64"></circle><path fill="#FFF" fill-rule="nonzero" d="M9 11.831L5.877 13.68l.828-3.467L3.96 7.88l3.62-.3L9 4.32l1.42 3.259 3.62.3-2.745 2.334.828 3.467z"></path></g></svg>',
			'amount' => '500.00',
		]);

        $membership = Membership::create([
			'name'   => 'Verified Seller',
			'description' => 'Verify your details with ikman to get this coveted badge that indicates to buyers that you are trustworthy and it is safe to transact with you.',
			'icon' => '<svg viewBox="36 48 18 16"><g fill="#54ACC8" fill-rule="nonzero"><path d="M 14.968 2.988 C 14.939 2.927 14.885 2.881 14.82 2.861 L 14.8 2.855 L 14.755 2.846 C 13.909 2.676 13.078 2.466 12.265 2.179 C 10.837 1.674 9.487 0.971 8.253 0.092 C 8.183 0.042 8.125 0.004 8.033 0.001 C 8.005 -0.001 7.973 0.001 7.944 0.001 C 7.806 0.001 7.72 0.073 7.614 0.15 C 6.59 0.884 5.481 1.489 4.31 1.953 C 3.303 2.349 2.26 2.64 1.2 2.859 C 1.084 2.882 1 3.006 1 3.119 L 1 5.308 C 1 6.045 0.998 6.783 1 7.518 C 1.013 10.175 2.388 12.639 4.642 14.045 C 4.885 14.198 5.132 14.347 5.375 14.495 L 7.713 15.931 L 7.766 15.963 C 7.848 16.013 7.962 16.011 8.042 15.963 L 10.145 14.709 C 10.477 14.512 10.81 14.312 11.14 14.115 C 11.702 13.784 12.223 13.386 12.691 12.931 C 13.917 11.723 14.705 10.141 14.933 8.435 C 14.991 7.995 14.998 7.555 14.998 7.113 L 14.998 3.113 C 15.001 3.069 14.991 3.026 14.968 2.988 Z M 12.218 6.09 L 11.643 6.674 L 9.147 9.218 L 7.579 10.815 C 7.333 11.061 6.934 11.061 6.688 10.815 L 6.377 10.498 L 4.18 8.261 C 3.93 8.008 3.948 7.608 4.18 7.353 C 4.41 7.098 4.837 7.115 5.07 7.353 L 5.384 7.67 L 7.134 9.454 L 7.261 9.325 L 9.757 6.781 L 11.325 5.184 C 11.575 4.929 11.966 4.948 12.216 5.184 C 12.468 5.415 12.451 5.852 12.216 6.09 L 12.218 6.09 Z" transform="matrix(1, 0, 0, 1, 36.663055419921875, 47.650753021240234)"></path></g></svg>',
			'amount' => '1000.00',
		]);

        $membership = Membership::create([
			'name'   => 'Auth Agent',
			'description' => 'Authorized Dealers get this exclusive badge that guarantees buyers that they will get authentic items when they buy from you.',
			'icon' => '<svg viewBox="0 0 16 18" class="svg-wrapper--8ky9e"><g fill="none" fill-rule="evenodd"><circle fill="#D95E46" cx="8" cy="9" r="8"></circle><path d="M5.6 8.9l1.829 1.829L5.6 8.9zm1.829 1.829l3.774-4.015-3.774 4.015z" stroke="#FBF6D5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></g></svg>',
			'amount' => '5000.00',
		]);
		
    }
}
