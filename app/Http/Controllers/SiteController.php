<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteController extends Controller
{
    
    public function index(){

        return \Illuminate\Support\Facades\View::make('welcome');
        
    }
    
    public function getTNC(){
        
        return \Illuminate\Support\Facades\View::make('TNC');
    }
}
