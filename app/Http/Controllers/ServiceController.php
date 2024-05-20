<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $servicePerPage = request()->input('per_page', 10);
        $services = Service::with('pictures', 'user')
            ->when(request()->filled('type'), function ($query) {
                $query->whereIn('type', explode(',', request('type')));
            })
            ->when(request()->filled('search'), function ($query) {
                $query->where('name', 'like', '%'.request('search').'%')
                    ->orWhere('description', 'like', '%'.request('search').'%');
            })
            ->when(request()->filled('sort_price'), function ($query) {
                $query->orderBy('price_per_hour', request('sort_price'));
            })
            // make sure the latest services are shown first, and the don't want to show the services that are unavailable
            ->where('availability', 'available')
            ->latest()
            ->paginate($servicePerPage);

        return $this->success(data: $services, message: 'Services retrieved successfully');
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
    public function store(ServiceRequest $request)
    {
        $service = Service::create($request->validated());

        $this->saveServicePictures($request, $service->service_id);

        return response()->json(['message' => 'Service created successfully'], 201);
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
    public function update(ServiceRequest $request, string $id)
    {
        $service = Service::findOrFail($id);
        $service->update($request->validated());

        $service->pictures()->delete();

        $pictures = $request->pictures;
        collect($pictures)->each(function ($picture) use ($service) {
            $pictureFormat = explode('/', mime_content_type($picture['picture_path']))[1];
            $decodedPicture = base64_decode(str_replace('data:image/'.$pictureFormat.';base64,', '', $picture['picture_path']));
            $fileName = uniqid().'.'.$pictureFormat;
            Storage::disk('public')->put('images/services/'.$fileName, $decodedPicture);
            $service->pictures()->create([
                'picture_path' => 'images/services/'.$fileName,
                'service_id' => $service->service_id,
            ]);
        });

        return $this->success(message: 'Service updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return $this->success(message: 'Service deleted successfully');
    }

    public function getAuthServices()
    {
        $servicePerPage = request()->input('per_page', 10);
        $services = Service::with('pictures')
            ->when(request()->filled('search'), function ($query) {
                $query->where('name', 'like', '%'.request('search').'%');

            })
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate($servicePerPage);

        return $this->success(data: $services, message: 'Services retrieved successfully');
    }

    public function saveServicePictures(Request $request, string $id)
    {
        // Find the service by id
        $service = Service::findOrFail($id);
        // Get the images from the request
        $pictures = $request->pictures;

        // Use map to save each image
        collect($pictures)->each(function ($image) use ($service) {
            // Get the image format (png, jpg, jpeg)
            $imageFormat = explode('/', mime_content_type($image['url']))[1];
            // Decode the base64-encoded image URL
            $decodedImage = base64_decode(str_replace('data:image/'.$imageFormat.';base64,', '', $image['url']));

            // Generate a unique file name with the correct extension
            $fileName = uniqid().'.'.$imageFormat;

            // Store the image in the storage/app/public/images/services directory
            Storage::disk('public')->put('images/services/'.$fileName, $decodedImage);

            // Create a new service picture record in the database
            $service->pictures()->create([
                'picture_path' => 'images/services/'.$fileName,
                'service_id' => $service->service_id,
            ]);
        });

        return $this->success(message: 'Pictures saved successfully');
    }

    public function updateStatus(Request $request, string $id)
    {
        $service = Service::findOrFail($id);
        $service->update(['availability' => $request->availability]);

        return $this->success(message: 'Service status updated successfully');
    }
}
