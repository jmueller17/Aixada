<?php
  /**************************************************************************
    Getopt.php -- PHP port of GNU getopt from glibc 2.0.6
    Copyright (c) 1987-1997 Free Software Foundation, Inc.
    Java Port Copyright (c) 1998 by Aaron M. Renn (arenn@urbanophile.com)
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
    
    Example:
    #!/usr/bin/php
    <?php
      require_once("gnu/getopt/Getopt.php");
      require_once("gnu/getopt/Longopt.php");

      $longopt = array();
      $longopt[0] = new LongOpt("help",  NO_ARGUMENT,       null, 'h');
      $longopt[1] = new LongOpt("about", REQUIRED_ARGUMENT, null, 'a');
      $getopt     = new Getopt($argv, "a:bc:d:hW;", $longopt);
      $c;
      $arg;
      while (($c = $getopt->getopts()) != -1) {
        switch($c) {
        case 'a':
        case 'd':
          $arg = $getopt->getOptarg();
          print("You picked $c with an argument of ".(($arg != null) ? $arg : "null")."\n");
          break;
        case 'b':
        case 'c':
          $arg = $getopt->getOptarg();
          print("You picked $c with an argument of ".(($arg != null) ? $arg : "null") . "\n");
          break;
        case 'h':
          print "Usage: test [options] \n";
          break;
        case '?':
          break; // getopt() already printed an error
        default:
          print("getopt() returned $c\n");
        }
      }
    ?>
   
  **************************************************************************/

  # REQUIRE_ORDER stop processing when the first non-option is seen
  define('REQUIRE_ORDER', 1);

  # PERMUTE the contents of ARGV so all the non-options are at the end
  define('PERMUTE',2);

  # RETURN_IN_ORDER allows options to return in an expected order
  define('RETURN_IN_ORDER', 3);

  class Getopt {
    var $optarg   = '';
    var $optind   = 0;
    var $opterr   = true;
    var $optopt   = '?';
    var $nextchar = '';
    var $optstr   = '';
    var $first    = 1;
    var $last     = 1;
    var $endparse = false;
    var $longopts = array();
    var $argv     = array();
    var $longonly;
    var $longind;
    var $posixly;
    var $longopted;
    var $ordering;
    var $progname;

    function __construct($argv, $optstr, $longopts=null, $longonly=false) {
      if (strlen($optstr) == 0) $optstr = " ";
      $this->argv     = $this->array_kshift($argv); // pop [0]=>name off the stack
      $this->optstr   = $optstr;
      $this->longopts = $longopts;
      $this->longonly = $longonly;

      $this->posixly = false;
      foreach ($_ENV as $key => $val) {
        if ($key == "POSIXLY_CORRECT") {
          $this->posixly = true;
          break;
        }
      }

      # Determine how to handle the $ordering of options and non-options
      if (substr($this->optstr, 0, 1) == '-') {
        $this->ordering = RETURN_IN_ORDER;
        if (strlen($optstr) > 1) 
          $this->optstr = substr($this->optstr, 1);

      } else if (substr($this->optstr, 0, 1) == '+') {
        $this->ordering = REQUIRE_ORDER;
        if (strlen($this->optstr) > 1)
          $this->optstr = substr($this->optstr, 1);
      } else if ($this->posixly) {
        $this->ordering = REQUIRE_ORDER;
      } else {
        $this->ordering = PERMUTE; # The normal default case
      }
    }

    /**
     * In GNU getopt, it is possible to change the string containg valid options
     * on the fly because it is passed as an argument to getopt() each time.  In
     * this version we do not pass the string on every call.  In order to allow
     * dynamic option string changing, this method is provided.
     *
     * @param $optstr The new option string to use
     */
    function setOptstring($optstr) {
      if (strlen($optstr) == 0)
        $optstr = " ";
      $this->optstr = $optstr;
    }

    /**
     * $optind it the index in ARGV of the next element to be scanned.
     * This is used for communication to and from the caller
     * and for communication between successive calls to `getopt'.
     *
     * When `getopt' returns -1, this is the index of the first of the
     * non-option elements that the caller should itself scan.
     *
     * Otherwise, `$optind' communicates from one call to the next
     * how much of ARGV has been scanned so far.  
     */
    function getOptind() {
      return($this->optind);
    }

    /**
     * This method allows the $optind index to be set manually.  Normally this
     * is not necessary (and incorrect usage of this method can lead to serious
     * lossage), but $optind is a public symbol in GNU getopt, so this method 
     * was added to allow it to be modified by the caller if desired.
     *
     * @param $optind The new value of $optind
     */
    function setOptind($optind) {
      $this->optind = $optind;
    }

    /**
     * Since in GNU getopt() the argument vector is passed back in to the
     * function every time, the caller can swap out argv on the fly.  Since
     * passing argv is not required in the Java version, this method allows
     * the user to override argv.  Note that incorrect use of this method can
     * lead to serious lossage.
     *
     * @param argv New argument list
     */
    function setArgv($argv) {
      $this->argv = $argv;
    }

    /** 
     * For communication from `getopt' to the caller.
     * When `getopt' finds an option that takes an argument,
     * the argument value is returned here.
     * Also, when `$ordering' is RETURN_IN_ORDER,
     * each non-option ARGV-element is returned here.
     * No set method is provided because setting this variable has no effect.
     */
    function getOptarg() {
      return($this->optarg);
    }

    /**
     * Normally Getopt will print a message to the standard error when an
     * invalid option is encountered.  This can be suppressed (or re-enabled)
     * by calling this method.  There is no get method for this variable 
     * because if you can't remember the state you set this to, why should I?
     */
    function setOpterr($opterr) {
      $this->opterr = $opterr;
    }

    /**
     * When getopt() encounters an invalid option, it stores the value of that
     * option in $optopt which can be retrieved with this method.  There is
     * no corresponding set method because setting this variable has no effect.
     */
    function getOptopt() {
      return($this->optopt);
    }

    /**
     * Returns the index into the array of long options (NOT argv) representing
     * the long option that was found.
     */
    function getLongind() {
      return($this->longind);
    }

    /**
     * Exchange the shorter segment with the far end of the longer segment.
     * That puts the shorter segment into the right place.
     * It leaves the longer segment in the right place overall,
     * but it consists of two parts that need to be swapped next.
     * This method is used by getopt() for argument permutation.
     */
    function exchange($argv) {
      $bottom = $this->first;
      $middle = $this->last;
      $top    = $this->optind;
      $tmp    = "";

      while ($top > $middle && $middle > $bottom) {
        if ($top - $middle > $middle - $bottom) {
          # Bottom segment is the short one. 
          $len = $middle - $bottom;

          # Swap it with the top part of the top segment. 
          for ($i = 0; $i < $len; $i++) {
              $tmp = $this->argv[$bottom + $i];
              $this->argv[$bottom + $i] = $this->argv[$top - ($middle - $bottom) + $i];
              $this->argv[$top - ($middle - $bottom) + $i] = $tmp;
            }
          # Exclude the moved bottom segment from further swapping. 
          $top -= $len;
        } else {
          # Top segment is the short one.
          $len = $top - $middle;

          # Swap it with the bottom part of the bottom segment. 
          for ($i = 0; $i < $len; $i++) {
              $tmp = $this->argv[$bottom + $i];
              $this->argv[$bottom + $i] = $this->argv[$middle + $i];
              $this->argv[$middle + $i] = $tmp;
            }
          # Exclude the moved top segment from further swapping. 
          $bottom += $len;
        }
      }
      # Update records for the slots the non-options now occupy. 
      $this->first += ($this->optind - $this->last);
      $this->last   = $this->optind;
    }

    /**
     * Check to see if an option is a valid long option.  Called by getopt().
     * Put in a separate method because this needs to be done twice.  (The
     * C getopt authors just copy-pasted the code!).
     *
     * @param $longind A buffer in which to store the 'val' field of found LongOpt
     * @return Various things depending on circumstances
     */
    function checkLongOption() {
      $pfound  = null;
      $nameend = -1;
      $ambig   = false;
      $exact   = false;
      $this->longopted = true;
      $this->longind   = -1;
  
      $nameend = strpos($this->nextchar, "=");
      if (empty($nameend)) {
        $nameend = strlen($this->nextchar);
      }
      
      for ($i = 0; $i < count($this->longopts); $i++) {
        if ($this->startsWith($this->longopts[$i]->getName(),substr($this->nextchar, 0, $nameend))) {
          if ($this->longopts[$i]->getName()==(substr($this->nextchar, 0, $nameend))) {
            # Exact match found
            $pfound = $this->longopts[$i];
            $longind = $i;
            $exact = true;
            break;
          } else if ($pfound == null) {
            $pfound = $this->longopts[$i];
            $longind = $i;
          } else {
            $ambig = true;
          }
        }
      } 
  
      if ($ambig && !$exact) {
        if ($opterr) {
          print "{$this->progname}: option '{$this->argv[$this->optind]}' is ambiguous\n";
        } 
        $this->nextchar = "";
        $optopt = 0;
        ++$optind;
 
        return('?');
      }
 
      if ($pfound != null) {
        $this->optind++;
 
        if ($nameend != strlen($this->nextchar)) {
          if ($pfound->hasArg() != NO_ARGUMENT) {
              if (strlen(substr($this->nextchar, $nameend)) > 1)
                $this->optarg = substr($this->nextchar, $nameend+1);
              else
                $this->optarg = "";
          } else {
            if ($this->opterr) {
              # -- option
              if ($this->startsWith($this->argv[$this->optind - 1], "--")) {
                print "{$this->progname}: option '--{$this->argv[$this->optind-1]}' doesn't allow an argument\n";
              } else {
                print "{$this->progname}: option '--{$this->argv[$this->optind-1]}' doesn't allow arguments\n";
              }
            }
            $this->nextchar = "";
            $this->optopt   = $pfound->getValue();
   
            return('?');
          }
        } else if ($pfound->hasArg() == REQUIRED_ARGUMENT) {
          if ($this->optind < count($this->argv)) {
            $this->optarg = $this->argv[$this->optind];
            $this->optind++;
          } else {
            if ($this->opterr) {
              print "{$this->progname}: option '{$this->argv[$this->optind-1]}' requires an argument\n";
            }
            $this->nextchar = "";
            $this->optopt   = $pfound->getValue();
            if (substr($this->optstr, 0, 1) == ':')
              return(':');
            else
              return('?');
          }
        } 
        $this->nextchar = "";

        if ($pfound->getFlag() != null) {
          $pfound->appendFlag($pfound->getValue());
          return(0);
        }
        return($pfound->getValue());
      } # if ($pfound != null)

      $this->longopted = false;
      return(0);
    }


    /**
     * This method returns a char that is the current option that has been
     * parsed from the command line.  If the option takes an argument, then
     * the internal variable '$optarg' is set which is a String representing
     * the the value of the argument.  This value can be retrieved by the
     * caller using the getOptarg() method.  If an invalid option is found,
     * an error message is printed and a '?' is returned.  The name of the
     * invalid option character can be retrieved by calling the getOptopt()
     * method.  When there are no more options to be scanned, this method
     * returns -1.  The index of first non-option element in argv can be
     * retrieved with the getOptind() method.
     *
     * @return Various things as described above
     */
    function getopts() {
      $this->optarg = null;

      if ($this->endparse == true) return(-1);

      if (empty($this->nextchar)) {
        # If we have just processed some options following some non-options,
        #  exchange them so that the options come first.
        if ($this->last > $this->optind)
          $this->last = $this->optind;
        if ($this->first > $this->optind)
          $this->first = $this->optind;

        if ($this->ordering == PERMUTE) {
          # If we have just processed some options following some non-options,
          # exchange them so that the options come first.
          if (($this->first != $this->last) && ($this->last != $this->optind))
            $this->exchange($this->argv);
          else if ($this->last != $this->optind)
            $this->first = $this->optind;
  
          # Skip any additional non-options
          # and extend the range of non-options previously skipped.
          while (($this->optind < count($this->argv))            && ($this->argv[$this->optind]=="" ||
                 (substr($this->argv[$this->optind],0,1) != '-') ||  $this->argv[$this->optind]=="-")) {
              $this->optind++;
          }
          $this->last = $this->optind;
        }

        # The special ARGV-element `--' means premature end of options.
        # Skip it like a null option,
        # then exchange with previous non-options as if it were an option,
        # then skip everything else like a non-option.
        if (($this->optind != count($this->argv)) && $this->argv[$this->optind]=="--") {
          $this->optind++;
          if (($this->first != $this->last) && ($this->last != $this->optind))
            $this->exchange($this->argv);
          else if ($this->first == $this->last)
            $this->first = $this->optind;
  
          $this->last   = count($this->argv);
          $this->optind = count($this->argv);
        }
  
        # If we have done all the ARGV-elements, stop the scan
        # and back over any non-options that we skipped and permuted.
        if ($this->optind == count($this->argv)) {
          # Set the next-arg-index to point at the non-options
          # that we previously skipped, so the caller will digest them.
          if ($this->first != $this->last)
            $this->optind = $this->first;

          return(-1);
        }

        # If we have come to a non-option and did not permute it,
        # either stop the scan or describe it to the caller and pass it by.
        if ($this->argv[$this->optind] == ""              || 
            substr($this->argv[$this->optind],0,1) != '-' ||
            $this->argv[$this->optind] == "-")             {
          if ($this->ordering == REQUIRE_ORDER)
            return(-1);

          $this->optarg = $this->argv[$this->optind++];
            return(1);
        }
      
        # We have found another option-ARGV-element.
        # Skip the initial punctuation.
        if ($this->startsWith($this->argv[$this->optind], "--"))
          $this->nextchar = substr($this->argv[$this->optind], 2);
        else
          $this->nextchar = substr($this->argv[$this->optind], 1);
      }

      /** 
       Check whether the ARGV-element is a long option.
  
       If $longonly and the ARGV-element has the form "-f", where f is
       a valid short option, don't consider it an abbreviated form of
       a long option that starts with f.  Otherwise there would be no
       way to give the -f short option.
  
       On the other hand, if there's a long option "fubar" and
       the ARGV-element is "-fu", do consider that an abbreviation of
       the long option, just like "--fu", and not "-f" with arg "u".
  
       This distinction seems to be the most useful approach.  */
      if (($this->longopts != null) && ($this->startsWith($this->argv[$this->optind], "--") || 
          ($this->longonly && (($this->argv[$this->optind].length()  > 2)           || 
          ($this->optstr.indexOf($this->argv[$this->optind].charAt(1)) == -1)))))   {
        $c = $this->checkLongOption();

        if ($this->longopted) {
          return($c);
        }
         
        # Can't find it as a long option.  If this is not getopt_$longonly,
        # or the option starts with '--' or is not a valid short
        # option, then it's an error.
        # Otherwise interpret it as a short option.
        if (!$this->longonly || $this->startsWith($this->argv[$this->optind], "--") || 
           ($this->optstr.indexOf($this->nextchar.charAt(0)) == -1))        {
          if ($opterr) {
            if ($this->startsWith($this->argv[$this->optind], "--")) {
              print "{$this->progname}: unrecognized options '{$this->argv[$this->optind]}'\n";
            } else {
              print "{$this->progname}: unrecognized option '{$this->argv[$this->optind]}'\n";
            }
          }
          $this->nextchar = "";
          $this->optind ++;
          $this->optopt = 0;
          return('?');
        }
      } # if (longopts)

      # Look at and handle the next short option-character */
      $c = substr($this->nextchar, 0, 1);
      if (strlen($this->nextchar) > 1) {
        $this->nextchar = substr($this->nextchar, 1);
      } else {
        $this->nextchar = "";
      }
  
      $temp = "";
      if (-1 != $this->indexOf($this->optstr, $c)) { 
        $temp = substr($this->optstr, $this->indexOf($this->optstr, $c));
      }

      if (empty($this->nextchar)) $this->optind++;

      if ((empty($temp)) || ($c == ':')) {
        if ($this->opterr) {
          if ($this->posixly) {
            print "{$this->progname}: illegal option -- '$c'\n";
          } else {
            print "{$this->progname}: invalid option -- '$c'\n";
          }
        }
        $this->optopt = $c;

        return('?');
      }

      # Convenience. Treat POSIX -W foo same as long option --foo
      if ((substr($temp, 0, 1) == 'W') && (strlen($temp) > 1) && (substr($temp, 1, 1) == ';')) {
        if (!empty($this->nextchar)) {
          $this->optarg = $this->nextchar;
        } else if ($optind == count($this->argv)) {
          if ($opterr) {
            # 1003.2 specifies the format of this message. 
            print "{$this->progname}: option '$c' requires an argument\n";
          }

          $this->optopt = $c;
          if (substr($this->optstr, 0, 1) == ':')
            return(':');
          else
            return('?');
        } else {
          # We already incremented `$optind' once;
          # increment it again when taking next ARGV-elt as argument. 
          $this->nextchar = $this->argv[$this->optind];
          $this->optarg   = $this->argv[$this->optind];
        }

        $c = $this->checkLongOption();
 
        if ($this->longopted) {
          return(c);
        } else {
          $this->nextchar = null;
          $this->optind++;
          return('W');
        }
      }

      if ((strlen($temp) > 1) && (substr($temp, 1, 1) == ':')) {
        if ((strlen($temp) > 2) && (substr($temp, 2, 1) == ':')) {
          if (!empty($this->nextchar)) {
            $this->optarg = $this->nextchar;
            $this->optind++;
          } else {
            $this->optarg = null;
          }
          $this->nextchar = null;
        } else {
          if (!empty($this->nextchar)) {
            $this->optarg = $this->nextchar;
            $this->optind++;
          } else if ($this->optind == count($this->argv)) {
            if ($this->opterr) {
              # 1003.2 specifies the format of this message
              print "{$this->progname}: option requires an argument -- '$c'\n";
            }
            $this->optopt = $c;
 
            if (substr($this->optstr, 0, 1) == ':') 
              return(':');
            else 
              return('?');

          } else {
            $this->optarg = $this->argv[$this->optind];
            $this->optind++;

            # Ok, here's an obscure Posix case.  If we have o:, and
            # we get -o -- foo, then we're supposed to skip the --,
            # end parsing of options, and make foo an operand to -o.
            # Only do this in Posix mode.
            if (($this->posixly) && $this->optarg=="--") {
              if ($optind == count($this->argv)) {
                if ($opterr) {
                  # 1003.2 specifies the format of this message
                  print "{$this->progname}: option requires an argument -- '$c'\n";
                }
                $this->optopt = $c;
   
                if (substr($this->optstr,0,1) == ':') {
                  return(':');
                } else {
                  return('?');
                }
              }

              # Set new $optarg and set to end
              # Don't permute as we do on -- up above since we
              # know we aren't in permute mode because of Posix.
              $this->optarg   = $this->argv[$this->optind];
              $this->optind ++;
              $this->first    = $this->optind;
              $this->last     = count($this->argv);
              $this->endparse = true;
            }
          }
          $this->nextchar = null;
        }
      }
      return($c);
    }

    function array_kshift(&$arr) {
      $r = array();
      foreach ($arr as $key => $val) {
        if ($key == 0) {
          $this->progname = end(explode('/',$val));
        }
        if ($key >= 1) {
          $k = $key-1;
          $r[$k] = $val;
        }
      }
      return $r;
    }

    function startsWith($haystack,$needle,$case=true) {
      if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
      return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
    }

    function endsWith($haystack,$needle,$case=true) {
      if($case){return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);}
      return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
    }

    function indexOf($haystack, $needle) {
      $arr = array();
      $arr = str_split($haystack);
      for($i = 0,$z = count($arr); $i < $z; $i++){
        if ($arr[$i] == $needle) {  #finds the needle
          return $i;
        }
      }
      return -1;
    }

  } # Class Getopt
?>
