<?php

namespace App\Http\Controllers;


use App\Spot;
use Illuminate\Http\Request;

class SpotController extends Controller
{

    /**
     * Add Spots to storage
     * to obtain coordinates refer: https://www.mapdevelopers.com/draw-circle-tool.php
     * @param Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    function store(Request $request)
    {
        $this->validate($request, [
            'latitude'  => 'required',
            'longitude' => 'required',
        ]);


        // adds new Item to collection
        $spot = Spot::make()->add($request->input('latitude'), $request->input('longitude'));
        return ['done' => true, 'message' => 'saved.', 'spot' => $spot];
    }

    function index()
    {
        return ['spots' => Spot::make()->get()];
    }
}
