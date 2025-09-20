<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Shop;

class InventoryReportExport implements FromView
{
    protected $reportData;
    protected $totalPurchasingValue;
    protected $shopId;
    protected $toDate;

    public function __construct($reportData, $totalPurchasingValue, $shopId, $toDate)
    {
        $this->reportData = $reportData;
        $this->totalPurchasingValue = $totalPurchasingValue;
        $this->shopId = $shopId;
        $this->toDate = $toDate;
    }

    public function view(): View
    {
        return view('exports.inventory_report_export', [
            'reportData' => $this->reportData,
            'totalPurchasingValue' => $this->totalPurchasingValue,
        ]);
    }
}