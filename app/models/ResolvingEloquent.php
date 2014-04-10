<?php
/*
    (c) 2014 Jörg Müller <joerg@toytic.com>
             Julian Pfeifle <julian.pfeifle@upc.edu>

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

class ResolvingEloquent extends Eloquent
{
    public function resolve($field) 
    {
	/*
	Log::info("resolving $field to " . $this->$field);
	if (isset($this->resolving_class[$field]))
	    Log::info("resolving $field in " . $this->resolving_class[$field]);
	*/
	return (isset($this->resolving_class[$field])
		? resolve_foreign_key($this->resolving_class[$field], $this->$field)
		: $this->$field);
    }
    
    public static function css_in_index($field, $value=null)
    {
	return '';
    }

    public static $foreign_class = array();

    public static function is_foreign_selection($field)
    {
	return false;
    }

    public static function is_foreign_chooser($field)
    {
	return false;
    }

    public static function is_single_entry_list($field)
    {
	return false;
    }

    public static function is_editable_foreign_field($field)
    {
	return false;
    }

    public static $update_display_after_edit = array();

    public static function is_checkbox($field)
    {
	return false;
    }

    public static function is_textarea($field)
    {
	return false;
    }

    public static function is_editable($field)
    {
	return true;
    }

    public static function is_creatable($field)
    {
	return true;
    }

    public static $dropbox_options_of = array();

    public static $display_size_of_field = array('default' => 15);
}
?>