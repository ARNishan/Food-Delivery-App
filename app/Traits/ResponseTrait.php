<?php

namespace App\Traits;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

trait ResponseTrait
{
    public function apiResponse($status_code,$message,$data){
        $response['status_code'] = $status_code;
        $response['message'] = $message;
        $response['data'] = $data;
        return response()->json($response,$status_code);
    }
}
