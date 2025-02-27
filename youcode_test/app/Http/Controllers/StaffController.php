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
    
    public function viewTest($id)
    {
        $staff = Auth::user()->staff;
        
        $test = PresentielTest::where('staff_id', $staff->id)
            ->with('candidate.user', 'candidate.documents')
            ->findOrFail($id);
        
        return view('staff.tests.view', compact('test'));
    }
    
    public function updateTestStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled,postponed',
            'notes' => 'nullable|string',
        ]);
        
        $staff = Auth::user()->staff;
        
        $test = PresentielTest::where('staff_id', $staff->id)
            ->findOrFail($id);
        
        $test->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);
        
        // Notifier le candidat
        $test->candidate->user->notifications()->create([
            'type' => 'test_status_updated',
            'content' => 'Le statut de votre test présentiel a été mis à jour : ' . $request->status,
            'data' => [
                'test_id' => $test->id,
                'status' => $request->status,
            ],
        ]);
        
        return redirect()->route('staff.tests.view', $id)
            ->with('success', 'Statut du test mis à jour avec succès');
    }
    