<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AddPOITest extends TestCase
{
    static $poies = [
        0 => [
            'latitude'  => 29.615439,
            'longitude' => 52.482551,
            'radius'    => 0.10467, // kilometer
        ],
        1 => [
            'latitude'  => 29.616097,
            'longitude' => 52.482041,
            'radius'    => 0.10467, // kilometer
        ],
        2 => [
            'latitude'  => 29.616069,
            'longitude' => 52.480888,
            'radius'    => 0.10467, // kilometer
        ],
        3 => [
            'latitude'  => 29.615938,
            'longitude' => 52.480357,
            'radius'    => 0.10467, // kilometer
        ],
        4 => [
            'latitude'  => 29.616875,
            'longitude' => 52.482036,
            'radius'    => 0.11921, // kilometer
        ],
        5 => [
            'latitude'  => 29.616885,
            'longitude' => 29.616885,
            'radius'    => 0.11921, // kilometer
        ],
    ];
    static $spots = [
        [
            'latitude'    => 29.616150,
            'longitude'   => 52.482006,
            'shouldBeIn'  => [
                0, 1, 4 // poies
            ],
            'shouldBeOut' => [
                2, 3,
            ],
        ],
        [
            'latitude'    => 29.617008,
            'longitude'   => 52.480884,
            'shouldBeIn'  => [
                2,4,
            ],
            'shouldBeOut' => [
                5,
            ],
        ],
    ];

    public function __construct($name = NULL, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        global $storage_prefix;
        $storage_prefix = 'test/';                     // changes the storage path for testing data
    }

    protected function setUp(): void
    {
        parent::setUp();
        // remove last storage
        $files = glob(__DIR__ . '/../storage/test/*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }
    }

    public function testAddingPOI()
    {
        $poi = \App\Poi::make();
        $test_Data = [
            'id'        => 1,
            'name'      => 'test1',
            'latitude'  => 29.616008,
            'longitude' => 52.482084,
            'radius'    => 0.10467,
        ];
        $args = array_values($test_Data);
        $poi->add(...$args);

        $poi = \App\Poi::make();
        $this->assertTrue((boolean)$poi->exists($test_Data['id']));
        $this->assertEquals($poi->get(), [$test_Data]);
    }

    public function testUpdatingSpots()
    {
        $spot = \App\Spot::make();
        $test_Data = [
            'latitude'  => 29.616008,
            'longitude' => 52.482084,
        ];
        $args = array_values($test_Data);
        $spot->add(...$args);
        $spot->update(0, ['test' => true]);

        $this->assertEquals(
            array_merge($test_Data, [
                'near' => [],
                'test' => true,
            ]),
            $spot->get()[0]
        );
    }

    function testAddingListOfPOIS()
    {

        $poi = \App\Poi::make();
        foreach (static::$poies as $i => $data) {
            $data = array_merge(
                [
                    'id'   => $i,
                    'name' => 'test-area-' . $i,
                ],
                $data
            );
            $args = array_values($data);
            $poi->add(...$args);
        }

        $spot = \App\Spot::make();
        foreach (static::$spots as $i => $data) {
            $spot->add($data['latitude'], $data['longitude']);

            // near field should  consists of expected areas
            foreach ($spot->get()[$i]['near'] as $poi) {
                $this->assertContains($poi['id'], $data['shouldBeIn']);
            }
        }
    }

    function testAddingListOfSpots()
    {
        // first we create spots
        $spot = \App\Spot::make();
        foreach (static::$spots as $i => $data) {
            $spot->add($data['latitude'], $data['longitude']);
        }
        // then we deliberately add POI afterwards. (because the new POI should be checked for each spots. by this test we prove this process)
        $poi = \App\Poi::make();
        foreach (static::$poies as $i => $data) {
            $data = array_merge(
                [
                    'id'   => $i,
                    'name' => 'test-area-' . $i,
                ],
                $data
            );
            $args = array_values($data);
            $poi->add(...$args);
        }

        // when new POI is added then it should be checked in all saved spots
        $spots = \App\Spot::make();
        foreach ($spots->get() as  $data) {
            // "near" field should  consist of expected areas
            $this->assertGreaterThan(0,count($data['near']));

            foreach ($data['near'] as $poi)
                foreach (static::$spots as $i => $testData) {
                    if($testData['latitude']==$poi['latitude'] && $testData['longitude']==$poi['longitude']  )
                        $this->assertContains($poi['id'], $data['shouldBeIn']);
                }
        }
    }
}
