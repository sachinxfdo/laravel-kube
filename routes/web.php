<?php

use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return Category::all();
});

Route::get('/create', function () {
    return Category::create([
        'name'=> 'Sahan',
    ]);
});