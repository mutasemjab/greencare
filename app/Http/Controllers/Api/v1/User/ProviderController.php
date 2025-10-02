<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ProviderCategory;
use App\Traits\Responses;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    use Responses;


    /**
     * Get providers by category ID
     */
    public function getByCategory($categoryId)
    {
        try {
            // Check if category exists
            $category = ProviderCategory::find($categoryId);
            if (!$category) {
                return $this->error_response(
                    __('messages.provider_category_not_found'),
                    []
                );
            }

            $providers = Provider::where('provider_category_id', $categoryId)
                ->with([
                    'providerCategory:id,name_en,name_ar,type_id',
                    'providerCategory.type:id,name_en,name_ar'
                ])->get();
            
            $providers = $providers->map(function ($provider) {
                return [
                    'id' => $provider->id,
                    'name' => $provider->name,
                    'description' => $provider->description,
                    'rating' => $provider->rating,
                    'years_experience' => $provider->number_years_experience,
                    'price' => (float) $provider->price,
                    'photo' => $provider->photo ? asset('assets/admin/uploads/' . $provider->photo) : null,
                    'category' => [
                        'id' => $provider->providerCategory->id,
                        'name_en' => $provider->providerCategory->name_en,
                        'name_ar' => $provider->providerCategory->name_ar,
                        'name' => $provider->providerCategory->name,
                        'type' => [
                            'id' => $provider->providerCategory->type->id,
                            'name_en' => $provider->providerCategory->type->name_en,
                            'name_ar' => $provider->providerCategory->type->name_ar,
                            'name' => $provider->providerCategory->type->name,
                        ]
                    ]
                ];
            });

            return $this->success_response(
                __('messages.providers_retrieved_successfully'),
                $providers
            );
        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_retrieving_providers'),
                []
            );
        }
    }

   

    /**
     * Search providers by name
     */
    public function search(Request $request)
    {
        try {
            $searchTerm = $request->get('search', '');
            
            if (empty($searchTerm)) {
                return $this->error_response(
                    __('messages.search_term_required'),
                    []
                );
            }

            $providers = Provider::where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                ->with([
                    'providerCategory:id,name_en,name_ar,type_id',
                    'providerCategory.type:id,name_en,name_ar'
                ])
                ->select('id', 'name', 'number_years_experience', 'price', 'photo', 'provider_category_id')
                ->get();
            
            $providers = $providers->map(function ($provider) {
                return [
                    'id' => $provider->id,
                    'name' => $provider->name,
                    'years_experience' => $provider->number_years_experience,
                    'price' => (float) $provider->price,
                    'photo' => $provider->photo ? asset('assets/admin/uploads/' . $provider->photo) : null,
                    'category' => [
                        'id' => $provider->providerCategory->id,
                        'name_en' => $provider->providerCategory->name_en,
                        'name_ar' => $provider->providerCategory->name_ar,
                        'name' => $provider->providerCategory->name,
                        'type' => [
                            'id' => $provider->providerCategory->type->id,
                            'name_en' => $provider->providerCategory->type->name_en,
                            'name_ar' => $provider->providerCategory->type->name_ar,
                            'name' => $provider->providerCategory->type->name,
                        ]
                    ]
                ];
            });

            return $this->success_response(
                __('messages.search_results_retrieved_successfully'),
                $providers
            );
        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_searching_providers'),
                []
            );
        }
    }
}