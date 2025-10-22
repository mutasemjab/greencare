<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\TypeElderlyCare;
use App\Models\TypeRequestNurse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeRequestNurseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TypeRequestNurse::query();
        
        // Filter by type if provided
        if ($request->has('type_of_service') && !empty($request->type_of_service)) {
            $query->where('type_of_service', $request->type_of_service);
        }
        
        // Search functionality by price range
        if ($request->has('min_price') && !empty($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price') && !empty($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }
        
        $requestNurses = $query->orderBy('type_of_service')->orderBy('price')->paginate(15);
        
        return view('admin.request-nurses.index', compact('requestNurses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.request-nurses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_of_service' => 'required|in:hour,day,sleep,number_of_days',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        TypeRequestNurse::create($request->all());

        return redirect()->route('request-nurses.index')
            ->with('success', __('messages.request_nurse_created_successfully'));
    }

 

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeRequestNurse $requestNurses)
    {
        return view('admin.request-nurses.edit', compact('requestNurses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeRequestNurse $requestNurses)
    {
        $validator = Validator::make($request->all(), [
            'type_of_service' => 'required|in:hour,day,sleep,number_of_days',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $requestNurses->update($request->all());

        return redirect()->route('request-nurses.index')
            ->with('success', __('messages.request_nurse_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeRequestNurse $requestNurses)
    {
        $requestNurses->delete();

        return redirect()->route('request-nurses.index')
            ->with('success', __('messages.request_nurse_deleted_successfully'));
    }
}