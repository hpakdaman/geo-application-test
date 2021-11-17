<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class InsideCircleWichPizzaTowerGeoDataTest extends TestCase
{
    static $spots = [
        [
            'latitude'    => 43.723215,
            'longitude'   => 10.3977,
            'shouldBeIn'  => [
                [
                    "id"        => 4,
                    "name"      => "Near Spot",
                    "latitude"  => 43.723152,
                    "longitude" => 10.397896,
                    "radius"    => 0.020,
                ],
            ],
            'shouldBeOut' => [
                [
                    "id"        => 1,
                    "name"      => "Pizza Tower",
                    "latitude"  => 43.723014,
                    "longitude" => 10.396628,
                    "radius"    => 0.01795,
                ],
                [
                    "id"        => 2,
                    "name"      => "Hotel Pisa",
                    "latitude"  => 43.724615,
                    "longitude" => 10.397755,
                    "radius"    => 0.0433,
                ],
                [
                    "id"        => 3,
                    "name"      => "Hotel Villa Kinzica",
                    "latitude"  => 43.722091,
                    "longitude" => 10.397888,
                    "radius"    => 0.02274,

                ],
            ],
        ],

    ];

    static function getMessage($spot, $circle)
    {
        return "lat: {$spot['latitude']} , long: {$spot['longitude']} => {$circle['state']} inside {$circle['latitude']} , {$circle['longitude']} , radius: {$circle['radius']} ";
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSpots()
    {
        foreach (static::$spots as $spot) {
            foreach ($spot['shouldBeIn'] as $shouldIn) {
                $result = App\Spot::isInside($spot, $shouldIn);
                $shouldIn['state'] = 'SHOULD BE';
                $this->assertEquals($result, true, static::getMessage($spot, $shouldIn));
            }
            foreach ($spot['shouldBeOut'] as $shouldOut) {
                $result = App\Spot::isInside($spot, $shouldOut);
                $shouldIn['state'] = 'SHOULD NOT BE';
                $this->assertEquals($result, false, static::getMessage($spot, $shouldIn));
            }
        }
    }
}
