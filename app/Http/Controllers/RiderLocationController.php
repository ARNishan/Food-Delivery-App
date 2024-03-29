<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiderLocation;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Redis;


class RiderLocationController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'rider_id' => 'required|exists:riders,id',
            'service_name' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        RiderLocation::create($validatedData);

        return response()->json(['message' => 'Rider location stored successfully'], 201);
    }

    public function findNearestRider(Request $request)
    {
        $validatedData = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
        ]);

        // Retrieve the coordinates of the specified restaurant
        $restaurant = Restaurant::find($validatedData['restaurant_id']);
        $restaurantLatitude = $restaurant->latitude;
        $restaurantLongitude = $restaurant->longitude;

        // Find the nearest rider within the last 5 minutes
        $nearestRider = RiderLocation::where('capture_time', '>=', now()->subMinutes(5))
            ->orderByRaw('ABS(latitude - ' . $restaurantLatitude . ') + ABS(longitude - ' . $restaurantLongitude . ')')
            ->first();

        if (!$nearestRider) {
            return response()->json(['message' => 'No riders found nearby'], 404);
        }

        // Calculate distance (you may use a more precise formula for distance calculation)
        $distance = calculateDistance($nearestRider->latitude, $nearestRider->longitude, $restaurantLatitude, $restaurantLongitude);

        return response()->json([
            'rider_id' => $nearestRider->rider_id,
            'distance' => $distance
        ]);
    }


}
