<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = DB::table('news')->orderBy('date_of_news', 'desc')->get();
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_of_news' => 'required|date',
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = uploadImage('assets/admin/uploads', $request->photo);

        DB::table('news')->insert([
            'date_of_news' => $request->date_of_news,
            'title_en' => $request->title_en,
            'title_ar' => $request->title_ar,
            'description_en' => $request->description_en,
            'description_ar' => $request->description_ar,
            'photo' => $photoPath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('news.index')->with('success', __('messages.news_created'));
    }

    public function edit($id)
    {
        $news = DB::table('news')->where('id', $id)->first();
        if (!$news) {
            return redirect()->route('news.index')->with('error', __('messages.news_not_found'));
        }
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date_of_news' => 'required|date',
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $news = DB::table('news')->where('id', $id)->first();
        if (!$news) {
            return redirect()->route('news.index')->with('error', __('messages.news_not_found'));
        }

        $updateData = [
            'date_of_news' => $request->date_of_news,
            'title_en' => $request->title_en,
            'title_ar' => $request->title_ar,
            'description_en' => $request->description_en,
            'description_ar' => $request->description_ar,
            'updated_at' => now(),
        ];

        if ($request->hasFile('photo')) {
            $updateData['photo'] = uploadImage('assets/admin/uploads', $request->photo);
        }

        DB::table('news')->where('id', $id)->update($updateData);

        return redirect()->route('news.index')->with('success', __('messages.news_updated'));
    }

    public function destroy($id)
    {
        DB::table('news')->where('id', $id)->delete();
        return redirect()->route('news.index')->with('success', __('messages.news_deleted'));
    }
}