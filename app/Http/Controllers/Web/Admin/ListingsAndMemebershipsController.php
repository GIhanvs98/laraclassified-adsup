<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ListingsAndMemebershipsController extends Controller
{
    public function listing()
    {
        $count = Post::withWhereHas('transactions', function ($query) {
                                $query->successfull('ad-promotion');
                            })
                            ->verified()
                            ->unarchived()
                            ->count();

        return view('admin.dashboard.admin-listing-durations', ['count' => $count]);
    }

    public function membership()
    {
        $count = Transaction::withWhereHas('membership')->valid('membership')->count();

        return view('admin.dashboard.admin-membership-durations', ['count' => $count]);
    }
}
