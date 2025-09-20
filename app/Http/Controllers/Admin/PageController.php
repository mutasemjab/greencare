<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of pages
     */
    public function index()
    {
        $pages = Page::orderBy('type')->get();
        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page
     */
    public function create()
    {
        $pageTypes = $this->getPageTypes();
        return view('admin.pages.create', compact('pageTypes'));
    }

    /**
     * Store a newly created page in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|integer|in:1,2',
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
        ]);

        // Check if page type already exists
        $existingPage = Page::where('type', $request->type)->first();
        if ($existingPage) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['type' => __('messages.page_type_already_exists')]);
        }

        try {
            Page::create($request->all());

            return redirect()->route('admin.pages.index')
                ->with('success', __('messages.page_created_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('messages.error_creating_page'));
        }
    }

    /**
     * Display the specified page
     */
    public function show(Page $page)
    {
        return view('admin.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified page
     */
    public function edit(Page $page)
    {
        $pageTypes = $this->getPageTypes();
        return view('admin.pages.edit', compact('page', 'pageTypes'));
    }

    /**
     * Update the specified page in storage
     */
    public function update(Request $request, Page $page)
    {
        $request->validate([
            'type' => 'required|integer|in:1,2',
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
        ]);

        // Check if page type already exists (excluding current page)
        $existingPage = Page::where('type', $request->type)
                           ->where('id', '!=', $page->id)
                           ->first();
        if ($existingPage) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['type' => __('messages.page_type_already_exists')]);
        }

        try {
            $page->update($request->all());

            return redirect()->route('admin.pages.index')
                ->with('success', __('messages.page_updated_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('messages.error_updating_page'));
        }
    }

    /**
     * Remove the specified page from storage
     */
    public function destroy(Page $page)
    {
        try {
            $page->delete();
            return redirect()->route('admin.pages.index')
                ->with('success', __('messages.page_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_deleting_page'));
        }
    }

    /**
     * Get page types array
     */
    private function getPageTypes()
    {
        return [
            1 => __('messages.terms_and_conditions'),
            2 => __('messages.privacy_policy'),
        ];
    }

    /**
     * Get page type name
     */
    public static function getPageTypeName($type)
    {
        $types = [
            1 => __('messages.terms_and_conditions'),
            2 => __('messages.privacy_policy'),
        ];

        return $types[$type] ?? __('messages.unknown_type');
    }
}