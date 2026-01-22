<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Signature;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * List all signatures
     */
    public function index()
    {
        $signatures = Signature::with('image')->get();

        return response()->json([
            'status' => true,
            'data' => $signatures
        ]);
    }

    /**
     * Store a new signature
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|file|max:10240', // max 10MB
        ]);

        $path = $request->file('image')->store('uploads', 'public');

        $file = File::create([
            'file_path' => $path,
            'file_type' => $request->file('image')->getClientOriginalExtension()
        ]);

        $signature = Signature::create([
            'image_id' => $file->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Signature uploaded successfully',
            'data' => $signature
        ], 201);
    }

    /**
     * Show single signature
     */
    public function show($id)
    {
        $signature = Signature::with('image')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $signature
        ]);
    }

    /**
     * Update signature
     */
    public function update(Request $request, $id)
    {
        $signature = Signature::findOrFail($id);

        $request->validate([
            'image' => 'required|file|max:10240', // max 10MB
        ]);

        // Delete old image
        if ($signature->image) {
            Storage::disk('public')->delete($signature->image->file_path);
            $signature->image->delete();
        }

        $path = $request->file('image')->store('uploads', 'public');

        $file = File::create([
            'file_path' => $path,
            'file_type' => $request->file('image')->getClientOriginalExtension()
        ]);

        $signature->update([
            'image_id' => $file->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Signature updated',
            'data' => $signature
        ]);
    }

    /**
     * Delete signature
     */
    public function destroy($id)
    {
        $signature = Signature::findOrFail($id);

        // Delete file
        if ($signature->image) {
            Storage::disk('public')->delete($signature->image->file_path);
            $signature->image->delete();
        }

        $signature->delete();

        return response()->json([
            'status' => true,
            'message' => 'Signature deleted'
        ]);
    }
}
