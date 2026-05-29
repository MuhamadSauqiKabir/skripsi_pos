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
            'floor' => ['required', 'integer', 'min:1', 'max:3'],
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

    public function update(Request $request, DiningTable $table): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'floor' => ['required', 'integer', 'min:1', 'max:3'],
            'is_active' => ['required', 'boolean'],
        ]);

        $table->update($data);

        $this->auditLogService->log($request->user()?->id, 'table.updated', 'dining_table', $table->id, $data, $request);

        return back()->with('success', 'Data meja berhasil diperbarui.');
    }

    public function destroy(DiningTable $table): RedirectResponse
    {
        if ($table->orders()->whereIn('status', ['paid', 'brewing', 'ready'])->exists()) {
            return back()->with('error', 'Meja tidak bisa dihapus karena masih memiliki pesanan aktif.');
        }

        $tableId = $table->id;
        $table->delete();

        $this->auditLogService->log(auth()->id(), 'table.deleted', 'dining_table', $tableId, null, request());

        return back()->with('success', 'Meja berhasil dihapus.');
    }
}
