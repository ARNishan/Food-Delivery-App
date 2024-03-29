<?php
namespace App\DTO;
use App\Http\Requests\RiderLocationRequest;

class RiderLocationDTO
{
    public $rider_id;
    public $service_name;
    public $lat;
    public $long;

    public function __construct($rider_id, $service_name,$lat, $long)
    {
        $this->rider_id = $rider_id;
        $this->service_name = $service_name;
        $this->lat = $lat;
        $this->long = $long;
    }

    public static function fromApiRequest(RiderLocationRequest $request) : RiderLocationDTO
    {
        return new self(
            $request->validated('rider_id'),
            $request->validated('service_name'),
            $request->validated('latitude'),
            $request->validated('longitude')
        );
    }
}
