<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Membership;
use App\Models\ReportAd;
use Illuminate\Http\Request;

class AdsReportController extends Controller
{
    public function index(){

        $count = ReportAd::count();

        return view('admin.dashboard.ads-reports', ['count' => $count]);
    }
}
