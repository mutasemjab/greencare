<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\News;
use App\Models\Type;
use App\Traits\Responses;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    use Responses; 


    /**
     * Get single news item
     */
    public function index()
    {
        
            $news = News::get();
            return $this->success_response(
                __('messages.news_retrieved_successfully'),
                $news
            );
      
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