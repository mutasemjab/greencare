<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\CareerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CareerController extends Controller
{
    public function index()
    {
        $careers = Career::withCount('applications')->orderBy('title')->paginate(15);
        return view('admin.careers.index', compact('careers'));
    }

    public function create()
    {
        return view('admin.careers.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Career::create([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('careers.index')
            ->with('success', __('messages.career_created_successfully'));
    }

    public function edit(Career $career)
    {
        return view('admin.careers.edit', compact('career'));
    }

    public function update(Request $request, Career $career)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $career->update([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('careers.index')
            ->with('success', __('messages.career_updated_successfully'));
    }

    public function destroy(Career $career)
    {
        $career->delete();
        return redirect()->route('careers.index')
            ->with('success', __('messages.career_deleted_successfully'));
    }

    public function applications(Career $career)
    {
        $applications = $career->applications()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.careers.applications', compact('career', 'applications'));
    }

    public function updateApplicationStatus(Request $request, CareerApplication $application)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,reviewed,accepted,rejected',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $application->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', __('messages.application_status_updated'));
    }
}