<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();

        return new ResponseResource(true, 'Successfully get data', $categories);
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
            'name'     => 'required|unique:categories',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create category
        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if ($category) {
            //return success with Api Resource
            return new ResponseResource(true, 'Successfully Created Category!', $category);
        }

        //return failed with Api Resource
        return new ResponseResource(false, 'Failed to Create Category!', null);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::whereId($id)->first();

        if ($category) {
            //return success with Api Resource
            return new ResponseResource(true, 'Successfully Get Data Category!', $category);
        }

        //return failed with Api Resource
        return new ResponseResource(false, 'Category Not Found!', null);
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
            'name'     => 'required|unique:categories,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = Category::find($id);

        //update category without image
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if ($category) {
            //return success with Api Resource
            return new ResponseResource(true, 'Category Successfully Updated!', $category);
        }

        //return failed with Api Resource
        return new ResponseResource(false, 'Failed to Update Category!', null);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return new ResponseResource(false, 'Data Category tidak ditemukan!', null);
        }

        try {
            // Gunakan metode delete untuk menghapus kategori
            $category->delete();

            // Return success dengan Api Resource
            return new ResponseResource(true, 'Data Category Berhasil Dihapus!', null);
        } catch (\Exception $e) {
            // Return failed dengan Api Resource dan pesan kesalahan
            return new ResponseResource(false, 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage(), null);
        }
    }
}
