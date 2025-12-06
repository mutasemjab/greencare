<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecialMedicalForm;
use App\Models\Room;
use Illuminate\Http\Request;

class SpecialMedicalFormController extends Controller
{
    /**
     * Display a listing of all special medical forms
     */
    public function index(Request $request)
    {
        $query = SpecialMedicalForm::with(['creator', 'room'])
            ->withCount('replies');

        // Filter by room
        if ($request->has('room_id') && !empty($request->room_id)) {
            $query->where('room_id', $request->room_id);
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by creator type
        if ($request->has('creator_type') && !empty($request->creator_type)) {
            $query->whereHas('creator', function ($q) use ($request) {
                $q->where('user_type', $request->creator_type);
            });
        }

        $forms = $query->orderBy('created_at', 'desc')->paginate(15);
        $rooms = Room::orderBy('title')->get();

        return view('admin.special-medical-forms.index', compact('forms', 'rooms'));
    }

    /**
     * Display the specified form with all replies
     */
    public function show(SpecialMedicalForm $specialMedicalForm)
    {
        $specialMedicalForm->load(['creator', 'room', 'replies.user']);
        
        return view('admin.special-medical-forms.show', compact('specialMedicalForm'));
    }
}