<?php
namespace App\DTO;
use App\Http\Requests\RiderLocationRequest;
use Illuminate\Support\Facades\Log;

class RiderLocationDTO
{
    public $rider_id;
    public $service_name;
    public $latitude;
    public $longitude;
    public $timestamp;

    public function __construct($rider_id, $service_name,$latitude, $longitude,$timestamp)
    {
        $this->rider_id = $rider_id;
        $this->service_name = $service_name;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->timestamp = $timestamp;
    }

    public static function fromApiRequest(RiderLocationRequest $request) : RiderLocationDTO
    {
        return new self(
            $request->validated('rider_id'),
            $request->validated('service_name'),
            $request->validated('latitude'),
            $request->validated('longitude'),
            $request->validated('timestamp')
        );
    }
}
