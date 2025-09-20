<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('categories')
            ->leftJoin('categories as parent', 'categories.category_id', '=', 'parent.id')
            ->select(
                'categories.*',
                'parent.name_en as parent_name_en',
                'parent.name_ar as parent_name_ar'
            )
            ->get();
        
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = DB::table('categories')
            ->whereNull('category_id')
            ->get();
        
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'photo' => 'required',
            'category_id' => 'nullable|exists:categories,id',
        ]);

         $data = $request->only(['name_en', 'name_ar','category_id']);

            // Upload photo using your custom uploadImage function
         if ($request->hasFile('photo')) {
             $data['photo'] = uploadImage('assets/admin/uploads/', $request->file('photo'));
         }

         Category::create($data);

        return redirect()->route('categories.index')->with('success', __('messages.Category_Created'));
    }

    public function edit($id)
    {
        $category = DB::table('categories')->where('id', $id)->first();
        
        if (!$category) {
            return redirect()->route('categories.index')->with('error', __('messages.Category_Not_Found'));
        }

        // Get parent categories excluding the current category and its children
        $parentCategories = DB::table('categories')
            ->where('id', '!=', $id)
            ->whereNull('category_id')
            ->orWhere(function($query) use ($id) {
                $query->where('category_id', '!=', $id)
                      ->whereNotNull('category_id');
            })
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }


    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'category_id' => 'nullable|exists:categories,id|not_in:' . $category->id,
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(['name_en', 'name_ar','category_id']);

            // Handle photo update using your custom uploadImage function
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($category->photo && File::exists(base_path('assets/admin/uploads/'.$category->photo))) {
                    File::delete(base_path('assets/admin/uploads/'.$category->photo));
                }
                
                // Upload new photo
                $data['photo'] = uploadImage('assets/admin/uploads/', $request->file('photo'));
            }

            $category->update($data);

            DB::commit();

            return redirect()->route('categories.index')
                           ->with('success', __('messages.Category_Updated_Successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', __('messages.Something_Went_Wrong'))
                           ->withInput();
        }
    }
}