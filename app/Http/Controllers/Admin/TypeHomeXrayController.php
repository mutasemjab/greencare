<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\TypeHomeXray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeHomeXrayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TypeHomeXray::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Price range filtering
        if ($request->has('min_price') && !empty($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price') && !empty($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }
        
        $homeXrays = $query->orderBy('name')->paginate(15);
        
        return view('admin.home-xrays.index', compact('homeXrays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.home-xrays.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:type_home_xrays,name',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        TypeHomeXray::create($request->all());

        return redirect()->route('home-xrays.index')
            ->with('success', __('messages.home_xray_created_successfully'));
    }

 

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeHomeXray $homeXray)
    {
        return view('admin.home-xrays.edit', compact('homeXray'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeHomeXray $homeXray)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:type_home_xrays,name,' . $homeXray->id,
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $homeXray->update($request->all());

        return redirect()->route('home-xrays.index')
            ->with('success', __('messages.home_xray_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeHomeXray $homeXray)
    {
        $homeXray->delete();

        return redirect()->route('home-xrays.index')
            ->with('success', __('messages.home_xray_deleted_successfully'));
    }
}