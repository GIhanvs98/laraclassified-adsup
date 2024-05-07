<?php

namespace App\Http\Controllers\Web\Public\Shops;

use App\Http\Controllers\Web\Public\Search\BaseController;
use App\Models\Shop;
use App\Models\User;
use App\Models\Membership;
use App\Models\Transaction;
use Larapen\LaravelMetaTags\Facades\MetaTag;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ShopController extends BaseController
{
	public function index(int $id, string $slug, Request $request)
	{

		if (Shop::has('user')->whereId($id)->doesntExist()) {
			return abort(404);
		}

		$shop = Shop::find($id);

		$user = $shop->user;

		if ($user->exists()) {

			$usernameSlug = Str::slug($shop->title, '-');

			if ($slug == $usernameSlug) {

				// Validate membership not a free membership.
				if (Membership::where('id', $user->membership->id)->member()->doesntExist()) {
					// If it is a free membership. 

					return abort(404);
				}

				// Validate transactions. If the user has paid for the given time, then it is allowed.
				$transactionsValid = Transaction::valid('membership')
					->where('user_id', $user->id);

				if ($transactionsValid->doesntExist()) {
					// If he hasent payed for the current time. 

					return abort(404);
				}

				// Meta Tags

				$shopTitle = ucfirst($shop->title) . " | " . config('app.name');

				MetaTag::set('title', $shopTitle);
				MetaTag::set('description', $shop->description);

				return appView(
					'shops.index',
					compact(
						'id',
						'slug',
					)
				);

			} else {
				return abort(404);
			}
		} else {
			return abort(404);
		}
	}
}
