<?php

namespace App\Repositories;


use App\Models\RiderLocation;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RiderLocationRepository
{
    protected Model $model;

    protected string $cacheKey = "nearest_rider_";


    public function __construct(RiderLocation $model)
    {
        $this->model = $model;
    }


    /**
     * @param array $data
     *
     * @return Model
     */
    public function store(array $data) : Model
    {
        try {

            $riderLocation = [];
            $riderLocation['rider_id'] = isset($data['rider_id']) ? $data['rider_id'] : null;
            $riderLocation['service_name'] = isset($data['service_name']) ? $data['service_name'] : null;
            $riderLocation['latitude'] =isset($data['latitude']) ? $data['latitude'] : null;
            $riderLocation['longitude'] = isset($data['longitude']) ? $data['longitude'] : null;
            $riderLocation['capture_time'] = isset($data['timestamp']) ? $data['timestamp'] : null;
            return $this->model->create($riderLocation);

        } catch (\Throwable $th) {
            throw new Exception("Database connectivity problem or query problem", 500);
        }
    }

    /**
     * @param int $restaurantId
     * @param float $restaurantLatitude
     * @param float $restaurantLongitude
     *
     * @return array
     */
    public function nearestRider(int $restaurantId, float $restaurantLatitude, float $restaurantLongitude) : array
    {
        try {
            $cacheKey = $this->cacheKey . $restaurantId;
            if(Cache::has($cacheKey)) {
                return json_decode(Cache::get($cacheKey), true);
            }
            $nearestRider = $this->model::where('capture_time', '>=', now()->subMinutes(5))
                ->orderByRaw('ABS(latitude - ' . $restaurantLatitude . ') + ABS(longitude - ' . $restaurantLongitude . ')')
                ->first();
            if($nearestRider) {
                $distance = calculateDistance($nearestRider->latitude, $nearestRider->longitude, $restaurantLatitude, $restaurantLongitude);
                $riderName = $nearestRider->rider->name;
                $this->storeRiderInfoInCache($cacheKey, $riderName, $distance);
                return [
                    'rider_name' => $riderName,
                    'distance' => $distance
                ];
            } else {
                return [];
            }
        } catch (\Throwable $th) {
            throw new Exception("Database connectivity problem or query problem", 500);
        }

    }

    /**
     * @param string $cacheKey
     * @param string $riderName
     * @param mixed $distance
     *
     * @return void
     */
    private function storeRiderInfoInCache(string $cacheKey, string $riderName, $distance) : void
    {
        // Cache the result for future requests
        Cache::put($cacheKey, json_encode(
            [
                'rider_name' => $riderName,
                'distance' => $distance
            ]
        ), now()->addMinutes(5));
    }
}
