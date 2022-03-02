<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Cart;


class CartController extends Controller
{
    public function cartList()
    {
        $cartItems = Cart::content();
        // dd($cartItems);
        return new JsonResponse($cartItems, Response::HTTP_OK);
    }


    public function addToCart(Request $request)
    {
        $cart = Cart::add([
            'id' => $request->id,
            'name' => $request->name,
            'price' => $request->price,
            'qty' => $request->quantity,
            'weight' => 0,
        ]);

        return new JsonResponse([
            'message' => 'Product is Added to Cart Successfully !',
            'success' => true
        ], Response::HTTP_OK);
    }

    public function updateCart(Request $request)
    {
        Cart::update($request->row_id, $request->quantity);

        return new JsonResponse([
            'message' => 'Course is Updated to Cart Successfully !',
            'success' => true
        ], Response::HTTP_OK);
    }

    public function removeCart(Request $request)
    {
        Cart::remove($request->row_id);

        return new JsonResponse([
            'message' => 'Course Cart Remove Successfully !',
            'success' => true
        ], Response::HTTP_OK);
    }
}
