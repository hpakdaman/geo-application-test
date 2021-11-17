<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class InsideCircleTest extends TestCase
{
    static $spots = [
        [
            'latitude'    => 29.616150,
            'longitude'   => 52.482006,
            'shouldBeIn'  => [
                [
                    'latitude'  => 29.615439,
                    'longitude' => 52.482551,
                    'radius'    => 0.10467, // kilometer
                ],
                [
                    'latitude'  => 29.616097,
                    'longitude' => 52.482041,
                    'radius'    => 0.10467, // kilometer
                ],
                [
                    'latitude'  => 29.616875,
                    'longitude' => 52.482036,
                    'radius'    => 0.11921, // kilometer
                ],
            ],
            'shouldBeOut' => [
                [
                    'latitude'  => 29.616069,
                    'longitude' => 52.480888,
                    'radius'    => 0.10467, // kilometer
                ], [
                    'latitude'  => 29.615938,
                    'longitude' => 52.480357,
                    'radius'    => 0.10467, // kilometer
                ],
            ],
        ],
        [
            'latitude'    => 29.617008,
            'longitude'   => 52.480884,
            'shouldBeIn'  => [
                [
                    'latitude'  => 29.616069,
                    'longitude' => 52.480888,
                    'radius'    => 0.10467, // kilometer
                ],
                [
                    'latitude'  => 29.616875,
                    'longitude' => 52.482036,
                    'radius'    => 0.11921, // kilometer
                ],
            ],
            'shouldBeOut' => [
                [
                    'latitude'  => 29.616885,
                    'longitude' => 29.616885,
                    'radius'    => 0.11921, // kilometer
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
