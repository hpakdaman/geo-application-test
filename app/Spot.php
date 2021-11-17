<?php namespace App;

use Illuminate\Support\Collection;

class Spot
{
    const PI = 3.14;

    /**
     * @var Collection
     */
    protected $data;

    /**
     * Makes an instance from this class (like Factory pattern but simple)
     * @return static
     */
    static function make()
    {
        $instance = new static();
        $instance->data = loadStorage('spots');
        return $instance;
    }


    /**
     * returns all data
     * @return array
     */
    public function get()
    {
        return $this->data->toArray();
    }

    /**
     * returns True if the checkpoint is inside given circle ($centerPoint)
     * @param $spot
     * @param $POI
     * @return bool
     */
    static function isInside($spot, $POI)
    {
        $ky = 40000 / 360;
        $kx = cos(self::PI * $POI['latitude'] / 180.0) * $ky;
        $dx = abs($POI['longitude'] - $spot['longitude']) * $kx;
        $dy = abs($POI['latitude'] - $spot['latitude']) * $ky;
        return sqrt($dx * $dx + $dy * $dy) <= $POI['radius'];
    }


    /**
     * adds a new spot and find its nearest coordinates
     * @param $latitude
     * @param $longitude
     * @return array
     */
    public function add($latitude, $longitude)
    {
        $this->data[] = $data = [
            'latitude'  => $latitude = toFloat($latitude),
            'longitude' => $longitude = toFloat($longitude),
            'near'      => Poi::make()->neighboringPOIs(compact('latitude', 'longitude')),
        ];
        $this->save();
        return $data;
    }

    /**
     * update an spot
     * @param $index
     * @param $changes
     * @return bool
     */
    public function update($index, $changes)
    {
        if (isset($this->data[$index])) {
            $this->data[$index] = $data = array_merge($this->data[$index], $changes);
            $this->save();
            return $data;
        }
        return false;
    }


    /**
     * @return false|int
     */
    private function save()
    {
        return saveStorage('spots', $this->data);
    }
}
