<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Log the row to help with debugging
        Log::info('Row data:', $row);

        // Validate required fields
        $requiredFields = [
            'name',
            'email',
            'password',
            'phone',
            'user_type',
            'country_id',
            'representative_id',
        ];

      /*  foreach ($requiredFields as $field) {
            if (!isset($row[$field])) {
                Log::error("Missing field: $field");
                throw new \Exception("Missing field: $field");
            }
        }*/

        return new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => $row['password'],
            'phone' => $row['phone'],
            'user_type' => $row['user_type'],
            'country_id' => $row['country_id'],
            'representative_id' => $row['representative_id'],
        ]);
    }
}
