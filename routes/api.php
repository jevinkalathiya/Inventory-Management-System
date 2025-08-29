<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryProductController;


Route::middleware(['verifyApiClient'])->group(function () {
    Route::controller(CategoryProductController::class)->group(function () {
        Route::get('getcategory','getCategory'); // to fetch all the categories
        Route::post('createcatgeory','createCategory');
        Route::put('/updatecategory/{id}', 'updateCategory'); // update
        Route::get('getproduct','getProduct'); // to fetch all the categories
        Route::post('createproduct','createProduct');
        Route::put('/updatecategory/{id}', 'updateCategory'); // update
    });
});
