<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.beranda');
});

Route::get('/about', function() {
    return view('pages.about',[
        'nama'=>'Desrico Siallagan',
        'umur'=>'22',
        'alamat'=>'samosir'
    ]);
});

Route::get('/about/{id}', function() {
    return view('pages.detail');
});

Route::view('/contact','pages.contact');
