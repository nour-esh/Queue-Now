<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Models\Service;

class ServiceController extends Controller
{
    //GET /services
    public function index()
    {
        $services = Service::with([
            'employees',
            'tickets' => function ($query) {
                $query->whereDate('queue_date', now()->toDateString());
            },
        ])->get();

        return response()->json([
            'status'  => 200,
            'message' => 'Services retrieved successfully.',
            'data'    => $services,
        ], 200);
    }

    //POST /services
    public function store(StoreServiceRequest $request)
    {
        $service = Service::create($request->validated());
        return response()->json([
            'message' => 'Service created successfully.',
            'data'    => $service,
        ], 201);
    }

    // GET /services/{id}
      // GET /services/{id}
    public function show($id)
    {
        $service = Service::with([
            'employees',
            'tickets' => function ($query) {
                $query->whereDate('queue_date', now()->toDateString());
            },
        ])->find($id);

        //404
        if (! $service) {
            return response()->json([
                'status'  => 404,
                'message' => 'Service not found.',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Service retrieved successfully.',
            'data'    => $service,
        ], 200);
    }

    //PATCH /services/{id}
    public function update(UpdateServiceRequest $request, $id)
    {
        $service = Service::find($id);
        if (! $service) {
            return response()->json([
                'status'  => 404,
                'message' => 'Service not found.',
                'data'    => null,
            ], 404);
        }
        $service->update($request->validated());
        return response()->json([
            'status'  => 200,
            'message' => 'Service updated successfully.',
            'data'    => $service,
        ], 200);
    }
}
