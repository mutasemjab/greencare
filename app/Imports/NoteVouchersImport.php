<?php

namespace App\Imports;

use App\Models\NoteVoucher;
use App\Models\Product;
use App\Models\VoucherProduct;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class NoteVouchersImport implements ToModel, WithHeadingRow
{
    private $noteVoucher; // Store the created NoteVoucher

    public function model(array $row)
    {
        // Log the row to help with debugging
        Log::info('Row data:', $row);

        // Validate required fields
        $requiredFields = [
            'note_voucher_type_id',
            'number',
            'date_note_voucher',
            'from_warehouse_id',
            'shop_id',
            'product_name_en',
            'quantity',
            'purchasing_price',
        ];

        foreach ($requiredFields as $field) {
            if (!isset($row[$field])) {
                Log::error("Missing field: $field");
                throw new \Exception("Missing field: $field");
            }
        }

        // If the NoteVoucher hasn't been created yet, create it now
        if (!$this->noteVoucher) {
            $this->noteVoucher = NoteVoucher::create([
                'note_voucher_type_id' => $row['note_voucher_type_id'],
                'number' => $row['number'],
                'date_note_voucher' => $row['date_note_voucher'],
                'from_warehouse_id' => $row['from_warehouse_id'],
                'shop_id' => $row['shop_id'],
            ]);
        }

        // Retrieve the product ID based on the product_name_en
        $product = Product::where('name_en', $row['product_name_en'])->first();

        if (!$product) {
            Log::error("Product not found: " . $row['product_name_en']);
            throw new \Exception("Product not found: " . $row['product_name_en']);
        }

        // Create the VoucherProduct record linked to the existing NoteVoucher
        VoucherProduct::create([
            'product_id' => $product->id,
            'quantity' => $row['quantity'],
            'purchasing_price' => $row['purchasing_price'],
            'unit_id' => $product->unit->id, // Assuming product has a unit_id column
            'note_voucher_id' => $this->noteVoucher->id,
        ]);

        return null; // No need to return the NoteVoucher each time
    }
}
