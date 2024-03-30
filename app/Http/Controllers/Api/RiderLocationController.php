<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RiderLocationRequest;
use App\Http\Requests\NearestRiderRequest;
use App\Http\Resources\RiderLocationResource;
use App\Services\RiderLocationService;
use App\DTO\RiderLocationDTO;
use App\Http\Controllers\Controller;


class RiderLocationController extends Controller
{
    protected $riderLocationService;

    public function __construct(RiderLocationService $riderLocationService)
    {
        $this->riderLocationService = $riderLocationService;
    }

    public function store(RiderLocationRequest $request)
    {
        $riderLocation = $this->riderLocationService->storeRiderLocation(RiderLocationDTO::fromApiRequest($request));
        return new RiderLocationResource($riderLocation);
    }

    public function findNearestRider(NearestRiderRequest $request)
    {
        $nearestRider = $this->riderLocationService->findNearestRider($request->validated('restaurant_id'));
        return response()->json($nearestRider);
    }
}
