<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brochure;
use App\Models\StockHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrochureController extends Controller
{
    public function health(Request $request): JsonResponse
    {
        try {
            DB::connection()->getPdo();
            $driver = DB::connection()->getDriverName();
            $tableList = $driver === 'pgsql'
                ? array_column(DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename"), 'tablename')
                : array_map(fn ($t) => $t->name, DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name"));
            return response()->json([
                'ok' => true,
                'database' => 'connected',
                'driver' => $driver,
                'tables' => array_values($tableList),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function index(): JsonResponse
    {
        $brochures = Brochure::orderBy('id')->get();
        return response()->json($brochures);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string', 'stock' => 'sometimes|integer']);
        $name = $request->input('name');
        $stock = (int) $request->input('stock', 0);
        $brochure = Brochure::create(['name' => $name, 'stock' => $stock]);
        return response()->json(['id' => $brochure->id, 'name' => $name, 'stock' => $brochure->stock]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $brochure = Brochure::findOrFail($id);
        $request->validate(['name' => 'sometimes|string', 'stock' => 'sometimes|integer']);
        $brochure->fill($request->only(['name', 'stock']));
        $brochure->save();
        return response()->json(['success' => true]);
    }

    public function destroy(string $id): JsonResponse
    {
        Brochure::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function updateStock(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer',
            'date' => 'sometimes|string',
            'memo' => 'nullable|string|max:1000',
        ]);
        $brochure = Brochure::findOrFail($id);
        $quantity = (int) $request->input('quantity');
        $date = $request->input('date', now()->format('Y-m-d'));
        $memo = $request->input('memo');
        $beforeStock = $brochure->stock;
        $newStock = $beforeStock + $quantity;
        $brochure->update([
            'stock' => $newStock,
            'last_stock_quantity' => $quantity,
            'last_stock_date' => $date,
        ]);
        if ($memo !== null && $memo !== '') {
            StockHistory::create([
                'type' => '수정',
                'date' => $date,
                'brochure_id' => $brochure->id,
                'brochure_name' => $brochure->name,
                'quantity' => $quantity,
                'before_stock' => $beforeStock,
                'after_stock' => $newStock,
                'memo' => $memo,
            ]);
        }
        return response()->json(['success' => true, 'stock' => $newStock]);
    }
}
