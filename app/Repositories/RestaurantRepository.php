<?php

namespace App\Repositories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;

class RestaurantRepository
{
    protected Model $model;

    public function __construct(Restaurant $model)
    {
        $this->model = $model;
    }

    public function get($id)
    {
        return $this->model::findOrFail($id);
    }


}
