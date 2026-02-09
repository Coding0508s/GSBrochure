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
        $request->validate([
            'name' => 'required|string|unique:brochures,name',
            'stock' => 'sometimes|integer',
            'stock_warehouse' => 'sometimes|integer',
        ], [
            'name.unique' => '이미 같은 이름의 브로셔가 있습니다. 다른 이름을 입력해 주세요.',
        ]);
        $name = trim((string) $request->input('name'));
        $stock = (int) $request->input('stock', 0);
        $stockWarehouse = (int) $request->input('stock_warehouse', 0);
        try {
            $brochure = Brochure::create(['name' => $name, 'stock' => $stock, 'stock_warehouse' => $stockWarehouse]);
            $date = now()->format('Y-m-d');
            $base = [
                'brochure_id' => $brochure->id,
                'brochure_name' => $name,
                'date' => $date,
                'contact_name' => '',
                'schoolname' => '',
                'memo' => null,
            ];
            StockHistory::create(array_merge($base, [
                'type' => '등록',
                'location' => 'warehouse',
                'quantity' => $stockWarehouse,
                'before_stock' => 0,
                'after_stock' => $stockWarehouse,
            ]));
            StockHistory::create(array_merge($base, [
                'type' => '등록',
                'location' => 'hq',
                'quantity' => $stock,
                'before_stock' => 0,
                'after_stock' => $stock,
            ]));
            return response()->json(['id' => $brochure->id, 'name' => $name, 'stock' => $brochure->stock]);
        } catch (\Throwable $e) {
            \Log::error('Brochure store error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'error' => $e->getMessage() ?: '브로셔 저장 중 오류가 발생했습니다.',
            ], 500);
        }
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
        $brochure = Brochure::findOrFail($id);
        StockHistory::where('brochure_id', $id)->delete();
        try {
            $brochure->delete();
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'foreign key') || $e->getCode() === '23000') {
                return response()->json([
                    'error' => '이 브로셔는 발송 내역이 있어 삭제할 수 없습니다.',
                ], 422);
            }
            throw $e;
        }
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
                'location' => 'hq',
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
                'location' => 'warehouse',
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
        // 물류센터 입출고 내역용 (물류 재고 감소)
        StockHistory::create([
            'type' => '이동',
            'location' => 'warehouse',
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
        // 본사 입출고 내역용 (본사 재고 증가)
        StockHistory::create([
            'type' => '이동',
            'location' => 'hq',
            'date' => $date,
            'brochure_id' => $brochure->id,
            'brochure_name' => $brochure->name,
            'quantity' => $quantity,
            'contact_name' => '',
            'schoolname' => '',
            'before_stock' => $hqBefore,
            'after_stock' => $hqBefore + $quantity,
            'memo' => $memoText,
        ]);
        return response()->json([
            'success' => true,
            'stock_warehouse' => $warehouseBefore - $quantity,
            'stock' => $hqBefore + $quantity,
        ]);
    }
}
