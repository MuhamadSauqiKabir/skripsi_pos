<?php

namespace App\Exports;

use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class WeeklySalesExport implements FromView
{
    public function __construct(
        private readonly array $report,
        private readonly CarbonInterface $startDate,
        private readonly CarbonInterface $endDate,
    ) {
    }

    public function view(): View
    {
        return view('exports.weekly-sales', [
            'report' => $this->report,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}
