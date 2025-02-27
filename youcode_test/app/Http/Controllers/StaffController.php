<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\PresentielTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function dashboard()
    {
        $staff = Auth::user()->staff;
        
        $upcomingTests = $staff->presentielTests()
            ->where('date', '>', now())
            ->where('status', 'scheduled')
            ->with('candidate.user')
            ->get();
        
        $availabilities = $staff->availabilities()
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->get();
        
        return view('staff.dashboard', compact('upcomingTests', 'availabilities'));
    }
    
    public function tests()
    {
        $staff = Auth::user()->staff;
        
        $tests = $staff->presentielTests()
            ->with('candidate.user')
            ->orderBy('date', 'desc')
            ->get();
        
        return view('staff.tests.index', compact('tests'));
    }
    

