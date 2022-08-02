<?php

namespace App\Http\Controllers;

use App\Models\Restaurants;
use Illuminate\Http\Request;

class RestaurantsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $restaurants = Restaurants::all();

        if ($restaurants)
            return response()->json([
                'success' => true,
                'message' => $restaurants
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko gauti restoranų sąrašo'
            ], 500);
    }

    public function show($id, Request $request)
    {
        $restaurant = Restaurants::where('id', $id);

        if ($restaurant->get())
            return response()->json([
                'success' => true,
                'message' => $restaurant->get()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Restoranao su tokiu id nerasta'
            ], 500);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Authentification
        if (auth()->user()->role != 0)
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);

        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'address' => 'required'
        ]);


        $restaurant = new Restaurants();
        $restaurant->name = $request->name;
        $restaurant->code = $request->code;
        $restaurant->address = $request->address;

        if ($restaurant->save())
            return response()->json([
                'success' => true,
                'message' => $restaurant->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko išsaugoti restorano'
            ], 500);
    }


    public function update($id, Request $request)
    {
        //Authentification
        if (auth()->user()->role != 0)
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);

        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'address' => 'required'
        ]);


        $restaurant = Restaurants::where('id', $id);

        if ($restaurant->update($request->all()))
            return response()->json([
                'success' => true,
                'message' => 'Restoranas sėkmingai atnaujintas'
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko išsaugoti restorano'
            ], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Restaurants  $restaurants
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        //Authentification
        if (auth()->user()->role != 0)
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);

        try {
            $restaurant = Restaurants::where('id', $id);

            $restaurant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Restoranas sėkmingai ištrintas'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Restoranas negali būti ištrintas, nes yra priskirtas prie menu'
            ], 500);
        }
    }
}