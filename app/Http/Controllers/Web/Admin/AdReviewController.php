<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Post;

class AdReviewController extends Controller
{
    public function index()
    {
        // $count = Post::where('is_approved', 0)->count();

        $count = Post::whereNull('reviewed_at')->count();

        return view('admin.dashboard.approve-ads', ['count' => $count]);
    }
}
