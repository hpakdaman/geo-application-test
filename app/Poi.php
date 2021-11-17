<?php namespace App;

use Illuminate\Support\Collection;

class Poi
{

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
        $instance->data = loadStorage('poies');
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
     * adds a new spot and find its nearest POIs
     * @param $ID
     * @param $name
     * @param $latitude
     * @param $longitude
     * @param $radius
     * @return array
     * @throws \Exception
     */
    public function add($ID, $name, $latitude, $longitude, $radius)
    {
        if ($this->exists($ID))
            throw new \Exception('The given POI exists! use update method to change.');

        $this->data[] = $poi = [
            'id'        => intval($ID),
            'name'      => trim($name),
            'latitude'  => toFloat($latitude),
            'longitude' => toFloat($longitude),
            'radius'    => toFloat($radius),
        ];
        $this->save();

        // when we add a new POI, we should check each spot whether it is near that POI
        self::updateSpotsForPoi($poi);
        return $poi;
    }

    /**
     * adds a new spot and find its nearest POIs
     * @param $ID
     * @param $changes
     * @return false|int
     * @throws \Exception
     */
    public function update($ID, $changes)
    {
        if (!$poi = $this->exists($ID))
            throw new \Exception('The given POI doesn\'t exists!');

        $this->data = $this->data->map(function ($poi) use ($ID, $changes) {
            if ($poi['id'] == $ID)
                return [
                    'id'        => $poi['id'],
                    'name'      => trim(isset($changes['name']) ? $changes['name'] : $poi['name']),
                    'latitude'  => toFloat(isset($changes['latitude']) ? $changes['latitude'] : $poi['latitude']),
                    'longitude' => toFloat(isset($changes['longitude']) ? $changes['longitude'] : $poi['longitude']),
                    'radius'    => toFloat(isset($changes['radius']) ? $changes['radius'] : $poi['radius']),
                ];
            return $poi;
        });
        $this->save();

        // when we add a new POI, we should check each spot whether it is near that POI
        self::updateSpotsForPoi($poi);
        return $this->data[$ID];
    }

    /**
     * adds a new spot and find its nearest POIs
     * @param $ID
     * @param $changes
     * @return false|int
     * @throws \Exception
     */
    public function delete($ID)
    {
        if (!$this->exists($ID))
            throw new \Exception('The given POI doesn\'t exists!');

        // removes
        $this->data = $this->data->filter(function ($poi) use($ID) {
            return $poi['id'] != $ID;
        });
        $saved = $this->save();


        $factory = Spot::make();
        // deleted POI should be removed from the spots also
        foreach ($factory->get() as $i => $spot) {
            // get all areas except that removed POI
            $area=array_filter($spot['near'],function ($poi) use($ID) {
                return $poi['id'] != $ID;
            });
            $factory->update($i,['near'=>$area]);
        }
        return $saved;
    }

    /**
     * updates all spots according to the given POI
     * @param $poi
     * @return void
     * @throws \Exception
     */
    private static function updateSpotsForPoi($poi)
    {
        $factory = Spot::make();
        foreach ($factory->get() as $i => $spot) {
            // check spots if they are near POI
            if (Spot::isInside($spot, $poi)) {
                // first , finds the existing POI in "near" field then remove it
                $near = array_filter($spot['near'], function ($np) use ($poi) {
                    return $np['id'] != $poi['id'];
                });
                // then adds the updated one
                $factory->update($i, ['near' => array_merge($near, [$poi])]);
            }
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function exists($id)
    {
        return $this->data->where('id', intval($id))->first();
    }

    /**
     * Find neighboring POIs from the given coordinates
     * @param $spot
     * @return array
     */
    function neighboringPOIs($spot)
    {
        $near = [];
        foreach ($this->data as $poi) {
            // check POIes if they are near that area
            if (Spot::isInside($spot, $poi)) {
                $near[] = $poi;
            }
        }

        return $near;
    }

    /**
     * @return false|int
     */
    private function save()
    {
        return saveStorage('poies', $this->data);
    }
}
