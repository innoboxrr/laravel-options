<?php

use Illuminate\Support\Facades\Route;

Route::get('policies', 'OptionController@policies')
	->name('policies');

Route::get('policy', 'OptionController@policy')
	->name('policy');

Route::get('index', 'OptionController@index')
	->name('index');

Route::get('show', 'OptionController@show')
	->name('show');

Route::post('create', 'OptionController@create')
	->name('create');

Route::put('update', 'OptionController@update')
	->name('update');

Route::delete('delete', 'OptionController@delete')
	->name('delete');

Route::post('restore', 'OptionController@restore')
	->name('restore');

Route::delete('force-delete', 'OptionController@forceDelete')
	->name('force.delete');

Route::post('export', 'OptionController@export')
	->name('export');