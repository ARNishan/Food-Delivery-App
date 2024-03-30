<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RiderLocationRequest;
use App\Http\Requests\NearestRiderRequest;
use App\Services\RiderService;
use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Response;

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
            [$code, $message, $data]  = $this->riderService->storeRiderLocation($request->all());
            if($code === Response::HTTP_OK){
                return $this->success($data, $message, $code);
            }
            return $this->error($message, null, $code, $data);

        }catch(\Exception $e){
            return $this->error('Something went wrong!', $e->getTrace(), 500);
        }

    }

    public function findNearestRider(NearestRiderRequest $request)
    {
        try{
            [$code, $message, $data] = $this->riderService->findNearestRider($request->restaurant_id);
            if($code === Response::HTTP_OK){
                return $this->success($data, $message, $code);
            }
            return $this->error($message, null, $code, $data);

        }catch(\Exception $e){
            return $this->error('Something went wrong!', $e->getTrace(), 500);
        }
    }
}
