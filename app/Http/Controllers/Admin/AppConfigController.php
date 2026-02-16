<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:appConfig-table', ['only' => ['index', 'show']]);
        $this->middleware('permission:appConfig-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:appConfig-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:appConfig-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appConfigs = AppConfig::latest()->paginate(10);
        return view('admin.app_configs.index', compact('appConfigs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.app_configs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_play_link_user_app' => 'nullable|url|max:500',
         
            'app_store_link_user_app' => 'nullable|url|max:500',
    
            'hawawi_link_user_app' => 'nullable|url|max:500',
      
            'min_version_google_play_user_app' => 'nullable|string|max:20',
      
            'min_version_app_store_user_app' => 'nullable|string|max:20',
       
            'min_version_hawawi_user_app' => 'nullable|string|max:20',
      
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        AppConfig::create($request->all());

        return redirect()->route('app-configs.index')
            ->with('success', __('messages.app_config_created_successfully'));
    }


    /**
     * Display the specified resource.
     */
    public function show(AppConfig $appConfig)
    {
        return view('admin.app_configs.show', compact('appConfig'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AppConfig $appConfig)
    {
        return view('admin.app_configs.edit', compact('appConfig'));
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, AppConfig $appConfig)
    {
        $validator = Validator::make($request->all(), [
            'google_play_link_user_app' => 'nullable|url|max:500',
           
            'app_store_link_user_app' => 'nullable|url|max:500',
 
            'hawawi_link_user_app' => 'nullable|url|max:500',
       
            'min_version_google_play_user_app' => 'nullable|string|max:20',
     
            'min_version_app_store_user_app' => 'nullable|string|max:20',
       
            'min_version_hawawi_user_app' => 'nullable|string|max:20',
    
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $appConfig->update($request->all());

        return redirect()->route('app-configs.index')
            ->with('success', __('messages.app_config_updated_successfully'));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppConfig $appConfig)
    {
        $appConfig->delete();

        return redirect()->route('app-configs.index')
            ->with('success', __('messages.app_config_deleted_successfully'));
    }
}