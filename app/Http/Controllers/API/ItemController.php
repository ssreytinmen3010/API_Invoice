<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of all items with detailed info
     */
    public function index()
    {
        // Eager load image to avoid N+1 queries
        $items = Item::with('image')->get();

        // Build detailed array for each item
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'item_code' => $item->item_code,
                'note' => $item->note,
                'image_id' => $item->image_id,
                'image_url' => $item->image ? $item->image->getFileUrl() : null,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        }

        return response()->json([
            'status' => true,
            'items' => $data
        ]);
    }

    /**
     * Store a newly created item
     */
    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'item_name' => 'required|string|max:255',
            'item_code' => 'required|string|max:255|unique:items,item_code',
            'note' => 'nullable|string',
            'image_id' => 'nullable|exists:files,id',
        ]);

        // Create the item
        $item = Item::create($request->only(['item_name', 'item_code', 'note', 'image_id']));

        // Load image relation
        $item->load('image');

        return response()->json([
            'status' => true,
            'message' => 'Item created successfully',
            'item' => [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'item_code' => $item->item_code,
                'note' => $item->note,
                'image_id' => $item->image_id,
                'image_url' => $item->image ? $item->image->getFileUrl() : null,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]
        ], 201);
    }

    /**
     * Display the specified item with details
     */
    public function show($id)
    {
        $item = Item::with('image')->findOrFail($id);

        return response()->json([
            'status' => true,
            'item' => [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'item_code' => $item->item_code,
                'note' => $item->note,
                'image_id' => $item->image_id,
                'image_url' => $item->image ? $item->image->getFileUrl() : null,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]
        ]);
    }

    /**
     * Update the specified item with detailed info
     */
/**
 * Update the specified item
 */
public function update(Request $request, $id)
{
    $item = Item::findOrFail($id);

    $request->validate([
        'item_name' => 'required|string|max:255',
        'item_code' => 'required|string|max:255|unique:items,item_code,' . $item->id,
        'note' => 'nullable|string',
        'image_id' => 'nullable|exists:files,id', // check image exists
    ]);

    $item->update([
        'item_name' => $request->item_name,
        'item_code' => $request->item_code,
        'note' => $request->note ?? null,
        'image_id' => $request->image_id ?? null, // update image_id
    ]);

    $item->load('image');

    return response()->json([
        'status' => true,
        'message' => 'Item updated successfully',
        'item' => [
            'id' => $item->id,
            'item_name' => $item->item_name,
            'item_code' => $item->item_code,
            'note' => $item->note,
            'image_id' => $item->image_id,
            'image_url' => $item->image ? $item->image->getFileUrl() : null,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
        ]
    ]);
}



    /**
     * Remove the specified item
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item deleted successfully'
        ]);
    }
}
