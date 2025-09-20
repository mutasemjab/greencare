<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsSampleExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Provide a sample row with column names as examples
        return collect([
            [
                'number' => '12345',
                'barcode' => '0123456789012',
                'name_en' => 'Sample Product EN',
                'name_ar' => 'عينة منتج AR',
                'name_fr' => 'Produit Exemple FR',
                'description_en' => 'Sample description in English',
                'description_ar' => 'وصف العينة باللغة العربية',
                'description_fr' => 'Description de l\'exemple en français',
                'has_variation' => '0',
                'tax' => '10',
                'selling_price_for_user' => '100.00',
                'min_order_for_user' => '1',
                'min_order_for_wholesale' => '10',
                'in_stock' => '50',
                'status' => '1',
                'category_id' => '1',
                'unit_id' => '1',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'number',
            'barcode',
            'name_en',
            'name_ar',
            'name_fr',
            'description_en',
            'description_ar',
            'description_fr',
            'has_variation',
            'tax',
            'selling_price_for_user',
            'min_order_for_user',
            'min_order_for_wholesale',
            'in_stock',
            'status',
            'category_id',
            'unit_id',
        ];
    }
}
