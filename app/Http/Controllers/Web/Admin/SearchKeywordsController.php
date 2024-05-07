<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;

class SearchKeywordsController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard.admin-settings-general-search-keywords');
    }
}
