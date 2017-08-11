<?php

Route::group(array('namespace' => 'Klisl\Statistics\Controllers', 'middleware' => 'web'), function() {

    Route::get('/statistics',['uses' =>'StatController@index'])->name('statistics');
    Route::post('/statistics',['uses' =>'StatController@forms'])->name('forms');

});