<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Stripe;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products', compact('products'));
    }
    public function cart()
    {
        return view('cart');
    }
    public function addToCart($id)
    {
        $product = Product::findOrFail($id);
          
        $cart = session()->get('cart', []);
  
        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image
            ];
        }
          
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }
    public function update(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Cart updated successfully');
        }
    }
    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Product removed successfully');
        }
    }
    public function checkout(){
        return view('checkout');
    }

    public function order(Request $request): RedirectResponse
    {
        if(session('cart')){
            $carts = session('cart');
            $total = 0;
            $customer_id = 1;
            foreach($carts as $id=>$details){
                $total += $details['price'] * $details['quantity'];
            }
        }
        $order_data = [
            'total_amount' =>$total,
            'customer_id' =>$customer_id,
            'coupon' =>0,
            'payment_method' =>'stripe',
        ];
        $order_id = Order::insertGetId($order_data);
       
        foreach($carts as $id=>$details){
            $order_details_data = [
                'order_id' =>$order_id,
                'product_id' =>$id,
                'quantity' => $details['quantity'],
                'price' => $details['price'],
                'discount' => 0,
                'subtotal' => $details['quantity'] * $details['price'],
            ];
            OrderDetail::insert($order_details_data);
        }
        // echo $order_id;
        // dd($carts);

        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
      
        Stripe\Charge::create ([
                "amount" => $total * 100,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Test payment from itsolutionstuff.com." 
        ]);
                
        return redirect('/')
                ->with('success', 'Order successful!');
    }
    
}
