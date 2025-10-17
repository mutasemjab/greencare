<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\PledgeForm;
use App\Models\Room;
use Illuminate\Http\Request;

class PledgeFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PledgeForm::with('room');
        
        // Filter by type if provided
        if ($request->has('type') && in_array($request->type, ['pledge_form', 'authorization_form'])) {
            $query->where('type', $request->type);
        }
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name_of_nurse', 'like', "%{$search}%")
                  ->orWhere('name_of_patient', 'like', "%{$search}%")
                  ->orWhere('identity_number_of_patient', 'like', "%{$search}%")
                  ->orWhere('phone_of_patient', 'like', "%{$search}%");
            });
        }
        
        $pledgeForms = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.pledge-forms.index', compact('pledgeForms'));
    }

    /**
     * Display the specified resource.
     */
    public function show(PledgeForm $pledgeForm)
    {
        $pledgeForm->load('room');
        
        return view('admin.pledge-forms.show', compact('pledgeForm'));
    }
}