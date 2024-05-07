<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;

class ListingFeaturesController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard.admin-settings-general-post-ad-features');
    }
}
