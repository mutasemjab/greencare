<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Models\News;
use App\Models\Type;
use App\Traits\Responses;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use Responses;

    /**
     * Get home page data (banners, types, news)
     */
    public function index()
    {
        try {
            // Get banners
            $banners = Banner::latest()->get()->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'photo' => $banner->photo_url,
                    'created_at' => $banner->created_at,
                ];
            });

            // Get types
            $types = Type::select('id', 'name_en', 'name_ar', 'photo')->get()->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name_en' => $type->name_en,
                    'name_ar' => $type->name_ar,
                    'name' => $type->name,
                    'photo' => $type->photo ? asset('storage/' . $type->photo) : null,
                ];
            });

            // Get latest news (limit to 6 for home page)
            $news = News::latest()->limit(6)->get()->map(function ($newsItem) {
                return [
                    'id' => $newsItem->id,
                    'title_en' => $newsItem->title_en,
                    'title_ar' => $newsItem->title_ar,
                    'title' => $newsItem->title,
                    'description_en' => $newsItem->description_en,
                    'description_ar' => $newsItem->description_ar,
                    'description' => $newsItem->description,
                    'photo' => $newsItem->photo_url,
                    'date_of_news' => $newsItem->date_of_news->format('Y-m-d'),
                    'created_at' => $newsItem->created_at,
                ];
            });

            $homeData = [
                'banners' => $banners,
                'types' => $types,
                'news' => $news,
            ];

            return $this->success_response(
                __('messages.home_data_retrieved_successfully'),
                $homeData
            );
        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_retrieving_home_data'),
                []
            );
        }
    }

    
    /**
     * Get single news item
     */
    public function newsDetails($id)
    {
        try {
            $newsItem = News::find($id);

            if (!$newsItem) {
                return $this->error_response(
                    __('messages.news_not_found'),
                    []
                );
            }

            $newsData = [
                'id' => $newsItem->id,
                'title_en' => $newsItem->title_en,
                'title_ar' => $newsItem->title_ar,
                'title' => $newsItem->title,
                'description_en' => $newsItem->description_en,
                'description_ar' => $newsItem->description_ar,
                'description' => $newsItem->description,
                'photo' => $newsItem->photo_url,
                'date_of_news' => $newsItem->date_of_news->format('Y-m-d'),
                'created_at' => $newsItem->created_at,
                'updated_at' => $newsItem->updated_at,
            ];

            return $this->success_response(
                __('messages.news_retrieved_successfully'),
                $newsData
            );
        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_retrieving_news'),
                []
            );
        }
    }
}