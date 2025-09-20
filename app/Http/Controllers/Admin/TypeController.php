<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:type-table', ['only' => ['index']]);
        $this->middleware('permission:type-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:type-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:type-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $types = Type::latest()->paginate(10);
        return view('admin.types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath =  uploadImage('assets/admin/uploads', $request->photo);
        }

        Type::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'photo' => $photoPath
        ]);

        return redirect()->route('types.index')->with('success', __('messages.type_added_successfully'));
    }

    public function edit(Type $type)
    {
        return view('admin.types.edit', compact('type'));
    }

    public function update(Request $request, Type $type)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $photoPath = $type->photo;
        if ($request->hasFile('photo')) {
            $photoPath = uploadImage('assets/admin/uploads', $request->photo);
        }

        $type->update([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'photo' => $photoPath
        ]);

        return redirect()->route('types.index')->with('success', __('messages.type_updated_successfully'));
    }

    public function destroy(Type $type)
    {
        // Delete photo
        if ($type->photo && Storage::disk('public')->exists($type->photo)) {
            Storage::disk('public')->delete($type->photo);
        }

        $type->delete();
        return redirect()->route('types.index')->with('success', __('messages.type_deleted_successfully'));
    }
}