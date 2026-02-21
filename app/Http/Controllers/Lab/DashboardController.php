<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\MedicalTest;
use App\Models\HomeXray;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $lab = auth('lab')->user();
        
        // إحصائيات المواعيد
        $stats = [
            'total_appointments' => MedicalTest::where('lab_id', $lab->id)->count() + 
                                   HomeXray::where('lab_id', $lab->id)->count(),
            
            'pending' => MedicalTest::where('lab_id', $lab->id)->where('status', 'pending')->count() + 
                        HomeXray::where('lab_id', $lab->id)->where('status', 'pending')->count(),
            
            'confirmed' => MedicalTest::where('lab_id', $lab->id)->where('status', 'confirmed')->count() + 
                          HomeXray::where('lab_id', $lab->id)->where('status', 'confirmed')->count(),
            
            'processing' => MedicalTest::where('lab_id', $lab->id)->where('status', 'processing')->count() + 
                           HomeXray::where('lab_id', $lab->id)->where('status', 'processing')->count(),
            
            'finished' => MedicalTest::where('lab_id', $lab->id)->where('status', 'finished')->count() + 
                         HomeXray::where('lab_id', $lab->id)->where('status', 'finished')->count(),
        ];
        
        // آخر المواعيد
        $recentMedicalTests = MedicalTest::with(['user', 'typeMedicalTest'])
            ->where('lab_id', $lab->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $recentHomeXrays = HomeXray::with(['user', 'typeHomeXray'])
            ->where('lab_id', $lab->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('lab.dashboard', compact('lab', 'stats', 'recentMedicalTests', 'recentHomeXrays'));
    }
}