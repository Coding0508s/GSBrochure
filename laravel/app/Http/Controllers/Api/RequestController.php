<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BrochureRequest;
use App\Models\Invoice;
use App\Models\RequestItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index(): JsonResponse
    {
        $requests = BrochureRequest::with(['requestItems', 'invoices'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function (BrochureRequest $req) {
                return [
                    'id' => $req->id,
                    'date' => $req->date,
                    'schoolname' => $req->schoolname,
                    'address' => $req->address,
                    'phone' => $req->phone,
                    'contact_id' => $req->contact_id,
                    'contact_name' => $req->contact_name,
                    'submitted_at' => $req->created_at?->toIso8601String(),
                    'updated_at' => $req->updated_at?->toIso8601String(),
                    'items' => $req->requestItems->map(fn ($ri) => [
                        'brochure_id' => $ri->brochure_id,
                        'brochure_name' => $ri->brochure_name,
                        'quantity' => $ri->quantity,
                    ])->values()->all(),
                    'invoices' => $req->invoices->pluck('invoice_number')->all(),
                ];
            });
        return response()->json($requests);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'date' => 'required|string',
            'schoolname' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'contact_id' => 'nullable|integer',
            'contact_name' => 'nullable|string',
            'brochures' => 'required|array',
            'brochures.*.brochure' => 'required',
            'brochures.*.brochureName' => 'required|string',
            'brochures.*.quantity' => 'required|integer|min:1',
            'invoices' => 'sometimes|array',
            'invoices.*' => 'string',
        ]);
        $invoices = $data['invoices'] ?? [];

        DB::beginTransaction();
        try {
            $req = BrochureRequest::create([
                'date' => $data['date'],
                'schoolname' => $data['schoolname'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'contact_id' => $data['contact_id'] ?? null,
                'contact_name' => $data['contact_name'] ?? null,
            ]);
            foreach ($data['brochures'] as $b) {
                RequestItem::create([
                    'request_id' => $req->id,
                    'brochure_id' => $b['brochure'],
                    'brochure_name' => $b['brochureName'],
                    'quantity' => $b['quantity'],
                ]);
            }
            foreach ($invoices as $inv) {
                if (trim($inv) !== '') {
                    Invoice::create(['request_id' => $req->id, 'invoice_number' => trim($inv)]);
                }
            }
            DB::commit();
            return response()->json(['id' => $req->id]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'date' => 'required|string',
            'schoolname' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'contact_id' => 'nullable|integer',
            'contact_name' => 'nullable|string',
            'brochures' => 'sometimes|array',
            'brochures.*.brochure' => 'required',
            'brochures.*.brochureName' => 'required|string',
            'brochures.*.quantity' => 'required|integer|min:1',
        ]);
        $req = BrochureRequest::findOrFail($id);

        DB::beginTransaction();
        try {
            $req->update([
                'date' => $data['date'],
                'schoolname' => $data['schoolname'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'contact_id' => $data['contact_id'] ?? null,
                'contact_name' => $data['contact_name'] ?? null,
            ]);
            RequestItem::where('request_id', $id)->delete();
            foreach ($data['brochures'] ?? [] as $b) {
                RequestItem::create([
                    'request_id' => $req->id,
                    'brochure_id' => $b['brochure'],
                    'brochure_name' => $b['brochureName'],
                    'quantity' => $b['quantity'],
                ]);
            }
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(string $id): JsonResponse
    {
        BrochureRequest::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function addInvoices(Request $request, string $id): JsonResponse
    {
        $data = $request->validate(['invoices' => 'required|array', 'invoices.*' => 'string']);
        BrochureRequest::findOrFail($id);
        foreach ($data['invoices'] as $inv) {
            if (trim($inv) !== '') {
                Invoice::create(['request_id' => $id, 'invoice_number' => trim($inv)]);
            }
        }
        return response()->json(['success' => true]);
    }

    public function deleteInvoices(string $id): JsonResponse
    {
        Invoice::where('request_id', $id)->delete();
        return response()->json(['success' => true]);
    }
}
