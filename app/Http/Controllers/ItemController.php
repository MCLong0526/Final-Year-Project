<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //find the user information by using user_id in the items table
        $itemPerPage = request()->input('per_page', 10);
        $items = Item::with('user', 'pictures')
            ->when(request()->filled('type'), function ($query) {
                $query->whereIn('type', explode(',', request('type')));
            })
            ->when(request()->filled('condition'), function ($query) {
                $query->where('condition', 'like', '%'.request('condition').'%');
            })
            ->when(request()->filled('search'), function ($query) {
                $query->where('name', 'like', '%'.request('search').'%')
                    ->orWhere('description', 'like', '%'.request('search').'%');
            })
            ->when(request()->filled('sort_price'), function ($query) {
                $query->orderBy('price', request('sort_price'));
            })

            ->paginate($itemPerPage);

        return $this->success(data: $items, message: 'Items retrieved successfully');

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ItemRequest $request)
    {
        $item = Item::create($request->validated());

        // pass request image to saveItemPictures method
        $this->saveItemPictures($request, $item->item_id);

        return response()->json(['message' => 'Item created successfully'], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ItemRequest $request, string $id)
    {

        $item = Item::findOrFail($id);

        $item->update($request->validated());

        // remove the old item pictures in pictures table and save the new pictures
        $item->pictures()->delete();

        //save images like the saveItemPictures method, but don't use the method
        $images = $request->images;
        collect($images)->each(function ($image) use ($item) {
            $imageFormat = explode('/', mime_content_type($image['picture_path']))[1];
            $decodedImage = base64_decode(str_replace('data:image/'.$imageFormat.';base64,', '', $image['picture_path']));
            $fileName = uniqid().'.'.$imageFormat;
            Storage::disk('public')->put('images/items/'.$fileName, $decodedImage);
            $item->pictures()->create([
                'picture_path' => 'images/items/'.$fileName,
                'item_id' => $item->item_id,
            ]);
        });

        return $this->success(message: 'Item updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //the id in the table items is item_id
        $item = Item::findOrFail($id);
        $item->delete();

        return $this->success(message: 'Item deleted successfully');
    }

    public function getAuthItems()
    {
        $itemPerPage = request()->input('per_page', 10);
        $items = Item::with('pictures')
            ->where('user_id', auth()->id())
            ->when(request()->filled('search'), function ($query) {
                $query->where('name', 'like', '%'.request('search').'%');

            })
            ->paginate($itemPerPage);

        return $this->success(data: $items, message: 'Items retrieved successfully');
    }

    public function saveItemPictures(Request $request, string $id)
    {
        // Find the item by id
        $item = Item::findOrFail($id);
        // Get the images from the request
        $images = $request->images;

        // Use map to save each image
        collect($images)->each(function ($image) use ($item) {
            // Get the image format (png, jpg, jpeg)
            $imageFormat = explode('/', mime_content_type($image['url']))[1];
            // Decode the base64-encoded image URL
            $decodedImage = base64_decode(str_replace('data:image/'.$imageFormat.';base64,', '', $image['url']));

            // Generate a unique file name with the correct extension
            $fileName = uniqid().'.'.$imageFormat;

            // Store the image in the storage/app/public/images/items directory
            Storage::disk('public')->put('images/items/'.$fileName, $decodedImage);

            // Create a new item picture record in the database
            $item->pictures()->create([
                'picture_path' => 'images/items/'.$fileName,
                'item_id' => $item->item_id,
            ]);
        });

        return $this->success(message: 'Pictures saved successfully');
    }
}
