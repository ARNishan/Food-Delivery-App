<?php

namespace Tests\Feature;

use App\Http\Controllers\RiderLocationController;
use App\Services\RiderLocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\RiderLocationRequest;
use App\Http\Requests\NearestRiderRequest;
use App\Http\Resources\RiderLocationResource;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Tests\TestCase;
use App\Models\Rider;

class RiderLocationControllerTest extends TestCase
{
    // use RefreshDatabase;

    /** @test */
    public function test_store_rider_location()
    {

        $riderLocationServiceMock = $this->createMock(RiderLocationService::class);
        $riderLocationServiceMock->expects($this->once())
            ->method('storeRiderLocation')
            ->willReturn(true);

        $rider = new Rider;
        $rider->name = 'Mr. A';
        $rider->email = 'a@email.com';
        $rider->contact_no = '0132798789';
        $rider->save();

        // Prepare request data
        $requestData = [
            'rider_id' => $rider->id,
            'service_name' => 'Delivery',
            'latitude' => 77.98490600,
            'longitude' => 789.212,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ];

        // Create a new instance of RiderLocationRequest and set the request data
        $request = RiderLocationRequest::create('/api/rider-location', 'POST', $requestData);

        // Validate the request
        $validator = $this->app['validator']->make($requestData, $request->rules());
        $request->setValidator($validator);

        $controller = new RiderLocationController($riderLocationServiceMock);
        $response = $controller->store($request);

        $this->assertInstanceOf(RiderLocationResource::class, $response);
        $responseData = $response->toArray($request);
        $this->assertEquals('Rider location stored successfully',  $responseData['message']);
    }

    /** @test */


    public function test_finds_nearest_rider()
    {
        $riderLocationServiceMock = $this->createMock(RiderLocationService::class);
        $riderLocationServiceMock->expects($this->once())
            ->method('findNearestRider')
            ->willReturn(true);

        $restaurant = new Restaurant;
        $restaurant->name = 'Test';
        $restaurant->latitude = 77.98490600;
        $restaurant->longitude = 789.112;
        $restaurant->save();
        $requestData = [
            'restaurant_id' => $restaurant->id,
        ];
        $request = NearestRiderRequest::create('api/nearest-rider', 'GET', $requestData);
         // Validate the request
        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('Accept', 'application/json');
        $validator = $this->app['validator']->make($requestData, $request->rules());
        $request->setValidator($validator);

        $controller = new RiderLocationController($riderLocationServiceMock);
        $response = $controller->findNearestRider($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
