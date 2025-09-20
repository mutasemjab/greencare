<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Log the row to help with debugging
        Log::info('Row data:', $row);

        // Validate required fields
        $requiredFields = [
            'number', 'barcode', 'name_en', 'name_ar', 'name_fr', 'description_en', 'description_ar',
            'description_fr', 'has_variation', 'tax', 'selling_price_for_user', 'min_order_for_user',
            'min_order_for_wholesale', 'in_stock', 'status', 'category_id', 'unit_id'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($row[$field])) {
                Log::error("Missing field: $field");
                throw new \Exception("Missing field: $field");
            }
        }

        return new Product([
            'number' => $row['number'],
            'barcode' => $row['barcode'],
            'name_en' => $row['name_en'],
            'name_ar' => $row['name_ar'],
            'name_fr' => $row['name_fr'],
            'description_en' => $row['description_en'],
            'description_ar' => $row['description_ar'],
            'description_fr' => $row['description_fr'],
            'has_variation' => $row['has_variation'],
            'tax' => $row['tax'],
            'selling_price_for_user' => $row['selling_price_for_user'],
            'min_order_for_user' => $row['min_order_for_user'],
            'min_order_for_wholesale' => $row['min_order_for_wholesale'],
            'in_stock' => $row['in_stock'],
            'status' => $row['status'],
            'category_id' => $row['category_id'],
            'unit_id' => $row['unit_id'],
            'shop_id' => auth()->user()->shop_id,
        ]);
    }
}
