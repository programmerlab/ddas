<?php

//Route::get('/', '\Corals\Foundation\Http\Controllers\PublicBaseController@welcome');

Route::get('/', 'SiteController@index');
Route::get('/tnc', 'SiteController@getTNC');

Route::get('marketplace/products/import-product', 'ImportController@test');
Route::post('marketplace/products/store-import-product', 'ImportController@uploadCsv');