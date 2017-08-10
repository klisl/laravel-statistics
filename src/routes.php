<?php

Route::group(array('namespace' => 'Klisl\Statistics\Controllers'), function() {

    Route::get('/statistics',['uses' =>'StatController@index'])->name('statistics');
    Route::post('/statistics',['uses' =>'StatController@forms'])->name('forms');
    Route::post('/statistics',['uses' =>'StatController@enter'])->name('enter');

});