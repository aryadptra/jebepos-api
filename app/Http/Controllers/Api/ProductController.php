<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::with('category')->get();

        return new ResponseResource(true, 'Successfully get data', $product);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'thumbnail' => 'required|image',
            'stock' => 'required|integer',
            'selling_price' => 'required|integer',
            'buying_price' => 'required|integer',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $thumbnail = $request->file('thumbnail');
        $thumbnail->storeAs('public/products/thumbnail', $thumbnail->hashName());

        //create product
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'code' => strtoupper(Str::slug($request->name)) . '-' . uniqid(),
            'description' => $request->description,
            'thumbnail' => $thumbnail->hashName(),
            'stock' => $request->stock,
            'selling_price' => $request->selling_price,
            'buying_price' => $request->buying_price,
            'status' => $request->status,
        ]);

        if ($product) {
            //return success with Api Resource
            return new ResponseResource(true, 'Successfully Created Product!', $product);
        }

        //return failed with Api Resource
        return new ResponseResource(false, 'Failed to Create Category!', null);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        return new ResponseResource(true, 'Successfully Get  Product!', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products,name,' . $id,
        ]);

        $product = Product::find($id);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check image update
        if ($request->file('thumbnail')) {

            //remove old thumbnail
            Storage::disk('local')->delete('public/products/thumbnail/' . basename($product->thumbnail));

            //upload new thumbnail
            $thumbnail = $request->file('thumbnail');
            $thumbnail->storeAs('public/products/thumbnail', $thumbnail->hashName());

            //update category with new thumbnail
            $product->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'code' => strtoupper(Str::slug($request->name)) . '-' . uniqid(),
                'description' => $request->description,
                'thumbnail' => $thumbnail->hashName(),
                'stock' => $request->stock,
                'selling_price' => $request->selling_price,
                'buying_price' => $request->buying_price,
                'status' => $request->status,
            ]);
        }

        //update category without image
        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'code' => strtoupper(Str::slug($request->name)) . '-' . uniqid(),
            'description' => $request->description,
            'stock' => $request->stock,
            'selling_price' => $request->selling_price,
            'buying_price' => $request->buying_price,
            'status' => $request->status,
        ]);

        if ($product) {
            //return success with Api Resource
            return new ResponseResource(true, 'Data Product Berhasil Diupdate!', $product);
        }

        //return failed with Api Resource
        return new ResponseResource(false, 'Data Product Gagal Diupdate!', null);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        //remove image
        Storage::disk('local')->delete('public/products/thumbnail/' . basename($product->thumbnail));

        if ($product->delete()) {
            //return success with Api Resource
            return new ResponseResource(true, 'Data Category Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new ResponseResource(false, 'Data Category Gagal Dihapus!', null);
    }
}