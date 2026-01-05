<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class LabController extends Controller
{
    /**
     * Display a listing of labs.
     */
    public function index()
    {
        $labs = Lab::latest()->paginate(15);
        
        return view('admin.labs.index', compact('labs'));
    }

    /**
     * Show the form for creating a new lab.
     */
    public function create()
    {
        return view('admin.labs.create');
    }

    /**
     * Store a newly created lab in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:labs,email',
            'phone' => 'required|string|unique:labs,phone',
            'license_number' => 'nullable|string|unique:labs,license_number',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'activate' => 'required|in:1,2',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|string|min:6',
        ]);

       // try {
            DB::beginTransaction();

            $data = $request->only([
                'name', 'email', 'phone', 'license_number', 
                'address', 'description', 'activate'
            ]);

            // Hash password
            $data['password'] = Hash::make($request->password);

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $data['photo'] = uploadImage('assets/admin/uploads/labs', $request->file('photo'));
            }

            Lab::create($data);

            DB::commit();

            return redirect()->route('labs.index')
                           ->with('success', __('messages.Lab_Added_Successfully'));

        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return redirect()->back()
        //                    ->with('error', __('messages.Something_Went_Wrong'))
        //                    ->withInput();
        // }
    }

    /**
     * Display the specified lab.
     */
    public function show(Lab $lab)
    {
        return view('admin.labs.show', compact('lab'));
    }

    /**
     * Show the form for editing the specified lab.
     */
    public function edit(Lab $lab)
    {
        return view('admin.labs.edit', compact('lab'));
    }

    /**
     * Update the specified lab in storage.
     */
    public function update(Request $request, Lab $lab)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:labs,email,' . $lab->id,
            'phone' => 'required|string|unique:labs,phone,' . $lab->id,
            'license_number' => 'nullable|string|unique:labs,license_number,' . $lab->id,
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'activate' => 'required|in:1,2',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:6',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'name', 'email', 'phone', 'license_number', 
                'address', 'description', 'activate'
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($lab->photo) {
                    $oldPhotoPath = base_path('assets/admin/uploads/labs/' . $lab->photo);
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
                $data['photo'] = uploadImage('assets/admin/uploads/labs', $request->file('photo'));
            }

            $lab->update($data);

            DB::commit();

            return redirect()->route('labs.index')
                           ->with('success', __('messages.Lab_Updated_Successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', __('messages.Something_Went_Wrong'))
                           ->withInput();
        }
    }

    /**
     * Remove the specified lab from storage.
     */
    public function destroy(Lab $lab)
    {
        try {
            // Delete photo from storage
            if ($lab->photo) {
                $photoPath = base_path('assets/admin/uploads/labs/' . $lab->photo);
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }
            
            $lab->delete();
            
            return redirect()->route('labs.index')
                           ->with('success', __('messages.Lab_Deleted_Successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', __('messages.Something_Went_Wrong'));
        }
    }

    /**
     * Toggle lab activation status.
     */
    public function toggleActivation(Lab $lab)
    {
        try {
            $lab->update([
                'activate' => $lab->activate == 1 ? 2 : 1
            ]);

            $message = $lab->activate == 1 
                ? __('messages.Lab_Activated_Successfully')
                : __('messages.Lab_Deactivated_Successfully');

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', __('messages.Something_Went_Wrong'));
        }
    }
}