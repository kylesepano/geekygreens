<?php

namespace App\Http\Controllers;

use App\Models\metrics;
use App\Models\shops;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    function index(){
        $shops = shops::all();
        $metrics = metrics::all();
        return view('index')->with('shops', $shops)->with('metrics', $metrics);
    }   

    function data(){
        return view('data');    
    }
}
