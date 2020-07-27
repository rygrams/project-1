<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Clothing;
use App\Category;
use Session;
use App\Cart;


class ClothingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $clothings = Clothing::all();
       
       return response()->json($clothings);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clothings = Clothing::orderBy('id','desc')->get();
        // $clothing->all();
        $categories = Category::all();
        return view('admin.clothing.add', ['clothings' => $clothings, 'categories' => $categories]); 
    //    return view('admin.clothing.add'); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'prix' => 'required|numeric|min:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);



        // Ajout d'image a un vetement 
        
        
        $clothing = new Clothing;
        if($request->hasFile('image'))
        {
            if($request->file('image')->isValid())
            {
                $extension = $request->image->extension();
                $fileName = 'ok'.'.'.$extension;
                // $request->image->move(public_path('images/clothing_images', $fileName));
                $path =  $request->image->storeAs('images/clothing_image', $fileName, 'public' );
                dd('added image');
            }
        }
        $clothing->create($request->all());
        // $clothing->save();


        return redirect()->back()->with('status', 'Vetements bien ajouter');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $clothing = Clothing::find($id);
        return view('admin.clothing.edit', ['clothing' => $clothing]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'prix' => 'required|numeric|min:500'
        ]);
        $clothing = Clothing::find($id);
        $clothing->update($request->all());
        return back()->with('update', 'Le vetement mis a jour');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $clothing = Clothing::find($id);
        $clothing->delete();
        return redirect()->back()->with('delete', 'Vetement supprimer');
    }

    public function getContentCart(){
        if(!Session::has('cart')){
            return view('orders.index');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        
        return view('orders.index', ['items' => $cart->items, 'totalPrice' => $cart->totalPrice]);
    }


    public function addToCart(Request $request, $id){
        $clothing = Clothing::find($id);
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->add($clothing, $clothing->id);
        $request->session()->put('cart',$cart);
        // if(Session::has('cart')){
            // dd(session()->pull('cart'));
            // dd($cart->items);
        // }
        return redirect()->back();
        // return view('orders.index',);
    }
    
    
    public function deleteOnCart(Request $request, $id){
        $clothing = Clothing::find($id);
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->delete($clothing->id);
        $request->session()->put('cart',$cart);
        return redirect()->back();
        // dd(Session::get('cart'));

    }
}
