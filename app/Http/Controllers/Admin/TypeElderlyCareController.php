<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\TypeElderlyCare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeElderlyCareController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TypeElderlyCare::query();
        
        // Filter by service type if provided
        if ($request->has('type_of_service') && !empty($request->type_of_service)) {
            $query->where('type_of_service', $request->type_of_service);
        }
        
        // Filter by care type if provided
        if ($request->has('type_of_care') && !empty($request->type_of_care)) {
            $query->where('type_of_care', $request->type_of_care);
        }
        
        // Search functionality by price range
        if ($request->has('min_price') && !empty($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price') && !empty($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }
        
        $elderlyCares = $query->orderBy('type_of_care')
                             ->orderBy('type_of_service')
                             ->orderBy('price')
                             ->paginate(15);
        
        return view('admin.elderly-cares.index', compact('elderlyCares'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.elderly-cares.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_of_service' => 'required|in:hour,day,sleep,number_of_days',
            'type_of_care' => 'required|in:elderly_care,patient_care,mom,child',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        TypeElderlyCare::create($request->all());

        return redirect()->route('elderly-cares.index')
            ->with('success', __('messages.elderly_care_created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeElderlyCare $elderlyCare)
    {
        return view('admin.elderly-cares.edit', compact('elderlyCare'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeElderlyCare $elderlyCare)
    {
        $validator = Validator::make($request->all(), [
            'type_of_service' => 'required|in:hour,day,sleep,number_of_days',
            'type_of_care' => 'required|in:elderly_care,patient_care,mom,child',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $elderlyCare->update($request->all());

        return redirect()->route('elderly-cares.index')
            ->with('success', __('messages.elderly_care_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeElderlyCare $elderlyCare)
    {
        $elderlyCare->delete();

        return redirect()->route('elderly-cares.index')
            ->with('success', __('messages.elderly_care_deleted_successfully'));
    }
}