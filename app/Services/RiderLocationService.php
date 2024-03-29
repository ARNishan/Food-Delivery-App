<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\DTO\RiderLocationDTO;
use App\Models\RiderLocation;
use App\Models\Restaurant;

class RiderLocationService
{
    public function __construct()
    {

    }

    public function storeRiderLocation(RiderLocationDTO $riderLocationDTO)
    {
        $riderLocation = new RiderLocation();
        $riderLocation->rider_id = $riderLocationDTO->rider_id;
        $riderLocation->service_name = $riderLocationDTO->service_name;
        $riderLocation->latitude = $riderLocationDTO->latitude;
        $riderLocation->longitude = $riderLocationDTO->longitude;
        $riderLocation->capture_time = $riderLocationDTO->timestamp;
        $riderLocation->save();

        return $riderLocation;
    }

    public function findNearestRider($restaurantId)
    {
        $restaurant = Restaurant::find($restaurantId);
        $restaurantLatitude = $restaurant->latitude;
        $restaurantLongitude = $restaurant->longitude;

        // Retrieve the cached result if available
        $cacheKey = "nearest_rider_$restaurantId";

        $cachedResult = Cache::get($cacheKey);

        if ($cachedResult) {
            return [
                'message' => 'Rider found!',
                'data' => json_decode($cachedResult)
            ];
        }
        // Find the nearest rider within the last 5 minutes
        $nearestRider = RiderLocation::where('capture_time', '>=', now()->subMinutes(5))
            ->orderByRaw('ABS(latitude - ' . $restaurantLatitude . ') + ABS(longitude - ' . $restaurantLongitude . ')')
            ->first();

        if (!$nearestRider) {
            return [
                'message' => 'No riders found nearby',
                'data' => []
            ];
        }

        // Calculate distance
        $distance = calculateDistance($nearestRider->latitude, $nearestRider->longitude, $restaurantLatitude, $restaurantLongitude);

        // Cache the result for future requests
        Cache::put($cacheKey, json_encode([
            'rider_name' => $nearestRider->rider->name,
            'distance' => $distance
        ]), now()->addMinutes(5));

        return [
            'message' => 'Rider found!',
            'data' => [
                'rider_name' => $nearestRider->rider->name,
                'distance' => $distance
            ]
        ];
    }

}
