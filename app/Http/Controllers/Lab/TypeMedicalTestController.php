<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\TypeMedicalTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeMedicalTestController extends Controller
{
    public function index(Request $request)
    {
        $query = TypeMedicalTest::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $medicalTests = $query->orderBy('name')->paginate(15);

        return view('lab.medical-test-types.index', compact('medicalTests'));
    }

    public function create()
    {
        return view('lab.medical-test-types.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255|unique:type_medical_tests,name',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        TypeMedicalTest::create($request->only('name', 'price'));

        return redirect()->route('lab.type-medical-tests.index')
            ->with('success', __('messages.medical_test_created_successfully'));
    }

    public function edit(TypeMedicalTest $typeMedicalTest)
    {
        return view('lab.medical-test-types.edit', compact('typeMedicalTest'));
    }

    public function update(Request $request, TypeMedicalTest $typeMedicalTest)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255|unique:type_medical_tests,name,' . $typeMedicalTest->id,
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $typeMedicalTest->update($request->only('name', 'price'));

        return redirect()->route('lab.type-medical-tests.index')
            ->with('success', __('messages.medical_test_updated_successfully'));
    }

    public function destroy(TypeMedicalTest $typeMedicalTest)
    {
        $typeMedicalTest->delete();

        return redirect()->route('lab.type-medical-tests.index')
            ->with('success', __('messages.medical_test_deleted_successfully'));
    }
}
