<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryProductRequest;

class CategoryProductController
{
    public function showForm(string $type)
    {
        return view('create', ['type' => $type]);
    }

    public function showList(string $type)
    {
        return view('list', ['type' => $type]);
    }

    public function create(CategoryProductRequest $req){
        $req->validated();

        $name = $req->input('category-name');

        if(empty($name)){
            return response()->json([
                'status' => 'error',
                'message' => 'Category name required.'
            ], 400);
        }

        $exists = Categorie::where('name', $name)->first();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category already exists.'
            ], 400);
        }

        try {
            Categorie::create([
                'name' => $name,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Category created successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to create category. Please try again.'
            ], 500);
        }
    }

    public function updateCategory(Request $request, int $id){
        $category = Categorie::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        $category->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ], 200);
    }

    public function getCategory(Request $request){
        // Check if start and length are sent
        $start = $request->input('start');
        $length = $request->input('length');

        if ($start === null || $length === null) {
            return response()->json([
                'error' => 'start or length not provided'
            ], 400);
        }

        $query = Categorie::query();

        // Search filter
        if ($request->has('search') && !empty($request->input('search.value'))) {
            $searchValue = $request->input('search.value');
            $query->where('name', 'like', "%{$searchValue}%");
        }

        // Total records
        $total = $query->count();

        $length = $request->input('length', 10);

        $categories = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $total, // can be different if search applied
            'data' => $categories,
        ]);
    }

    public function getProduct(Request $request){
        // Check if start and length are sent
        $start = $request->input('start');
        $length = $request->input('length');

        if ($start === null || $length === null) {
            return response()->json([
                'error' => 'start or length not provided'
            ], 400);
        }

        $query = Product::query();

        // Search filter
        if ($request->has('search') && !empty($request->input('search.value'))) {
            $searchValue = $request->input('search.value');
            $query->where('name', 'like', "%{$searchValue}%");
        }

        // Total records
        $total = $query->count();

        $length = $request->input('length', 10);

        $products = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $total, // can be different if search applied
            'data' => $products,
        ]);
    }
}
