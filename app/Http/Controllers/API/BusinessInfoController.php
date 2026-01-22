<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessInfo;
use Illuminate\Support\Facades\Storage;

class BusinessInfoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * List all business info for logged-in user
     */
    public function index()
    {
        $infos = BusinessInfo::with(['user', 'vatCustomer', 'image'])->get();

        return response()->json([
            'status' => true,
            'data' => $infos
        ]);
    }

    /**
     * Store new business info
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'vat_customer_id' => 'nullable|exists:vat_customers,id',
            'address' => 'nullable|string',
            'terms' => 'nullable|string',
            'image' => 'nullable|file|max:10240', // 10MB
        ]);

        $data = $request->only(['name','phone','vat_customer_id','address','terms']);
        $data['user_id'] = $request->user()->id;

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads', 'public');
            $data['image_id'] = \App\Models\File::create([
                'file_path' => $path,
                'file_type' => $request->file('image')->getClientOriginalExtension()
            ])->id;
        }

        $businessInfo = BusinessInfo::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Business info created',
            'data' => $businessInfo
        ], 201);
    }

    /**
     * Show single business info
     */
    public function show($id)
    {
        $info = BusinessInfo::with(['user','vatCustomer','image'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $info
        ]);
    }

    /**
     * Update business info
     */
    public function update(Request $request, $id)
    {
        $info = BusinessInfo::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string',
            'phone' => 'sometimes|string',
            'vat_customer_id' => 'nullable|exists:vat_customers,id',
            'address' => 'nullable|string',
            'terms' => 'nullable|string',
            'image' => 'nullable|file|max:10240',
        ]);

        $data = $request->only(['name','phone','vat_customer_id','address','terms']);

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image
            if ($info->image) {
                Storage::disk('public')->delete($info->image->file_path);
                $info->image->delete();
            }

            $path = $request->file('image')->store('uploads', 'public');
            $data['image_id'] = \App\Models\File::create([
                'file_path' => $path,
                'file_type' => $request->file('image')->getClientOriginalExtension()
            ])->id;
        }

        $info->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Business info updated',
            'data' => $info
        ]);
    }

    /**
     * Delete business info
     */
    public function destroy($id)
    {
        $info = BusinessInfo::findOrFail($id);

        // Delete image file
        if ($info->image) {
            Storage::disk('public')->delete($info->image->file_path);
            $info->image->delete();
        }

        $info->delete();

        return response()->json([
            'status' => true,
            'message' => 'Business info deleted'
        ]);
    }
}
