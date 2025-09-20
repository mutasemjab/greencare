<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    

    public function index()
    {
        $brands = Brand::withCount('products')->latest()->paginate(15);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(['name_en', 'name_ar',]);

            // Upload photo using your custom uploadImage function
            if ($request->hasFile('photo')) {
                $data['photo'] = uploadImage('assets/admin/uploads/', $request->file('photo'));
            }

            Brand::create($data);

            DB::commit();

            return redirect()->route('brands.index')
                           ->with('success', __('messages.Brand_Added_Successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', __('messages.Something_Went_Wrong'))
                           ->withInput();
        }
    }

    public function show(Brand $brand)
    {
        $brand->load(['products.images', 'products.category']);
        return view('admin.brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(['name_en', 'name_ar']);

            // Handle photo update using your custom uploadImage function
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($brand->photo && File::exists(base_path('assets/admin/uploads/'.$brand->photo))) {
                    File::delete(base_path('assets/admin/uploads/'.$brand->photo));
                }
                
                // Upload new photo
                $data['photo'] = uploadImage('assets/admin/uploads/', $request->file('photo'));
            }

            $brand->update($data);

            DB::commit();

            return redirect()->route('brands.index')
                           ->with('success', __('messages.Brand_Updated_Successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', __('messages.Something_Went_Wrong'))
                           ->withInput();
        }
    }

}