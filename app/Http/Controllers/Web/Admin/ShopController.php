<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __invoke()
    {
        $count = Shop::whereHas('user', function (Builder $query) {
            $query->verified();
        })->count();

        return view('admin.dashboard.admin-shops', ['count' => $count]);
    }
}
