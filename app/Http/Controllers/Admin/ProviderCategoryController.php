<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ProviderCategory;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProviderCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:providerCategory-table', ['only' => ['index']]);
        $this->middleware('permission:providerCategory-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:providerCategory-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:providerCategory-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $providerCategories = ProviderCategory::with('type')->latest()->paginate(10);
        return view('admin.provider-categories.index', compact('providerCategories'));
    }

    public function create()
    {
        $types = Type::all();
        return view('admin.provider-categories.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'type_of_visit' => 'required',
            'phone_of_emeregency' => 'required',
            'price' => 'required',
            'type_id' => 'required|exists:types,id',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath =uploadImage('assets/admin/uploads', $request->photo);
        }

        ProviderCategory::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'type_of_visit' => $request->type_of_visit,
            'phone_of_emeregency' => $request->phone_of_emeregency,
            'price' => $request->price,
            'type_id' => $request->type_id,
            'photo' => $photoPath
        ]);

        return redirect()->route('provider-categories.index')->with('success', __('messages.provider_category_added_successfully'));
    }

    public function edit(ProviderCategory $providerCategory)
    {
        $types = Type::all();
        return view('admin.provider-categories.edit', compact('providerCategory', 'types'));
    }

    public function update(Request $request, ProviderCategory $providerCategory)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'type_of_visit' => 'required',
            'phone_of_emeregency' => 'required',
            'price' => 'required',
            'type_id' => 'required|exists:types,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $photoPath = $providerCategory->photo;
        if ($request->hasFile('photo')) {
            
            $photoPath = uploadImage('assets/admin/uploads', $request->photo);
        }

        $providerCategory->update([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'type_of_visit' => $request->type_of_visit,
            'phone_of_emeregency' => $request->phone_of_emeregency,
            'price' => $request->price,
            'type_id' => $request->type_id,
            'photo' => $photoPath
        ]);

        return redirect()->route('provider-categories.index')->with('success', __('messages.provider_category_updated_successfully'));
    }

    public function destroy(ProviderCategory $providerCategory)
    {
        // Delete photo
        if ($providerCategory->photo && Storage::disk('public')->exists($providerCategory->photo)) {
            Storage::disk('public')->delete($providerCategory->photo);
        }

        $providerCategory->delete();
        return redirect()->route('provider-categories.index')->with('success', __('messages.provider_category_deleted_successfully'));
    }
}