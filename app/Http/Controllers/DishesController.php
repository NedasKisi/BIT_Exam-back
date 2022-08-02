<?php

namespace App\Http\Controllers;

use App\Models\Dishes;
use App\Models\Menus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DishesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dishes = Dishes::all();

        foreach ($dishes as $dish) {
            if ($dish->menu_id) {
                $menu = Menus::find($dish->menu_id);
                $dish->menu = $menu->name;
            } else {
                $dish->menu = 'Nepasirinkta';
            }
        }

        if ($dishes)
            return response()->json([
                'success' => true,
                'message' => $dishes
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko gauti patiekalų sąrašo'
            ], 500);
    }
    public function show($id, Request $request)
    {

        $dish = Dishes::where('id', $id);

        if ($dish->get())
            return response()->json([
                'success' => true,
                'message' => $dish->get()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko rasti patiekalo'
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
            'description' => 'required',
            'photo' => 'required'

        ]);
        $dish = new Dishes();
        $dish->name = $request->name;
        $dish->description = $request->description;
        $dish->photo = $request->photo;
        if ($request->menu_id) {
            $dish->menu_id = $request->menu_id;
        } else {
            $dish->menu_id = null;
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
                    'message' => 'Nepavyko išsaugoti patiekalo nuotraukos'
                ], 500);

            $dish->photo = '/storage/' . $filepath;
        }
        if ($dish->save())
            return response()->json([
                'success' => true,
                'message' => $dish->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko išsaugoti patiekalo'
            ], 500);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dishes  $dishes
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
            'description' => 'required',
            'photo' => 'required'

        ]);

        $dish = Dishes::find($id);
        $data = [];

        $data['name'] = $request->name;
        $dish->description = $request->description;
        $dish->photo = $request->photo;
        if ($request->menu_id) {
            $data['menu_id'] = $request->menu_id;
        } else {
            $data['menu_id'] = null;
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
                    'message' => 'Nepavyko išsaugoti patiekalo nuotraukos'
                ], 500);

            $data['photo'] = '/storage/' . $filepath;
        }
        if ($dish->update($data))
            return response()->json([
                'success' => true,
                'message' => $dish->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko atnaujinti patiekalo'
            ], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dishes  $dishes
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

        $dish = Dishes::where('id', $id);

        if ($dish->delete())
            return response()->json([
                'success' => true,
                'message' => 'Patiekalas sėkmingai ištrintas'
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko ištrinti patiekalo'
            ], 500);
    }

    public function byMenu($id)
    {
        $dishes = Dishes::where('menu_id', $id);

        if ($dishes->get())
            return response()->json([
                'success' => true,
                'message' => $dishes->get()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko gauti patiekalų sąrašo'
            ], 500);
    }

    public function sortByPrice()
    {
        $dishes = Dishes::orderBy('name');

        if ($dishes->get())
            return response()->json([
                'success' => true,
                'message' => $dishes->get()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko gauti patiekalų sąrašo'
            ], 500);
    }
    public function search($keyword)
    {
        $dishes = Dishes::where('name', 'like', '%' . $keyword . '%');

        if ($dishes->get())
            return response()->json([
                'success' => true,
                'message' => $dishes->get()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko gauti patiekalų sąrašo'
            ], 500);
    }
}