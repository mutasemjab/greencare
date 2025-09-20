<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Family;
use App\Models\User;
use App\Models\FamilyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FamilyController extends Controller
{
    /**
     * Display a listing of families
     */
    public function index()
    {
        $families = Family::withCount('users')->paginate(15);
        return view('admin.families.index', compact('families'));
    }

    /**
     * Show the form for creating a new family
     */
    public function create()
    {
        $users = User::where('activate', 1)->where('user_type','patient')->get();
        return view('admin.families.create', compact('users'));
    }

    /**
     * Store a newly created family
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $family = Family::create([
                'name' => $request->name
            ]);

            if ($request->has('user_ids')) {
                foreach ($request->user_ids as $userId) {
                    FamilyUser::create([
                        'family_id' => $family->id,
                        'user_id' => $userId
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('families.index')
                ->with('success', __('messages.family_created_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', __('messages.error_creating_family'))
                ->withInput();
        }
    }

    /**
     * Display the specified family
     */
    public function show(Family $family)
    {
        $family->load('users');
        return view('admin.families.show', compact('family'));
    }

    /**
     * Show the form for editing the specified family
     */
    public function edit(Family $family)
    {
        $family->load('users');
        $users = User::where('activate', 1)->where('user_type','patient')->get();
        $familyUserIds = $family->users->pluck('id')->toArray();
        
        return view('admin.families.edit', compact('family', 'users', 'familyUserIds'));
    }

    /**
     * Update the specified family
     */
    public function update(Request $request, Family $family)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $family->update([
                'name' => $request->name
            ]);

            // Remove existing family members
            FamilyUser::where('family_id', $family->id)->delete();

            // Add new family members
            if ($request->has('user_ids')) {
                foreach ($request->user_ids as $userId) {
                    FamilyUser::create([
                        'family_id' => $family->id,
                        'user_id' => $userId
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('families.index')
                ->with('success', __('messages.family_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', __('messages.error_updating_family'))
                ->withInput();
        }
    }

    /**
     * Remove the specified family
     */
    public function destroy(Family $family)
    {
        try {
            $family->delete();
            return redirect()->route('families.index')
                ->with('success', __('messages.family_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_deleting_family'));
        }
    }

    /**
     * Remove a user from family
     */
    public function removeMember(Request $request, Family $family)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            FamilyUser::where('family_id', $family->id)
                ->where('user_id', $request->user_id)
                ->delete();

            return redirect()->back()
                ->with('success', __('messages.member_removed_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_removing_member'));
        }
    }

    /**
     * Add a user to family
     */
    public function addMember(Request $request, Family $family)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            // Check if user is already in this family
            $exists = FamilyUser::where('family_id', $family->id)
                ->where('user_id', $request->user_id)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->with('error', __('messages.member_already_exists'));
            }

            FamilyUser::create([
                'family_id' => $family->id,
                'user_id' => $request->user_id
            ]);

            return redirect()->back()
                ->with('success', __('messages.member_added_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_adding_member'));
        }
    }
}
