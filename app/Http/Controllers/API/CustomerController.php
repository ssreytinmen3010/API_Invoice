<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;


use Illuminate\Auth\Events\Validated;
use App\Http\Middleware\Authenticate;
use Illuminate\Foundation\Http\Kernel as HttpKernel;



class CustomerController extends Controller
{
    /**
     * Require authentication for all functions
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET: List customers
     */
    public function index()
    {
        $customers = Customer::with('vatCustomer')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $customers,
        ]);
    }

    /**
     * POST: Store new customer
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'alternative_phone' => 'nullable|string|max:20',
            'vat_customer_id' => 'nullable|exists:vat_customers,id',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $customer = Customer::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Customer created successfully',
            'data' => $customer,
        ], 201);
    }

    /**
     * GET: Show single customer
     */
    public function show($id)
    {
        $customer = Customer::with('vatCustomer')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $customer,
        ]);
    }

    /**
     * PUT: Update customer
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'alternative_phone' => 'nullable|string|max:20',
            'vat_customer_id' => 'nullable|exists:vat_customers,id',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $customer->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Customer updated successfully',
            'data' => $customer,
        ]);
    }

    /**
     * DELETE: Remove customer
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json([
            'status' => true,
            'message' => 'Customer deleted successfully',
        ]);
    }
}
