<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;



class ImportController extends Controller{

    public function test(){
        return view('import_product');
    }

    public function uploadCsv(Request $request){
        echo '<pre>'; print_r($request->all());
        echo '<pre>'; print_r($_FILES);
        exit;
    }

}