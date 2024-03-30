<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RiderLocationRequest;
use App\Http\Requests\NearestRiderRequest;
use App\Http\Resources\RiderLocationResource;
use App\Services\RiderService;
use App\DTO\RiderLocationDTO;
use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;


class RiderLocationController extends Controller
{
    use ResponseTrait;
    protected $riderService;

    public function __construct(RiderService $RiderService)
    {
        $this->riderService = $RiderService;
    }

    public function store(RiderLocationRequest $request)
    {
        try{
            $request->validated();
            return $this->riderService->storeRiderLocation($request);
        }catch(\Exception $e){
            return $this->apiResponse(500, 'Something went wrong!', array());
        }

    }

    public function findNearestRider(NearestRiderRequest $request)
    {
        try{
            return $this->riderService->findNearestRider($request->validated('restaurant_id'));
        }catch(\Exception $e){
            return $this->apiResponse(500, 'Something went wrong!', array());
        }
    }
}
