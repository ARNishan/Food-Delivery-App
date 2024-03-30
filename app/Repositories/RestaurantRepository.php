<?php

namespace App\Repositories;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Cache;

class RestaurantRepository
{
    public function get($id)
    {
        return Restaurant::findOrFail($id);
    }


}
