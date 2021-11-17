<?php

namespace App\Http\Controllers;


use App\Poi;
use App\Spot;
use Illuminate\Http\Request;

class POIController extends Controller
{
    function store(Request $request)
    {
        $this->validate($request, [
            'id'        => 'required',
            'name'      => 'required',
            'latitude'  => 'required',
            'longitude' => 'required',
            'radius'    => 'required',
        ]);

        $args = array_values($request->only('id', 'name', 'latitude', 'longitude', 'radius'));
        $poi = Poi::make()->add(...$args);
        return ['done' => true, 'message' => 'saved.', 'poi' => $poi];
    }

    function index()
    {
        return ['poies' => Poi::make()->get()];
    }

    function show($id)
    {
        return ['poi' => Poi::make()->exists($id)];
    }

    function update($id)
    {
        $poi = Poi::make()->update($id, request()->only(['name', 'latitude', 'longitude', 'radius']));
        return ['done' => true, 'message' => 'updated.', 'poi' => $poi];
    }

    function destroy($id)
    {
        Poi::make()->delete($id);
        return ['done' => true, 'message' => 'deleted.'];
    }
}
