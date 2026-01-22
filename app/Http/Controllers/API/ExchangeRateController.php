<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExchangeRate;

class ExchangeRateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /exchange-rates
     * List all exchange rates
     */
    public function index()
    {
        $rates = ExchangeRate::orderBy('currency')->get();

        return response()->json([
            'status' => true,
            'data' => $rates
        ]);
    }

    /**
     * POST /exchange-rates
     * Create new exchange rate
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string|max:10',
            'rate'     => 'required|numeric|min:0',
        ]);

        $rate = ExchangeRate::create([
            'currency'  => $validated['currency'],
            'rate'      => $validated['rate'],
            'is_active' => false, // default OFF
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Exchange rate created successfully',
            'data' => $rate
        ], 201);
    }

    /**
     * GET /exchange-rates/{id}
     */
    public function show($id)
    {
        $rate = ExchangeRate::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $rate
        ]);
    }

    /**
     * PUT /exchange-rates/{id}
     */
    public function update(Request $request, $id)
    {
        $rate = ExchangeRate::findOrFail($id);

        $validated = $request->validate([
            'currency' => 'required|string|max:10',
            'rate'     => 'required|numeric|min:0',
        ]);

        $rate->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Exchange rate updated successfully',
            'data' => $rate
        ]);
    }

    /**
     * POST /exchange-rates/{id}/toggle
     * Activate selected exchange rate (ONLY ONE ACTIVE)
     */
    public function toggle($id)
    {
        // Turn OFF all exchange rates
        ExchangeRate::where('is_active', true)->update([
            'is_active' => false
        ]);

        // Turn ON selected rate
        $rate = ExchangeRate::findOrFail($id);
        $rate->update([
            'is_active' => true
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Exchange rate activated successfully',
            'data' => $rate
        ]);
    }

    /**
     * DELETE /exchange-rates/{id}
     */
    public function destroy($id)
    {
        $rate = ExchangeRate::findOrFail($id);
        $rate->delete();

        return response()->json([
            'status' => true,
            'message' => 'Exchange rate deleted successfully'
        ]);
    }
}
