<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\RiderLocationRequest;
use App\Traits\ResponseTrait;
use App\Repositories\RiderLocationRepository;
use App\Repositories\RestaurantRepository;
use Symfony\Component\HttpFoundation\Response;

class RiderService
{
    use ResponseTrait;
    protected $restaurantRepository, $riderLocationRepository;

    public function __construct(RestaurantRepository $restaurantRepository,RiderLocationRepository $riderLocationRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
        $this->riderLocationRepository = $riderLocationRepository;

    }

    /**
     * @param array $request
     *
     * @return array
     */
    public function storeRiderLocation(array $request) : array
    {
        try{
            $riderLocation = $this->riderLocationRepository->store($request);
            if($riderLocation) {
                return [
                    Response::HTTP_OK,
                    "Rider location stored successfully",
                    $riderLocation
                ];
            } else {
                return [
                    Response::HTTP_OK,
                    "Data store failed",
                    []
                ];
            }
        } catch(\Exception $e){
            return [
                $e->getCode(),
                "Something went wrong",
                []
            ];
        }

    }

    /**
     * @param int $restaurantId
     *
     * @return array
     */
    public function findNearestRider(int $restaurantId) : array
    {
        try{
            $restaurant = $this->restaurantRepository->get($restaurantId);
            $restaurantLatitude = $restaurant->latitude;
            $restaurantLongitude = $restaurant->longitude;

            $nearestRiderInfo = $this->riderLocationRepository->nearestRider($restaurantId, $restaurantLatitude,$restaurantLongitude);

            if(!empty($nearestRiderInfo)) {
                return [
                    Response::HTTP_OK,
                    "Rider found!",
                    $nearestRiderInfo
                ];
            } else {
                return [
                    Response::HTTP_OK,
                    "No riders found nearby",
                    []
                ];
            }

        }catch(\Exception $e){
            return [
                $e->getCode(),
                "Something went wrong",
                []
            ];
        }

    }

}
