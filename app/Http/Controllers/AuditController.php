<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index()
    {
        $audits = AuditTrail::with('user')->latest()->paginate(15);

        return view('audit.index', compact('audits'));
    }
}
