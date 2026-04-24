<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use App\Services\AuditLogService;
use App\Services\TableQrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TableController extends Controller
{
    public function __construct(
        private readonly TableQrCodeService $tableQrCodeService,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'coordinate_x' => ['required', 'numeric'],
            'coordinate_y' => ['required', 'numeric'],
        ]);

        $table = DiningTable::create([
            ...$data,
            'public_token' => Str::lower(Str::random(14)),
        ]);

        $this->tableQrCodeService->refresh($table);
        $this->auditLogService->log($request->user()?->id, 'table.created', 'dining_table', $table->id, $data, $request);

        return back()->with('success', 'Meja dan QR code berhasil dibuat.');
    }
}
