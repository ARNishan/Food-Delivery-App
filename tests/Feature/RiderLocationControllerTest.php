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
use Illuminate\Http\Request;
use Tests\TestCase;
use App\Models\Rider;

class RiderLocationControllerTest extends TestCase
{
    use RefreshDatabase;

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
            'latitude' => 123.456,
            'longitude' => 789.012,
            'timestamp' => '2022-01-01 12:00:00'
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
    public function it_finds_nearest_rider()
    {
        $riderLocationServiceMock = $this->createMock(RiderLocationService::class);
        $riderLocationServiceMock->expects($this->once())
        ->method('storeRiderLocation')
        ->willReturn((object) ['id' => 1, 'rider_id' => 123,  'service_name' => 'test service','latitude' => 40.7128, 'longitude' => -74.0060]);

        $controller = new RiderLocationController($riderLocationServiceMock);
        $request = NearestRiderRequest::create('api/nearest-rider', 'GET', ['restaurant_id' => 123]);
        $response = $controller->findNearestRider($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode(['id' => 1, 'rider_id' => 123, 'service_name' => 'test service','latitude' => 40.7128, 'longitude' => -74.0060]), $response->getContent());
    }
}
