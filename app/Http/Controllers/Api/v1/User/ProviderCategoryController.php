<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ProviderCategory;
use App\Models\Type;
use App\Traits\Responses;
use Illuminate\Http\Request;

class ProviderCategoryController extends Controller
{
    use Responses;

    /**
     * Get provider categories by type ID
     */
     public function getByType($typeId)
    {
        try {
            // Check if type exists
            $type = Type::find($typeId);
            if (!$type) {
                return $this->error_response(
                    __('messages.type_not_found'),
                    []
                );
            }

            $categories = ProviderCategory::where('type_id', $typeId)
                ->with('type:id,name_en,name_ar')
                ->get();
            
            $categories = $categories->map(function ($category) {
                // Get first provider for this category
                $firstProvider = Provider::where('provider_category_id', $category->id)
                    ->first();

                $providerData = null;
                if ($firstProvider) {
                    $providerData = [
                        'id' => $firstProvider->id,
                        'name' => $firstProvider->name,
                        'experience' => $firstProvider->number_years_experience,
                        'description' => $firstProvider->description,
                        'price' => $firstProvider->price,
                        'photo' => $firstProvider->photo ? asset('assets/admin/uploads/' . $firstProvider->photo) : null,
                        'rating' => $firstProvider->rating,
                    ];
                }

                return [
                    'id' => $category->id,
                    'name_en' => $category->name_en,
                    'name_ar' => $category->name_ar,
                    'name' => $category->name,
                    'type_of_visit' => $category->type_of_visit,
                    'price' => $category->price,
                    'phone_of_emeregency' => $category->phone_of_emeregency,
                    'photo' => $category->photo ? asset('assets/admin/uploads/' . $category->photo) : null,
                    'provider' => $providerData,
                    'type' => [
                        'id' => $category->type->id,
                        'name_en' => $category->type->name_en,
                        'name_ar' => $category->type->name_ar,
                        'name' => $category->type->name,
                    ]
                ];
            });

            return $this->success_response(
                __('messages.provider_categories_retrieved_successfully'),
                $categories
            );
        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_retrieving_provider_categories'),
                []
            );
        }
    }
    
}