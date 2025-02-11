<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Infos;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');

    }

    public function addToCart(Request $request, Product $product)
    {

        $cartItem = new CartItem([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'quantity' => $request->input('quantity', 1),
            'size' => $request->input('size'),
            'color' => $request->input('color'),
        ]);
        // $cartItem = auth()->user()->cartItems()->get();

        $cartItem->save();

        return redirect()->back()->with('success', 'Product added to cart');
    }
    public function update(Request $request, CartItem $cartItem)
{
    $cartItem->update(['quantity' => $request->input('quantity')]);
    return redirect()->route('cart.index')->with('success', 'Quantity updated');
}

    public function index(Request $request)
    {
        $totalCartCount = $request->user()->cartItems()->count();
        $infos = Infos::paginate(1);
        $categories = Category::all();
        $cartItems = $request->user()->cartItems()->with('product')->get();
        $totalCost = $cartItems->sum(function ($cartItem) {
            return $cartItem->quantity * $cartItem->product->price;
        });
        return view('cart.index', compact('cartItems', 'totalCost','infos','categories','totalCartCount'));


    }
    public function remove(CartItem $cartItem)
   {
    $cartItem->delete();
    return redirect()->route('cart.index')->with('success', 'Item removed from cart');
   }
}
