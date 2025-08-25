<?php

use App\Http\Controllers\DataController;
use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\Route;

Route::get('/' , [PagesController::class, 'index'])->name('home');
Route::get('/data' , [PagesController::class, 'data'])->name('data');
Route::post('/upload', [DataController::class, 'upload'])->name('upload');