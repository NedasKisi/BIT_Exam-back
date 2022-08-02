<?php

namespace App\Http\Controllers;

use App\Models\Menus;
use App\Models\Restaurants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menus = Menus::all();

        foreach ($menus as $menu) {
            if ($menu->restaurant_id) {
                $restaurant = Restaurants::find($menu->restaurant_id);
                $menu->restaurant = $restaurant->name;
            } else {
                $menu->restaurant = 'Nepasirinkta';
            }
        }

        if ($menus)
            return response()->json([
                'success' => true,
                'message' => $menus
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko gauti meniu sąrašo'
            ], 500);
    }
    public function show($id, Request $request)
    {

        $menu = Menus::where('id', $id);

        if ($menu->get())
            return response()->json([
                'success' => true,
                'message' => $menu->get()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko rasti meniu'
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
            'name' => 'required'
        ]);
        $menu = new Menus();
        $menu->name = $request->name;
        if ($request->restaurant_id) {
            $menu->restaurant_id = $request->restaurant_id;
        } else {
            $menu->restaurant_id = null;
        }
        if ($menu->save())
            return response()->json([
                'success' => true,
                'message' => $menu->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko išsaugoti meniu'
            ], 500);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Menus  $menus
     * @return \Illuminate\Http\Response
     */
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
        ]);

        $menu = Menus::find($id);
        $data = [];

        $data['name'] = $request->name;
        if ($request->restaurant_id) {
            $data['restaurant_id'] = $request->restaurant_id;
        } else {
            $data['restaurant_id'] = null;
        }

        if ($request->file('photo')) {
            $uploadedFile = $request->file('photo');
            $filename = time() . $uploadedFile->getClientOriginalName();
            $filepath = str_replace(' ', '_', $filename);
            $storage = Storage::disk('local')->putFileAs(
                'public',
                $uploadedFile,
                $filepath
            );

            if (!$storage)
                return response()->json([
                    'success' => false,
                    'message' => 'Nepavyko išsaugoti nuotraukos'
                ], 500);

            $data['photo'] = '/storage/' . $filepath;
        }
        if ($menu->update($data))
            return response()->json([
                'success' => true,
                'message' => $menu->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko atnaujinti'
            ], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Menus  $menus
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Authentification
        if (auth()->user()->role != 0)
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);

        $menu = Menus::where('id', $id);

        if ($menu->delete())
            return response()->json([
                'success' => true,
                'message' => 'Menu sėkmingai ištrintas'
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko ištrinti menu'
            ], 500);
    }

    public function byRestaurant($id)
    {
        $menus = Menus::where('restaurant_id', $id);

        if ($menus->get())
            return response()->json([
                'success' => true,
                'message' => $menus->get()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko gauti menu sąrašo'
            ], 500);
    }

    public function sortByName()
    {
        $menus = Menus::orderBy('name');

        if ($menus->get())
            return response()->json([
                'success' => true,
                'message' => $menus->get()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko gauti menu sąrašo'
            ], 500);
    }
    public function search($keyword)
    {
        $menus = Menus::where('name', 'like', '%' . $keyword . '%');

        if ($menus->get())
            return response()->json([
                'success' => true,
                'message' => $menus->get()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko gauti menu sąrašo'
            ], 500);
    }
}