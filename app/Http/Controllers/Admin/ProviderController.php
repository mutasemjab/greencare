<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Provider;
use App\Models\ProviderCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProviderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:provider-table', ['only' => ['index']]);
        $this->middleware('permission:provider-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:provider-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:provider-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $providers = Provider::with('providerCategory')->latest()->paginate(10);
        return view('admin.providers.index', compact('providers'));
    }

    public function create()
    {
        $providerCategories = ProviderCategory::all();
        return view('admin.providers.create', compact('providerCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'number_years_experience' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'provider_category_id' => 'required|exists:provider_categories,id',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = uploadImage('assets/admin/uploads', $request->photo);
        }

        Provider::create([
            'name' => $request->name,
            'number_years_experience' => $request->number_years_experience,
            'description' => $request->description,
            'price' => $request->price,
            'provider_category_id' => $request->provider_category_id,
            'photo' => $photoPath
        ]);

        return redirect()->route('providers.index')->with('success', __('messages.provider_added_successfully'));
    }

    public function edit(Provider $provider)
    {
        $providerCategories = ProviderCategory::all();
        return view('admin.providers.edit', compact('provider', 'providerCategories'));
    }

    public function update(Request $request, Provider $provider)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'number_years_experience' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'provider_category_id' => 'required|exists:provider_categories,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $photoPath = $provider->photo;
        if ($request->hasFile('photo')) {
           
            $photoPath = uploadImage('assets/admin/uploads', $request->photo);
        }

        $provider->update([
            'name' => $request->name,
            'number_years_experience' => $request->number_years_experience,
            'description' => $request->description,
            'price' => $request->price,
            'provider_category_id' => $request->provider_category_id,
            'photo' => $photoPath
        ]);

        return redirect()->route('providers.index')->with('success', __('messages.provider_updated_successfully'));
    }

    public function destroy(Provider $provider)
    {
        // Delete photo
        if ($provider->photo && Storage::disk('public')->exists($provider->photo)) {
            Storage::disk('public')->delete($provider->photo);
        }

        $provider->delete();
        return redirect()->route('providers.index')->with('success', __('messages.provider_deleted_successfully'));
    }
}