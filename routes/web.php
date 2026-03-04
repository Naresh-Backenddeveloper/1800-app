<?php

use App\Http\Controllers\admin\AdminCategoriesContyroller;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminProductController;
use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\UserController;
use Illuminate\Support\Facades\Route;



Route::prefix('/_admin')->group(function () {

    Route::get('', [AuthController::class, 'login']);
    Route::post('', [AuthController::class, 'login']);
    Route::get('/pie-stats', [AdminDashboardController::class, 'getPieStats']);

    Route::middleware('auth.admin')->prefix('/secure')->group(function () {
        Route::get('', [AdminDashboardController::class, 'index']);
        Route::get('users', [UserController::class, 'index']);

        Route::prefix('/adds')->group(function () {
            Route::get('', [AdminProductController::class, 'index']);
        });

        Route::prefix('/categories')->group(function () {
            Route::get('', [AdminCategoriesContyroller::class, 'index']);
            Route::get('add', [AdminCategoriesContyroller::class, 'addcategory']);
            Route::post('add', [AdminCategoriesContyroller::class, 'addcategorySubmit']);
            Route::get('edit/{id}', [AdminCategoriesContyroller::class, 'editcategory']);
            Route::post('edit/{id}', [AdminCategoriesContyroller::class, 'updateCategory']);
            Route::get('delete/{id}', [AdminCategoriesContyroller::class, 'deleteCategory']);
            Route::prefix('/specification')->group(function () {
                Route::get('/{id}', [AdminCategoriesContyroller::class, 'specificationIndex']);
                Route::get('add/{id}', [AdminCategoriesContyroller::class, 'specificationAdd']);
                Route::post('add/{id}', [AdminCategoriesContyroller::class, 'addSpecification']);
                Route::get('edit/{id}', [AdminCategoriesContyroller::class, 'specificationedit']);
                Route::post('edit/{id}', [AdminCategoriesContyroller::class, 'editSpecification']);
                Route::get('delete/{id}', [AdminCategoriesContyroller::class, 'delete']);
            });

            Route::prefix('/sub')->group(function () {
                Route::get('add/{id}', [AdminCategoriesContyroller::class, 'subcategoriesAdd']);
                Route::post('add/{id}', [AdminCategoriesContyroller::class, 'addSubcategorysubmit']);
                Route::get('edit/{id}', [AdminCategoriesContyroller::class, 'subcategoriesedit']);
                Route::post('edit/{id}', [AdminCategoriesContyroller::class, 'subcategoryeditSubmit']);
                Route::get('delete/{id}', [AdminCategoriesContyroller::class, 'deletesubcategory']);
                Route::get('/{id}', [AdminCategoriesContyroller::class, 'subcategories']);
            });
        });
    });
});
