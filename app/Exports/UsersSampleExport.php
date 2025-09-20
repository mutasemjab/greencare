<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersSampleExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Provide a sample row with column names as examples
        return collect([
            [
                'name' => 'ahmed',
                'email' => 'ahmed@gmail.com',
                'phone' => '+962795970357',
                'user_type' => '2',
                'country_id' => '1',
                'representative_id' => '1',

            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'email',
            'phone',
            'user_type',
            'country_id',
            'representative_id',
        ];
    }
}
