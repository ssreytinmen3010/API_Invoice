<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discount;

class DiscountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /discounts
     * List all discounts
     */
    public function index()
    {
        $discounts = Discount::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $discounts
        ]);
    }

    /**
     * POST /discounts
     * Create discount (percent OR amount)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'percent' => 'nullable|numeric|min:0|max:100',
            'amount'  => 'nullable|numeric|min:0',
        ]);

        // Ensure at least one value exists
        if (!$request->percent && !$request->amount) {
            return response()->json([
                'status' => false,
                'message' => 'Either percent or amount is required'
            ], 422);
        }

        $discount = Discount::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Discount created successfully',
            'data' => $discount
        ], 201);
    }

    /**
     * GET /discounts/{id}
     */
    public function show($id)
    {
        $discount = Discount::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $discount
        ]);
    }

    /**
     * PUT /discounts/{id}
     */
    public function update(Request $request, $id)
    {
        $discount = Discount::findOrFail($id);

        $validated = $request->validate([
            'percent' => 'nullable|numeric|min:0|max:100',
            'amount'  => 'nullable|numeric|min:0',
        ]);

        if (!$request->percent && !$request->amount) {
            return response()->json([
                'status' => false,
                'message' => 'Either percent or amount is required'
            ], 422);
        }

        $discount->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Discount updated successfully',
            'data' => $discount
        ]);
    }

    /**
     * DELETE /discounts/{id}
     */
    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return response()->json([
            'status' => true,
            'message' => 'Discount deleted successfully'
        ]);
    }
}
