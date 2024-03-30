<?php

namespace App\Repositories;

use App\Models\RiderLocation;
use Illuminate\Support\Facades\Cache;

class RiderLocationRepository
{
    public function store($data)
    {
        $riderLocation = new RiderLocation();
        $riderLocation->rider_id = $data['rider_id'];
        $riderLocation->service_name = $data['service_name'];
        $riderLocation->latitude = $data['latitude'];
        $riderLocation->longitude = $data['longitude'];
        $riderLocation->capture_time = $data['timestamp'];
        $riderLocation->save();
        return $riderLocation;
    }

    public function nearestRider($restaurantLatitude,$restaurantLongitude)
    {
        return RiderLocation::where('capture_time', '>=', now()->subMinutes(5))
        ->orderByRaw('ABS(latitude - ' . $restaurantLatitude . ') + ABS(longitude - ' . $restaurantLongitude . ')')
        ->first();
    }
}
