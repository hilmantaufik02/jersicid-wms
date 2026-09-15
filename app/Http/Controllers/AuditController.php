<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index()
    {
        // TODO: Tambahkan logika untuk menampilkan audit trail
        return view('audit.index');
    }
}
