<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * List all payments
     */
    public function index()
    {
        $payments = Payment::with('bankImage')->get();

        return response()->json([
            'status' => true,
            'data' => $payments
        ]);
    }

    /**
     * Store a new payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_info' => 'required|string',
            'bank_image' => 'nullable|file|max:10240', // 10MB
        ]);

        $data = ['payment_info' => $request->payment_info];

        if ($request->hasFile('bank_image')) {
            $path = $request->file('bank_image')->store('uploads', 'public');
            $file = File::create([
                'file_path' => $path,
                'file_type' => $request->file('bank_image')->getClientOriginalExtension()
            ]);
            $data['bank_image_id'] = $file->id;
        }

        $payment = Payment::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Payment created',
            'data' => $payment
        ], 201);
    }

    /**
     * Show single payment
     */
    public function show($id)
    {
        $payment = Payment::with('bankImage')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $payment
        ]);
    }

    /**
     * Update payment
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'payment_info' => 'sometimes|string',
            'bank_image' => 'nullable|file|max:10240',
        ]);

        $data = $request->only('payment_info');

        if ($request->hasFile('bank_image')) {
            // Delete old image
            if ($payment->bankImage) {
                Storage::disk('public')->delete($payment->bankImage->file_path);
                $payment->bankImage->delete();
            }

            $path = $request->file('bank_image')->store('uploads', 'public');
            $file = File::create([
                'file_path' => $path,
                'file_type' => $request->file('bank_image')->getClientOriginalExtension()
            ]);

            $data['bank_image_id'] = $file->id;
        }

        $payment->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Payment updated',
            'data' => $payment
        ]);
    }

    /**
     * Delete payment
     */
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);

        // Delete bank image
        if ($payment->bankImage) {
            Storage::disk('public')->delete($payment->bankImage->file_path);
            $payment->bankImage->delete();
        }

        $payment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Payment deleted'
        ]);
    }
}
