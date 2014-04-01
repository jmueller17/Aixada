<?php
/*
    Copyright (C) 2013 by Jörg Müller and Julian Pfeifle 
    joerg@toytic.com
    julian.pfeifle@upc.edu

    This file is part of Aixada.

    Aixada is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Aixada is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

require_once('validators.php');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function() 
	   {
	       return View::make('aixada_index');
	   });

Route::get('/test.html', function() 
	   {
	       return View::make('test');
	   });

