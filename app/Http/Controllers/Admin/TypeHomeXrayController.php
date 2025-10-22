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
        $query = TypeHomeXray::with('parent');
        
        // Category filter (parent categories only)
        if ($request->has('category') && !empty($request->category)) {
            if ($request->category === 'parents') {
                $query->parentsOnly();
            } elseif ($request->category === 'subcategories') {
                $query->subcategoriesOnly();
            } elseif (is_numeric($request->category)) {
                $query->childrenOf($request->category);
            }
        }
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('parent', function($parentQuery) use ($search) {
                      $parentQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Price range filtering
        if ($request->has('min_price') && !empty($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price') && !empty($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }
        
        $homeXrays = $query->orderBy('parent_id', 'asc')
                          ->orderBy('name', 'asc')
                          ->paginate(15);
        
        // Get parent categories for filter dropdown
        $parentCategories = TypeHomeXray::parentsOnly()
                                       ->orderBy('name')
                                       ->get();
        
        return view('admin.home-xrays.index', compact('homeXrays', 'parentCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = TypeHomeXray::parentsOnly()
                                       ->orderBy('name')
                                       ->get();
        
        return view('admin.home-xrays.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'parent_id' => 'nullable|exists:type_home_xrays,id',
        ]);

        // Custom validation to prevent self-referencing
        $validator->after(function ($validator) use ($request) {
            if ($request->parent_id && $request->has('id') && $request->parent_id == $request->id) {
                $validator->errors()->add('parent_id', __('messages.cannot_be_parent_of_itself'));
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        TypeHomeXray::create([
            'name' => $request->name,
            'price' => $request->price,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('home-xrays.index')
            ->with('success', __('messages.home_xray_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(TypeHomeXray $homeXray)
    {
        $homeXray->load(['parent', 'children']);
        
        return view('admin.home-xrays.show', compact('homeXray'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeHomeXray $homeXray)
    {
        $parentCategories = TypeHomeXray::parentsOnly()
                                       ->where('id', '!=', $homeXray->id) // Prevent self-referencing
                                       ->orderBy('name')
                                       ->get();
        
        return view('admin.home-xrays.edit', compact('homeXray', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeHomeXray $homeXray)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'parent_id' => 'nullable|exists:type_home_xrays,id',
        ]);

        // Custom validation to prevent self-referencing and circular references
        $validator->after(function ($validator) use ($request, $homeXray) {
            if ($request->parent_id) {
                if ($request->parent_id == $homeXray->id) {
                    $validator->errors()->add('parent_id', __('messages.cannot_be_parent_of_itself'));
                }
                
                // Check if the selected parent is a child of this category
                $parentCategory = TypeHomeXray::find($request->parent_id);
                if ($parentCategory && $parentCategory->parent_id == $homeXray->id) {
                    $validator->errors()->add('parent_id', __('messages.circular_reference_not_allowed'));
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $homeXray->update([
            'name' => $request->name,
            'price' => $request->price,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('home-xrays.index')
            ->with('success', __('messages.home_xray_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeHomeXray $homeXray)
    {
        // Check if this category has children
        if ($homeXray->children()->count() > 0) {
            return redirect()->route('home-xrays.index')
                ->with('error', __('messages.cannot_delete_category_with_subcategories'));
        }

        $homeXray->delete();

        return redirect()->route('home-xrays.index')
            ->with('success', __('messages.home_xray_deleted_successfully'));
    }

    /**
     * Get subcategories by parent ID (AJAX)
     */
    public function getSubcategories(Request $request)
    {
        $parentId = $request->get('parent_id');
        
        $subcategories = TypeHomeXray::where('parent_id', $parentId)
                                    ->orderBy('name')
                                    ->get(['id', 'name', 'price']);
        
        return response()->json($subcategories);
    }

    /**
     * Move category to different parent (AJAX)
     */
    public function moveCategory(Request $request, TypeHomeXray $homeXray)
    {
        $validator = Validator::make($request->all(), [
            'new_parent_id' => 'nullable|exists:type_home_xrays,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        // Prevent self-referencing
        if ($request->new_parent_id == $homeXray->id) {
            return response()->json(['success' => false, 'message' => __('messages.cannot_be_parent_of_itself')]);
        }

        $homeXray->update(['parent_id' => $request->new_parent_id]);

        return response()->json([
            'success' => true, 
            'message' => __('messages.category_moved_successfully')
        ]);
    }
}