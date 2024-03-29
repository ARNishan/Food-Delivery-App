<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\RiderLocation;
use Illuminate\Support\Facades\Redis;

class FindNearestRiderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $latitude,$longitude,$restaurantId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($latitude,$longitude,$restaurantId)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->restaurantId = $restaurantId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Retrieve arguments passed to the job
        $restaurantLatitude = $this->latitude;
        $restaurantLongitude = $this->longitude;
        $restaurantId = $this->restaurantId;

        $nearestRider = RiderLocation::where('capture_time', '>=', now()->subMinutes(5))
            ->orderByRaw('ABS(latitude - ' . $restaurantLatitude . ') + ABS(longitude - ' . $restaurantLongitude . ')')
            ->first();

        if ($nearestRider) {
            // Calculate distance (you may use a more precise formula for distance calculation)
            $distance = calculateDistance($nearestRider->latitude, $nearestRider->longitude, $restaurantLatitude, $restaurantLongitude);

            // Cache the result
            $cacheKey = "nearest_rider_$restaurantId";
            $cachedResult = json_encode(['rider_id' => $nearestRider->rider_id, 'distance' => $distance]);
            Redis::set($cacheKey, $cachedResult);
        } else {
            // Cache a message indicating no riders found nearby
            $cacheKey = "nearest_rider_$restaurantId";
            $cachedResult = json_encode(['message' => 'No riders found nearby']);
            Redis::set($cacheKey, $cachedResult);
        }
    }
}
