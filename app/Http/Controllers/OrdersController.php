<?php

namespace App\Http\Controllers;

use App\Models\Dishes;
use App\Models\Orders;
use App\Models\Menus;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Orders::where('user_id', auth()->user()->id)->get();

        $generatedOrders = [];

        foreach ($orders as $order) {
            $dish = Dishes::find($order->dish_id);
            if ($dish->menu_id) {
                $menu = Menus::find($dish->menu_id);
                $order['menu_name'] = $menu->name;
            } else {
                $order['menu_name'] = 'Nepriskirta';
            }
            $order['dish_name'] = $dish->name;
            $order['description'] = $dish->description;
            $order['photo'] = $dish->photo;
            $generatedOrders[] = $order;
        }

        if ($generatedOrders) {
            return response()->json([
                'success' => true,
                'message' => $generatedOrders
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko gauti užsakymų sąrašo'
            ], 500);
        }
    }

    public function all()
    {
        //Authentification
        if (auth()->user()->role != 0)
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);

        $orders = Orders::all();

        $generatedOrders = [];

        foreach ($orders as $order) {
            $dish = Dishes::find($order->dish_id);
            if ($dish->menu_id) {
                $menu = Menus::find($dish->menu_id);
                $order['menu_name'] = $menu->name;
            } else {
                $order['menu_name'] = 'Nepriskirta';
            }
            $order['dish_name'] = $dish->name;
            $order['description'] = $dish->description;
            $order['photo'] = $dish->photo;
            $generatedOrders[] = $order;
        }

        return response()->json([
            'success' => true,
            'message' => $generatedOrders
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'dish_id' => 'required'
        ]);


        $order = new Orders();
        $order->dish_id = $request->dish_id;
        $order->approved = 0;
        $order->user_id = auth()->user()->id;

        if ($order->save())
            return response()->json([
                'success' => true,
                'message' => $order->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko sukurti užsakymo'
            ], 500);
    }

    public function status($id, Request $request)
    {
        //Authentification
        if (auth()->user()->role != 0)
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);

        $order = Orders::find($id);
        if ($order->update(['approved' => $order->approved === 0 ? 1 : 0]))
            return response()->json([
                'success' => true,
                'message' => 'Užsakymas sėkmingai patvirtintas'
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko patvirtinti užsakymo'
            ], 500);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Orders $orders)
    {
        //Authentification
        if (auth()->user()->role != 0)
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);

        $order = Orders::where('id', $id);

        if ($order->delete())
            return response()->json([
                'success' => true,
                'message' => 'Užsakymas sėkmingai ištrintas'
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Nepavyko ištrinti užsakymo'
            ], 500);
    }
}