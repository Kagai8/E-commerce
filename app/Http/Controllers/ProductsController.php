<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Cart;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    
   public function index() {
        /*$products = [0=> ["name"=>"Iphone","category"=>"smart phone","price"=>1000],
        1=>["name"=>"Galaxy","category"=>"tablet","price"=>2000],
        2=>["name"=>"Sony","category"=>"smart phone","price"=>3000]]; */

        $products = Product::paginate(4);

        return view("allproducts",compact("products"));
    }

    public function menProducts(){
        $products = DB::table('products')->where('type', "Men")->get();
        return view("menProducts",compact("products"));
    }

    public function womenProducts(){
        $products = DB::table('products')->where('type', "Women")->get();
        return view("womenProducts",compact("products"));
    }


    public function search(Request $request){
        $searchText = $request->get('searchText');
        $products= Product::where('name',"Like",$searchText."%")->paginate(3);
        return view("allproducts",compact("products"));
    }

    public function addProductToCart(Request $request,$id) {
       /* $request->session()->forget("cart");
        $request->session()->flush();*/



        $prevCart= $request->session()->get('cart');
        $cart = new Cart($prevCart);

        $product = Product::find($id);
        $cart -> addItem($id,$product);
        $request->session()->put('cart', $cart);

       // dump($cart);

       return redirect()->route("allproducts");
    }

    public function increaseSingleProduct(Request $request,$id){

        $prevCart = $request->session()->get('cart');
        $cart = new Cart($prevCart);

        $product = Product::find($id);
        $cart->addItem($id,$product);
        $request->session()->put('cart', $cart);

        //dump($cart);

        return redirect()->route("cartproducts");


    }

    public function decreaseSingleProduct(Request $request,$id){

        $prevCart = $request->session()->get('cart');
        $cart = new Cart($prevCart);

        if( $cart->items[$id]['quantity'] > 1){
                  $product = Product::find($id);
                  $cart->items[$id]['quantity'] = $cart->items[$id]['quantity']-1;
                  $cart->items[$id]['totalSinglePrice'] = $cart->items[$id]['quantity'] *  $product['price'];
                  $cart->updatePriceAndQuantity();
              
                  $request->session()->put('cart', $cart);
                  
          }

       

        return redirect()->route("cartproducts");
    }

    public function showCart(){

        $cart = Session::get('cart');

        //cart is not empty
        if($cart){
           
           return view('cartproducts',['cartItems'=> $cart]);
         //cart is empty
        }else{
           
            return view('cartproducts',['cartItems'=> $cart]);
        }

    }

    public function deleteItemFromCart(Request $request,$id){

        $cart = $request->session()->get("cart");

         if(array_key_exists($id,$cart->items)){
             unset($cart->items[$id]);

         }

         $prevCart =  $request->session()->get("cart");
         $updatedCart = new Cart($prevCart);
         $updatedCart->updatePriceAndQuantity();

         $request->session()->put("cart",$updatedCart);

        return redirect()->route('cartproducts');



    }
}
