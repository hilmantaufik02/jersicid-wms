<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function owner()
    {
        // TODO: Tambahkan statistik untuk dashboard owner
        return view('dashboard.owner');
    }

    public function gudang()
    {
        // TODO: Tambahkan statistik untuk dashboard gudang
        return view('dashboard.gudang');
    }
}