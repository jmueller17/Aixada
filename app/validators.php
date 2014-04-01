<?php
/*
    (c) 2014 Castellers de la Vila de Gràcia
    info@cvg.cat

    This file is part of l'Admin Blau.

    L'Admin Blau is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    L'Admin Blau is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

Validator::extend('alpha_whitespace', function($attribute, $value)
{
    return preg_match('/^([a-z_-\s])+$/i', $value);
});

Validator::extend('num_whitespace', function($attribute, $value)
{
    return preg_match('/^([0-9\s])+$/i', $value);
});

Validator::extend('currency', function($attribute, $value)
{
    return is_numeric(str_replace(',', '.', $value));
});

Validator::extend('integer_size', function($attribute, $value, $param)
{
    return 
	(strlen($value) == $param[0]) &&
	is_numeric($value);
});

?>