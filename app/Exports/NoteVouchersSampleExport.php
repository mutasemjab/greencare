<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NoteVouchersSampleExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Provide a sample row with column names as examples
        return collect([
            [
                'note_voucher_type_id' => '1',
                'number' => '1',
                'date_note_voucher' => '2024-09-01',
                'from_warehouse_id' => '1',
                'shop_id' => '1',
                'product_name_en' => 'V-1151 pencil tube',
                'quantity' => '2',
                'purchasing_price' => '1.5',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'note_voucher_type_id',
            'number',
            'date_note_voucher',
            'from_warehouse_id',
            'shop_id',
            'product_name_en',
            'quantity',
            'purchasing_price',

        ];
    }
}
