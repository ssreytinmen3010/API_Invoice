<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class FileController extends Controller
{
    /**
     * List all uploaded files
     */
    public function index()
    {
        $files = File::all()->map(function($file) {
            return [
                'id' => $file->id,
                'file_path' => $file->file_path,
                'file_type' => $file->file_type,
                'url' => asset('storage/' . $file->file_path)
            ];
        });

        return response()->json([
            'status' => true,
            'files' => $files
        ]);
    }

    /**
     * Upload a file (image or document)
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // max 10MB
        ]);

        // Save file in storage/app/public/uploads
        $path = $request->file('file')->store('uploads', 'public');

        $file = File::create([
            'file_path' => $path,
            'file_type' => $request->file('file')->getClientOriginalExtension(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'File uploaded successfully',
            'file' => [
                'id' => $file->id,
                'file_path' => $file->file_path,
                'file_type' => $file->file_type,
                'url' => asset('storage/' . $file->file_path)
            ]
        ]);
    }

    /**
     * Show single file
     */
    public function show($id)
    {
        $file = File::findOrFail($id);
        return response()->json([
            'status' => true,
            'file' => [
                'id' => $file->id,
                'file_path' => $file->file_path,
                'file_type' => $file->file_type,
                'url' => asset('storage/' . $file->file_path)
            ]
        ]);
    }

    /**
     * Delete a file
     */
    public function destroy($id)
    {
        $file = File::findOrFail($id);

        // Delete file from storage
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return response()->json([
            'status' => true,
            'message' => 'File deleted successfully'
        ]);
    }
public function upload(Request $request)
{
    
    try {
        // Validate input
        $request->validate([
            'file' => 'required|file|max:5120', // max 10MB
        ]);

        // Save the file in storage/app/public/uploads
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads', 'public');

            // Save record in database
            $file = File::create([
                'file_path' => $path,
                'file_type' => $request->file('file')->getClientOriginalExtension(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'File uploaded successfully',
                'file' => $file
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No file found in request'
            ], 400);
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors
        return response()->json([
            'status' => false,
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        // Handle any other errors
        return response()->json([
            'status' => false,
            'message' => 'File upload failed',
            'error' => $e->getMessage()
        ], 500);
    }
}
// public function update(Request $request, $id)
// {
//     try {
//         // Find existing file record
//         $fileRecord = File::find($id);
//         if (!$fileRecord) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'File record not found'
//             ], 404);
//         }

//         // Check if a file is uploaded
//         if (!$request->hasFile('file')) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'No file found in request',
//                 'all_inputs' => $request->all(),
//                 'files' => $request->files->all()
//             ], 400);
//         }

//         $file = $request->file('file');

//         // Validate file
//         $validator = \Validator::make($request->all(), [
//             'file' => 'required|file|max:10240', // 10MB
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'status' => false,
//                 'errors' => $validator->errors()
//             ], 422);
//         }

//         // Delete old file from storage if exists
//         if (\Storage::disk('public')->exists($fileRecord->file_path)) {
//             \Storage::disk('public')->delete($fileRecord->file_path);
//         }

//         // Save new file with unique name
//         $filename = time() . '_' . $file->getClientOriginalName();
//         $path = $file->storeAs('uploads', $filename, 'public');

//         // Update DB record
//         $fileRecord->update([
//             'file_path' => $path,
//             'file_type' => $file->getClientOriginalExtension(),
//         ]);

//         return response()->json([
//             'status' => true,
//             'message' => 'File updated successfully',
//             'file' => $fileRecord,
//             'url' => asset('storage/' . $path) // public URL
//         ], 200);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => false,
//             'message' => 'File update failed',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }

public function update(Request $request, $id)
{
    try {
        // Find existing file record
        $fileRecord = File::find($id);
        if (!$fileRecord) {
            return response()->json([
                'status' => false,
                'message' => 'File not found'
            ], 404);
        }

        // Validate file
        $request->validate([
            'file' => 'required|file|max:5120', // 10MB
        ]);

        // Check if file exists in request
        if (!$request->hasFile('file')) {
            return response()->json([
                'status' => false,
                'message' => 'No file found in request'
            ], 400);
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();

        // Save new file
        $path = $file->storeAs('uploads', $filename, 'public');

        // Delete old file if exists
        if ($fileRecord->file_path && Storage::disk('public')->exists($fileRecord->file_path)) {
            Storage::disk('public')->delete($fileRecord->file_path);
        }

        // Update database record
        $fileRecord->update([
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'File updated successfully',
            'file' => $fileRecord,
            'url' => asset('storage/' . $path)
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Return validation errors
        return response()->json([
            'status' => false,
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        // Return any other errors
        return response()->json([
            'status' => false,
            'message' => 'File update failed',
            'error' => $e->getMessage()
        ], 500);
    }
}


}
