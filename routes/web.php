<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {return view('cashier');});
Route::get('/item', function () {return view('items');});
Route::get('/method', function () {return view('method');});
Route::get('/admin', function () {return view('admin');});