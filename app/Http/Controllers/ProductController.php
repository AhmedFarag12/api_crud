<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage; 
use Carbon\Carbon;
use illuminate\Support\Str;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::select('id' , 'title' , 'description' , 'image')->get();
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
            'title'=>'required',
            'description'=>'required',
            'image'=>'required|image',
        ]);

        $imageName = Str::random().'.'.$request->image->getClientOriginalExtension();
        Storage::disk('public')->putFileAS('product/image',$request->image , $imageName );
        Product::create($request->post() + ['image' => $imageName]);
        return response()->json([
            'message'=>'Item Added Successfully'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json([
            'product'=>$product
        ]);
    }

 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title'=>'required',
            'description'=>'required',
            'image'=>'nullable',
        ]);

        $product->fill($request->post())->update();

        if($request->hasFile('image')){
            if ($product->image){
                $exist = Storage::disk('public')->exists("product/image/{$product->image}");
                    if($exist){
                        Storage::disk('public')->delete("product/image/{$product->image}");
                 }
    
            }
    
            $imageName = Str::random().'.'.$request->image->getClientOriginalExtension();
            Storage::disk('public')->putFileAS('product/image',$request->image , $imageName );
            $product->image =  $imageName;
            $product->save();
        }
      

        
        return response()->json([
            'message'=>'Item Updated Successfully'
        ]);
    }
 
    public function destroy(Product $product)
    {
        if ($product->image){
            $exist = Storage::disk('public')->exists("product/image/{$product->image}");
             if($exist){
                Storage::disk('public')->delete("product/image/{$product->image}");
             }

        }

        $product->delete();
        return response()->json([
            'message'=>'Item Deleted Successfully'
        ]);
        
    }
}
