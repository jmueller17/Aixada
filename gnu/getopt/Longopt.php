<?php
  /**************************************************************************
    Getopt.php -- PHP port of GNU getopt from glibc 2.0.6
    Copyright (c) 1987-1997 Free Software Foundation, Inc.
    Copyright (c) 1998 by Aaron M. Renn (arenn@urbanophile.com)
    PHP  Port Copyright (c) 2010 by Jeffrey Fulmer <jeff@joedog.org>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU Library General Public License as published 
    by  the Free Software Foundation; either version 2 of the License or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful, but
    WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Library General Public License for more details.

    You should have received a copy of the GNU Library General Public License
    along with this program; see the file COPYING.LIB.  If not, write to 
    the Free Software Foundation Inc., 59 Temple Place - Suite 330, 
    Boston, MA  02111-1307 USA

  **************************************************************************/
  define('NO_ARGUMENT', 0);
  define('REQUIRED_ARGUMENT', 1);
  define('OPTIONAL_ARGUMENT', 2);
  
  class LongOpt {
    var $name;
    var $hasarg;
    var $flag = null;
    var $val;

    function __construct($name, $hasarg, $flag, $val) {
      if (($hasarg != NO_ARGUMENT) && ($hasarg != REQUIRED_ARGUMENT) && ($hasarg != OPTIONAL_ARGUMENT)) {
        print "invalid message\n";
      }
      $this->name = $name;
      $this->hasarg = $hasarg;
      $this->flag = $flag;
      $this->val = $val;
    }

    function hasArg() {
      return($this->hasarg);
    }

    function getName() {
      return($this->name);
    }

    function getFlag() {
      return($this->flag);
    }

    function getValue() {
      return($this->val);
    }

    function appendFlag($flag) {
      $this->flag .= $flag;  
    }
  } // Class LongOpt
?>
