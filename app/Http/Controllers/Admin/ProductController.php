<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Celebrity;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Variation;
use App\Models\Shop;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::with(['category','brand', 'images'])
                          ->latest()
                          ->paginate(15);
        
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        
        return view('admin.products.create', compact('categories', 'brands',));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'price' => 'required|numeric|min:0',
            'tax' => 'numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif',
          
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'name_en', 'name_ar', 'description_en', 'description_ar', 
                'price', 'tax', 'discount_percentage', 'category_id', 
                 'brand_id',
            ]);

            // Calculate price after discount
            if ($request->discount_percentage) {
                $data['price_after_discount'] = $data['price'] - ($data['price'] * $data['discount_percentage'] / 100);
            }

            $product = Product::create($data);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path =  uploadImage('assets/admin/uploads', $image);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'photo' => $path
                    ]);
                }
            }


            DB::commit();

            return redirect()->route('products.index')
                           ->with('success', __('messages.Product_Added_Successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', __('messages.Something_Went_Wrong'))
                           ->withInput();
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'images', ]);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();
   
        
        $product->load(['images', 'variations']);
        
        return view('admin.products.edit', compact('product', 'categories','brands',));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'price' => 'required|numeric|min:0',
            'tax' => 'numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif',
           
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'name_en', 'name_ar', 'description_en', 'description_ar', 
                'price', 'tax', 'discount_percentage', 'category_id', 
               'brand_id', 
            ]);

            // Calculate price after discount
            if ($request->discount_percentage) {
                $data['price_after_discount'] = $data['price'] - ($data['price'] * $data['discount_percentage'] / 100);
            } else {
                $data['price_after_discount'] = null;
            }

            $product->update($data);

            // Handle new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path =  uploadImage('assets/admin/uploads', $image);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'photo' => $path
                    ]);
                }
            }

            // Handle image deletions
            if ($request->has('delete_images')) {
                foreach ($request->delete_images as $imageId) {
                    $image = ProductImage::find($imageId);
                    if ($image && $image->product_id == $product->id) {
                        $filePath = base_path('assets/admin/uploads/' . $image);
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                        $image->delete();
                    }
                }
            }

          
            DB::commit();

            return redirect()->route('products.index')
                           ->with('success', __('messages.Product_Updated_Successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', __('messages.Something_Went_Wrong'))
                           ->withInput();
        }
    }

    public function destroy(Product $product)
    {
        try {
            // Delete associated images from storage
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->photo);
            }
            
            $product->delete();
            
            return redirect()->route('products.index')
                           ->with('success', __('messages.Product_Deleted_Successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', __('messages.Something_Went_Wrong'));
        }
    }

}