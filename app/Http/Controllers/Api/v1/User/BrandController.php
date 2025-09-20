<?php


namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Traits\Responses;


class BrandController extends Controller
{
     use Responses;

     public function index()
     {
         
         $brands = Brand::get();
         
         return $this->success_response('Brand retrieved successfully', $brands);
     }
   
     public function getProductsFromBrand($id)
     {
         
         $brands = Brand::with('products','products.images')->where('id',$id)->get();
         
         return $this->success_response('Brand retrieved successfully', $brands);
     }
   
}
