<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('views/branch/BranchDashboard');
    }
}
