<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variant;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $variants = Variant::all();
       $products=Product::with('prices.variant_one','prices.variant_two','prices.variant_three')->paginate(3);
       
        return view('products.index',compact('products','variants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $products=Product::all();
        $variants = Variant::all();
        return view('products.create', compact('products','variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_variant.*.option' => 'required',
            'product_variant.*.value' => 'required',
        ]);
    
        // Store the product in the database
        $product_id=Product::create([
            'title'=>$request['title'],
            'sku'=>$request['sku'],
            'description'=>$request['description']
        ])->id;
        // Store the variant options in the database
        foreach ($request->product_variant as $variant) {
            $product_id->variants()->create([
                'option' => $variant['option'],
                'value' => implode(',', $variant['value']),
            ]);
        }
    
        // Redirect the user to the product listing page
        return redirect()->route('products.index');

        




        

        // $product_variant_ids=$this->insertProductVariant($request,$product_id);
        //     $this->insertProductVariantPrices($request,$product_variant_ids,$product_id);

            // $variants = $request->product_variant;
            // if( count($variants) > 0 ) {
            //     foreach($variants as $variant) {

            //         $productVariant = ProductVariant::create([
            //             'product_id' => $product_id->id, 
            //             'variant_id' => $variant['option'], 
            //             'variant'    => implode(",",$variant['value']),
            //         ]);
            //     }
            // }

            // $productVariant = new ProductVariant;
            // $productVariant->variant = $variant;

    }

    public function search(Request $product)
    {
        $this->validate($product, [
            'title' => 'bail|required|string|max:100',
            'variantid' => 'required|exists:variants,id',
            'price_from' => 'required|numeric',
            'price_to' => 'required|numeric',
            'date' => 'required|date',
        ]);
        $title=$product->title;
        $vid=$product->variantid;
        $from=$product->price_from;
        $to=$product->price_to;
        $date=$product->date;

        $products=Product::where([['title', 'LIKE', "%{$title}%"]])
        ->whereDate('created_at',$date)
        ->whereHas('variants', function ($query) use($vid) {
            $query->where('variant_id', '=', $vid);
        })
        ->with(['prices'=>function($query) use($from, $to)
        {
            return $query->with('variant_one','variant_two','variant_three')->whereBetween('price',[$from,$to]);
        }])
        ->paginate(3);

        $variants = Variant::all();
        return view('products.index',compact('products','variants'));
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //$variants = Variant::all();
        
        $data['variants'] = Variant::all();
        $data['product']=$product;
        $data['product']['product_variants']=ProductVariant::where('product_id',$product->id)->get()->groupBy('variant_id');
        $data['prices']=$product->load('prices.variant_one','prices.variant_two','prices.variant_three');
        return view('products.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $product_id=$product->id;
        $this->data_validate($request, $product_id);

        $product->update([
            'title' =>$request->title,
            'sku' =>$request->sku,
            'description' =>$request->description,
        ]);
        $ids=array_column($request->product_variant, 'option');
            $product->productvariants($ids);
            $product_variant_ids=$this->insertProductVariant($request,$product_id);
            $product->prices();
            $this->insertProductVariantPrices($request,$product_variant_ids,$product_id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
