<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\TypeMedicalTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeMedicalTestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TypeMedicalTest::query();
        
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
        
        $medicalTests = $query->orderBy('name')->paginate(15);
        
        return view('admin.medical-tests.index', compact('medicalTests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.medical-tests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:type_medical_tests,name',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        TypeMedicalTest::create($request->all());

        return redirect()->route('medical-tests.index')
            ->with('success', __('messages.medical_test_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(TypeMedicalTest $medicalTest)
    {
        return view('admin.medical-tests.show', compact('medicalTest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeMedicalTest $medicalTest)
    {
        return view('admin.medical-tests.edit', compact('medicalTest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeMedicalTest $medicalTest)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:type_medical_tests,name,' . $medicalTest->id,
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $medicalTest->update($request->all());

        return redirect()->route('medical-tests.index')
            ->with('success', __('messages.medical_test_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeMedicalTest $medicalTest)
    {
        $medicalTest->delete();

        return redirect()->route('medical-tests.index')
            ->with('success', __('messages.medical_test_deleted_successfully'));
    }
}