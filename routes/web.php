<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app')->name('home');
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api).+')->name('spa');

Route::get('my', function () {
    return 'مرحبا';
});
