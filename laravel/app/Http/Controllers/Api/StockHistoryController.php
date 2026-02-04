<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockHistoryController extends Controller
{
    public function index(): JsonResponse
    {
        $history = StockHistory::orderByDesc('created_at')->get();
        return response()->json($history);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|string',
            'date' => 'required|string',
            'brochure_id' => 'required|integer',
            'brochure_name' => 'required|string',
            'quantity' => 'required|integer',
            'contact_name' => 'nullable|string',
            'schoolname' => 'nullable|string',
            'before_stock' => 'required|integer',
            'after_stock' => 'required|integer',
        ]);
        StockHistory::create($data);
        return response()->json(['success' => true]);
    }
}
