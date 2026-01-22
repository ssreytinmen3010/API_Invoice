<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VatCustomer;

class VatCustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /vat-customers
     * List all VAT customers
     */
    public function index()
    {
        $vatCustomers = VatCustomer::orderBy('percent')->get();

        return response()->json([
            'status' => true,
            'data' => $vatCustomers
        ]);
    }

    /**
     * POST /vat-customers
     * Create new VAT customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'percent' => 'required|numeric|min:0|max:100',
        ]);

        $vatCustomer = VatCustomer::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'VAT customer created successfully',
            'data' => $vatCustomer
        ], 201);
    }

    /**
     * GET /vat-customers/{id}
     * Show single VAT customer
     */
    public function show($id)
    {
        $vatCustomer = VatCustomer::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $vatCustomer
        ]);
    }

    /**
     * PUT /vat-customers/{id}
     * Update VAT customer
     */
    public function update(Request $request, $id)
    {
        $vatCustomer = VatCustomer::findOrFail($id);

        $validated = $request->validate([
            'percent' => 'required|numeric|min:0|max:100',
        ]);

        $vatCustomer->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'VAT customer updated successfully',
            'data' => $vatCustomer
        ]);
    }

    /**
     * DELETE /vat-customers/{id}
     * Delete VAT customer
     */
    public function destroy($id)
    {
        $vatCustomer = VatCustomer::findOrFail($id);
        $vatCustomer->delete();

        return response()->json([
            'status' => true,
            'message' => 'VAT customer deleted successfully'
        ]);
    }
}
