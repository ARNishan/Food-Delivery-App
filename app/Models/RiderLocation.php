<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderLocation extends Model
{
    use HasFactory;
    protected $fillable = ['rider_id', 'service_name', 'latitude', 'longitude', 'capture_time'];

    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }

    public function store($request){
        $this->rider_id = $request->rider_id;
        $this->service_name = $request->service_name;
        $this->latitude = $request->latitude;
        $this->longitude = $request->longitude;
        $this->capture_time = $request->timestamp;
        $this->save();

    }

}
