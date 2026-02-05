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
        $request->validate(['name' => 'required|string', 'stock' => 'sometimes|integer', 'stock_warehouse' => 'sometimes|integer']);
        $name = $request->input('name');
        $stock = (int) $request->input('stock', 0);
        $stockWarehouse = (int) $request->input('stock_warehouse', 0);
        $brochure = Brochure::create(['name' => $name, 'stock' => $stock, 'stock_warehouse' => $stockWarehouse]);
        return response()->json(['id' => $brochure->id, 'name' => $name, 'stock' => $brochure->stock]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $brochure = Brochure::findOrFail($id);
        $request->validate(['name' => 'sometimes|string', 'stock' => 'sometimes|integer', 'stock_warehouse' => 'sometimes|integer']);
        $brochure->fill($request->only(['name', 'stock', 'stock_warehouse']));
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

    /**
     * 화성 물류창고 재고만 변경 (신청 시 출고 차감용). 본사 재고(stock)는 변경하지 않음.
     */
    public function updateWarehouseStock(Request $request, string $id): JsonResponse
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
        $beforeStock = $brochure->stock_warehouse ?? 0;
        $newStock = $beforeStock + $quantity;
        if ($newStock < 0) {
            return response()->json(['error' => '화성 물류창고 재고가 부족합니다.', 'stock_warehouse' => $beforeStock], 400);
        }
        $brochure->update([
            'stock_warehouse' => $newStock,
            'last_warehouse_stock_quantity' => $quantity,
            'last_warehouse_stock_date' => $date,
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
                'memo' => '[화성물류] ' . $memo,
            ]);
        }
        return response()->json(['success' => true, 'stock_warehouse' => $newStock]);
    }

    /**
     * 물류창고 재고에서 본사 재고로 이동 (물류 -N, 본사 +N).
     */
    public function transferToHq(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'date' => 'sometimes|string',
            'memo' => 'nullable|string|max:1000',
        ]);
        $brochure = Brochure::findOrFail($id);
        $quantity = (int) $request->input('quantity');
        $date = $request->input('date', now()->format('Y-m-d'));
        $memo = $request->input('memo');
        $warehouseBefore = $brochure->stock_warehouse ?? 0;
        $hqBefore = $brochure->stock ?? 0;
        if ($warehouseBefore < $quantity) {
            return response()->json([
                'error' => '물류창고 재고가 부족합니다.',
                'stock_warehouse' => $warehouseBefore,
            ], 400);
        }
        $brochure->update([
            'stock_warehouse' => $warehouseBefore - $quantity,
            'stock' => $hqBefore + $quantity,
            'last_stock_quantity' => $quantity,
            'last_stock_date' => $date,
        ]);
        $memoText = '물류창고→본사 이동' . ($memo ? ' - ' . $memo : '');
        StockHistory::create([
            'type' => '이동',
            'date' => $date,
            'brochure_id' => $brochure->id,
            'brochure_name' => $brochure->name,
            'quantity' => $quantity,
            'contact_name' => '',
            'schoolname' => '',
            'before_stock' => $warehouseBefore,
            'after_stock' => $warehouseBefore - $quantity,
            'memo' => $memoText,
        ]);
        return response()->json([
            'success' => true,
            'stock_warehouse' => $warehouseBefore - $quantity,
            'stock' => $hqBefore + $quantity,
        ]);
    }
}
