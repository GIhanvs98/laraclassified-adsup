<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Transaction;

class TransactionController extends Controller
{

    public function index()
    {
        $count = Transaction::count();

        return view('admin.dashboard.transactions', ['count' => $count]);
    }

}
