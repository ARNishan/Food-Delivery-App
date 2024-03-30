<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\RiderLocationRequest;
use App\Traits\ResponseTrait;
use App\Repositories\RiderLocationRepository;
use App\Repositories\RestaurantRepository;
class RiderService
{
    use ResponseTrait;
    protected $restaurantRepository, $riderLocationRepository;
    public function __construct(RestaurantRepository $restaurantRepository,RiderLocationRepository $riderLocationRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
        $this->riderLocationRepository = $riderLocationRepository;

    }

    public function storeRiderLocation(RiderLocationRequest $request)
    {
        try{
            $data = $request->toArray();
            $riderLocation = $this->riderLocationRepository->store($data);
            return $this->apiResponse(200, 'Rider location stored successfully', $riderLocation);
        }catch(\Exception $e){
            return $this->apiResponse(500, 'Somthing went wrong!', array());
        }

    }

    public function findNearestRider($restaurantId)
    {
        try{
            $restaurant = $this->restaurantRepository->get($restaurantId);
            $restaurantLatitude = $restaurant->latitude;
            $restaurantLongitude = $restaurant->longitude;

            // Retrieve the cached result if available
            $cacheKey = "nearest_rider_$restaurantId";

            $cachedResult = Cache::get($cacheKey);

            if ($cachedResult) {
                return $this->apiResponse(200, 'Rider found!', json_decode($cachedResult));
            }
            $nearestRider = $this->riderLocationRepository->nearestRider($restaurantLatitude,$restaurantLongitude);

            if (!$nearestRider) {
                return $this->apiResponse(200, 'No riders found nearby', []);
            }

            // Calculate distance
            $distance = calculateDistance($nearestRider->latitude, $nearestRider->longitude, $restaurantLatitude, $restaurantLongitude);

            // Cache the result for future requests
            Cache::put($cacheKey, json_encode([
                'rider_name' => $nearestRider->rider->name,
                'distance' => $distance
            ]), now()->addMinutes(5));

            return $this->apiResponse(200, 'Rider found!', [
                'rider_name' => $nearestRider->rider->name,
                'distance' => $distance
            ]);

        }catch(\Exception $e){
            return $this->apiResponse(500, 'Somthing went wrong!', array());
        }

    }

}
