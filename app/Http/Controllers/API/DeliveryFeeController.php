<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryFee;

class DeliveryFeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /delivery-fees
     * List all delivery fees
     */
    public function index()
    {
        $deliveryFees = DeliveryFee::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $deliveryFees
        ]);
    }

    /**
     * POST /delivery-fees
     * Create delivery fee
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $deliveryFee = DeliveryFee::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Delivery fee created successfully',
            'data' => $deliveryFee
        ], 201);
    }

    /**
     * GET /delivery-fees/{id}
     */
    public function show($id)
    {
        $deliveryFee = DeliveryFee::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $deliveryFee
        ]);
    }

    /**
     * PUT /delivery-fees/{id}
     */
    public function update(Request $request, $id)
    {
        $deliveryFee = DeliveryFee::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $deliveryFee->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Delivery fee updated successfully',
            'data' => $deliveryFee
        ]);
    }

    /**
     * DELETE /delivery-fees/{id}
     */
    public function destroy($id)
    {
        $deliveryFee = DeliveryFee::findOrFail($id);
        $deliveryFee->delete();

        return response()->json([
            'status' => true,
            'message' => 'Delivery fee deleted successfully'
        ]);
    }
}
