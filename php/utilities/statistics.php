<?php


require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'php/utilities/general.php');
require_once(__ROOT__ . 'local_config/config.php');
//$firephp = FirePHP::getInstance(true);

$max_ent = -1;
$n_ent = -1;
$ordinal = array();

function read_timeline_data($which)
{
    $entities = array(); // uf, product or provider
    $query = '';
    switch($which) {
    case 'uf':
        $query = "select distinct i.uf_id as id, u.name from aixada_order_item i left join aixada_uf u on i.uf_id=u.id order by i.uf_id;";
        break;
    case 'provider':
        $query = "select distinct p.id, p.name from aixada_provider p left join aixada_product pr on pr.provider_id=p.id left join aixada_order_item i on i.product_id=pr.id;";
        break;
    case 'product':
        $query = "select distinct p.id, p.name from aixada_product p left join aixada_order_item i on i.product_id=p.id;";
        break;
    }
    $rs = DBWrap::get_instance()->Execute($query);
    while ($row = $rs->fetch_assoc()) {
        $entities[$row['id']] = $row['name'];
    }
    DBWrap::get_instance()->free_next_results();
    $orders = array();
    $max_total_price = array();
    $max_week = array();
    $global_max_price = -1;
    $to_drop = array();
    foreach($entities as $ent => $name) {
        $rs = do_stored_query($which . '_weekly_orders', $ent);
        while ($row = $rs->fetch_assoc()) {
            $week        = clean_zeros($row['order_week']);
            $total_price = $row['total_price'];
            if (!isset($orders[$week]))
                $orders[$week] = array();
            $orders[$week][$ent] = $total_price; // the entity orders in week $week
            if (!isset($max_total_price[$ent])) { // the entity is new
                $max_total_price[$ent] = -1;
                if (!isset($orders[$week-1]))
                    $orders[$week-1] = array();
                $orders[$week-1][$ent] = 'b'; // remember when entity is born
                $max_week[$ent] = $week;
            }
            if ($max_total_price[$ent] < $total_price)
                $max_total_price[$ent] = $total_price;
            if ($max_week[$ent] < $week)
                $max_week[$ent] = $week;
            if ($global_max_price < $total_price)
                $global_max_price = $total_price;
        }
        DBWrap::get_instance()->free_next_results();
        if (!isset($max_week[$ent])) {
            $to_drop[] = $ent;
        }
        if (!isset($max_total_price[$ent]) or
            !floatval($max_total_price[$ent]))
            $max_total_price[$ent] = 1;
    }
    foreach($to_drop as $ent) {
        unset($entities[$ent]);
    }
    foreach($entities as $ent => $name) {
        $mw = $max_week[$ent] + 1;
        if (!isset($orders[$mw]))
            $orders[$mw] = array();
        $orders[$mw][$ent] = 'o'; // remember when entity is over
    }
    ksort($orders);
    return array($orders, $entities, $max_total_price, $max_week, $global_max_price);
}

function write_svg($date_lines, $lines, $text, $min_x, $max_x, $min_y, $max_y, $weeks_into_past)
{
    $svg =
        '<?xml version="1.0" standalone="no"?>' 
        . '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" '
        . '"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'
        . '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" '
        . 'viewBox="' . (($min_x-200)). ' ' . ($min_y) . ' ' 
        . (($max_x-$min_x+600)) . ' ' . (($max_y-$min_y)) . '">' . "\n"
        . '<title>History of the Aixada</title>' . "\n";
    
    $svg .= $date_lines . "\n";
    foreach($lines as $line) {
        $svg .= $line . "\n";
    }
    $svg .= $text; 
    return $svg . "</svg>";
}

function read_orders($which)
{
    // This reads data from the file $tmpname, if it exists; 
    // else, it refreshes it from the database

    $orders = $entities = $max_total_price = $max_week = array();
    $global_max_price = -1;
    $tmpname = sys_get_temp_dir() . '/timeline-' . $which . '.php';
    $inhandle = @fopen($tmpname, 'r');
    if (!$inhandle) {
        $data = read_timeline_data($which);
        $outhandle = @fopen($tmpname, 'w');
        if (!$outhandle)
            throw new Exception("Couldn't open {$tmpname} for writing");
        $s = serialize($data);
        fwrite($outhandle, $s);
        fclose($outhandle);
    } else {
        $s = fread($inhandle, 1000000);
        $data = unserialize($s);
    }
    return $data;
}

function line_color($ent)
{
    global $max_ent, $ordinal;
    return 'rgb(' . floor($ordinal[$ent] * 255/$max_ent) . ',0,' . floor((1-$ordinal[$ent]/$max_ent)*255) . ')';
}

function make_date_x($weeks_into_past, $stepsize)
{
    $row = DBWrap::get_instance()->Execute("select first_order(1)")->fetch_array();
    $s0 = $row[0];
    $s = substr($s0, 0, 8) . '01'; // first day of the month of the first order
    $t0 = strtotime($s0);
    $t = strtotime($s . ' + 1 month');
    $now = strtotime("now");
    $m = 0;
    $c = 60 * 60 * 24 * 7; // one week in seconds
    $arr_x = array();
    while($t < $now) {
        $t = strtotime($s . ' + ' . $m++ . ' month');
        $arr_x[strftime('%d-%m-%Y', $t)] = $stepsize * ($t - $t0) / $c;
    }
    return $arr_x; 
}

function make_grid_lines($xmin, $xmax, $ymin, $ymax, $weeks_into_past, $stepsize)
{
    global $n_ent;
    $arr_x = make_date_x($weeks_into_past, $stepsize);
    $gray = 'rgb(104,104,104)';
    $svg = '<g stroke="' . $gray . '" stroke-width=".2" font-size="8" text-anchor="middle" fill="gray">';
    foreach($arr_x as $date => $x) {
        if ($x >= $xmin) 
            $svg .= '<path d="'
                . 'M' . round($x, 3) . ',' . $ymin . 'L' . round($x, 3) . ',' . $ymax . '"/>'
                . '<text x="' . round($x, 3) . '" y="' . round($ymax+10, 3) . '">' 
                . $date . '</text>';
    }
    $dxmin = $xmin - 40;
    $svg .= '<path d="M' . $dxmin . ',' . round(10*($n_ent - 20), 3) 
        .  'L' . round($xmax, 3) . ',' . round(10*($n_ent - 20), 3) . '"/>'
        . '<path d="M' . $dxmin . ',' . round(10*($n_ent - 40), 3)
        . 'L' . round($xmax, 3) . ',' . round(10*($n_ent - 40), 3) . '"/>'
        . '<text x="' . round($xmax+8, 3) . '" y="' . round(10*($n_ent - 20), 3) . '">20</text>'
        . '<text x="' . round($xmax+8, 3) . '" y="' . round(10*($n_ent - 40), 3). '">40</text>';
    return $svg . "</g>\n";
}

function outlined_text($x, $y, $text, $color, $anchor)
{
    return
        '<text style="text-anchor:' . $anchor . ';stroke:white;' 
        . 'stroke-width:3;fill:white" x="' . $x . '" y="' . $y . '">'
        . $text . '</text>'
        . '<text style="text-anchor:' . $anchor . ';fill:' . $color 
        . ';stroke:' . $color . ';stroke-width:.2" x="' . $x . '" y="' . $y . '">'
        . $text . '</text>';
}

function make_active_time_lines($which, // 'uf', 'provider' or 'product'
                                $weeks_into_past = 32)
{
    //    global $firephp;
    global $max_ent, $n_ent, $ordinal;
    $ordinal = array();
    $line = array();
    $text = '';
    $active_entities = array();
    $max_active_entities = 0;
    $height = array();
    $prev_pos = array();
    $stepsize = 20;
    $cur_x = 0;
    $min_x = 1000000;
    $data = read_orders($which);
    list($orders, $entities, $max_total_price, $max_week, $global_max_price) = $data;
    $ct=0;
    $max_ct = count($orders);
    $entities_k = array_keys($entities);
    $max_ent = intval(array_pop($entities_k));
    $n_ent = count($entities);
    $label_position = array(); // $lab_y => array($lab_x, $lab_len)
    $pix_per_letter = 8; // average number of pixels per letter in the label
    $ent_rep_labels = '';
    foreach($orders as $week => $events) {
        if ($max_ct - $ct <= $weeks_into_past or $weeks_into_past < 0) {                
            if ($cur_x < $min_x) {
                $min_x = $cur_x;
            }
        }
        $cur_x = $stepsize * $week;
        ksort($events);
        if ($ct>0 and $ct%26==0 and $ct<= $max_ct - 8) {
            if ($max_ct - $ct <= $weeks_into_past or $weeks_into_past < 0) {
                foreach($active_entities as $ent) {
                    $ent_rep_labels .= 
                        outlined_text($cur_x + .5*$stepsize, $height[$ent]+2, $ent, 
                                      line_color($ent), 'middle');
                }
            }
        }
        foreach($events as $ent => $event) { 
            // first process birth & death and how this affects the line heights
            if ($event === 'b') {
                $ordinal[$ent] = count($ordinal)+1;
                $active_entities[] = $ent;
                if (count($active_entities) > $max_active_entities)
                    $max_active_entities = count($active_entities);
                $h = $height[$ent] = 10*($n_ent-count($active_entities));
                $prev_pos[$ent] = array($cur_x, $h); // save the position of the circle
                $lab_x = $cur_x-5; // the preliminary position of the label
                $lab_y = $h+2;
                // first, clean the stored label positions by taking only those
                // labels that don't stick into the current position
                $tmp = array();
                foreach($label_position as $lpos_y => $lab_xdata) {
                    if ($cur_x <= $lab_xdata[0] + $lab_xdata[1]) // x_pos + length
                        $tmp[$lpos_y] = $lab_xdata;
                }
                $label_position = $tmp;
                // if there is an interfering label at the current height,
                if (isset($label_position[$lab_y])) {
                    while (isset($label_position[$lab_y])) {
                        $lab_y -= 20;
                    }
                    if ($max_ct - $ct <= $weeks_into_past or $weeks_into_past < 0)
                        $text .= '<path style="stroke:green;stroke-width:1" d="M'
                            . round($lab_x,3) . ',' . round($lab_y, 3) 
                            . 'L' . round($cur_x,3) . ',' . round($h, 3) . '"/>';
                }
                $lab_len = $pix_per_letter * (strlen($ent) + 1 + strlen($entities[$ent]));
                $label_position[$lab_y] = array($lab_x, $lab_len);
                if ($max_ct - $ct <= $weeks_into_past or $weeks_into_past < 0) {
                    $text .= outlined_text(round($lab_x, 3), round($lab_y, 3), 
                                           $ent . ' <![CDATA[' . $entities[$ent] . ']]>', 
                                           line_color($ent), 'end')
                        . '<circle style="fill:green;stroke:green" '
                        . 'cx="' . round($cur_x, 3) . '" cy="' . round($h, 3) . '" r="2"/>';
                }
                $line[$ent] = '<g stroke="' . line_color($ent) 
                    . '" fill="none" stroke-linecap="round">';
            }
        }
        foreach($events as $ent => $event) { 
            // now process deaths
            if ($event === 'o') {
                $h = $height[$ent];
                if ($max_ct - $ct <= $weeks_into_past or $weeks_into_past < 0) {                
                    $line[$ent] .= '<line style="stroke-width:1" '
                        . 'x1="' . round($prev_pos[$ent][0], 3) 
                        . '" y1="' . round($prev_pos[$ent][1], 3)
                        . '" x2="' . round($cur_x-15, 3) 
                        . '" y2="' . round($h, 3) . '"/>'
                        . '<circle style="fill:green;stroke:green" '
                        . 'cx="' . round($cur_x-15, 3) 
                        . '" cy="' . round($h, 3) . '" r="2"/>';
                    $text .= outlined_text(round($cur_x-11, 3), round($h+2, 3), 
                                           $ent . ' <![CDATA[' . $entities[$ent] . ']]>',
                                           line_color($ent), 'start');
                }
                $line[$ent] .= "</g>\n";
                $lab_len = $pix_per_letter * (strlen($ent) + 1 + strlen($entities[$ent]));
                $label_position[$h+2] = array($cur_x, $lab_len);
                $index = array_search($ent, $active_entities);
                if ($ct <= $max_ct - 8) { // as long as the end is far away, make the entities drop
                    for ($i=$index+1; $i<count($active_entities); $i++) {
                        $height[$active_entities[$i]] += 10;
                    }
                    array_splice($active_entities, $index, 1);
                }
            }
        }
        foreach($events as $ent => $event) { 
            // ... and everything in between
            if (is_numeric($event)) {
                $old_x = $prev_pos[$ent][0];  
                $old_y = $prev_pos[$ent][1];
                $style0 = 'stroke-width:'; 
                $style = '';
                $nominal_thickness = max((10 * $event/$max_total_price[$ent]), 0.5);
                if ($cur_x - $old_x > $stepsize) {
                    $style = $style0 . '.5;stroke-dasharray:2 2;';
                } else {
                    $style = $style0 . round($nominal_thickness,3);
                }
                $h = $height[$ent];
                if ($cur_x - $old_x > $stepsize) {
                    if ($max_ct - $ct <= $weeks_into_past or $weeks_into_past < 0) {
                        $line[$ent] .= '<circle style="fill:white;stroke:green" ' 
                            . 'cx="' . round($old_x, 3) . '" cy="' . round($old_y, 3) . '" r="2"/>'
                            . '<circle style="fill:white;stroke:green" ' 
                            . 'cx="' . round($cur_x - 2, 3) . '" cy="' . round($h, 3) . '" r="2"/>';
                        if ($old_y != $h) {
                            $text .= outlined_text(round($old_x + 3, 3), round($old_y + 2, 3), 
                                                   $ent, 'green', 'start') 
                                . outlined_text(round($cur_x - 5, 3), round($h + 2, 3), 
                                                $ent, 'green', 'end');
                        }
                    }
                } /* else if ($cur_x - $old_x > $stepsize) { */
/*                         $f = ($cur_x - $old_x)/$stepsize; */
/*                         $tx = $old_x/$f + ($f-1)*$cur_x/$f; */
/*                         $ty = $old_y/$f + ($f-1)*$h/$f; */
/*                         $line[$ent] .= */
/*                             '<path style="' . $style0 . $nominal_thickness */
/*                             . '" d="M' . round($tx, 3) . ',' . round($ty, 3)  */
/*                             . 'L' . round($cur_x, 3) . ',' . round($h, 3) . '"/>'; */
/*                     } */
                else {
                    if ($max_ct - $ct <= $weeks_into_past or $weeks_into_past < 0) {
                        if ($old_y==$h) { // we do a line
                            $line[$ent] .= '<path style="' . $style 
                                . '" d="M' . round($old_x, 3) . ',' . round($old_y, 3)
                                . 'L' . round($cur_x, 3) . ',' . round($h, 3) . '"/>';
                        } else {
                            $line[$ent] .= '<path style="' . $style 
                                . '" d="M' . round($old_x, 3) . ',' . round($old_y, 3)
                                . 'C' . round($old_x+14, 3) . ','  . round($old_y, 3) . ' '
                                . round($cur_x-14, 3) . ',' . round($h, 3) . ' ' 
                                . round($cur_x, 3) . ',' . round($h, 3) . '"/>';
                        }
                    }
                }
                $prev_pos[$ent] = array($cur_x, $height[$ent]);
            }
        }        
        $ct++;
    }
    // Now draw the finishing dots
    $cur_x += 8*$stepsize;
    foreach($active_entities as $ent) {
        $h = $height[$ent];
        $text .= outlined_text(round($cur_x-11, 3), round($h+2, 3), 
                               $ent . ' <![CDATA[' . $entities[$ent] . ']]>',
                               line_color($ent), 'start');
    }
    $text = '<g font-size="8">' . $ent_rep_labels . $text . '</g>';
    $cur_x -= 3*$stepsize;
    $min_y = 10*($n_ent - $max_active_entities) - 30;
    $max_y = 10*$n_ent + 30;
    $date_lines = make_grid_lines($min_x, $cur_x, $min_y, $max_y, $weeks_into_past, $stepsize);
    $svg = write_svg($date_lines, $line, $text, $min_x, $cur_x, $min_y, $max_y, $weeks_into_past);
    $svgname = 'local_config/timeline.svg';
    $outhandle = @fopen($svgname, 'w');
    if (!$outhandle)
        throw new Exception("Couldn't open $svgname for writing");
    fwrite($outhandle, $svg);
    fclose($outhandle);
    $scalefactor = 1.5;
    return '<object width="' . ($scalefactor*($cur_x-$min_x+200))
        . '" height="' . ($scalefactor*($max_y-$min_y+200))
        . '" data="' . $svgname . '" type="image/svg+xml"></object>';
}

function collect_balances_data()
{
    $balance_of_uf = array();
    $date_of_week = array();
    $rs = do_stored_query('uf_weekly_balance');
    $week = -1;
    while ($row = $rs->fetch_assoc()) {
        $uf = $row['uf'];
        $week = $row['week'];
        if (!isset($balance_of_uf[$uf])) {
            $date_of_week[$week] = $row['date'];
            $balance_of_uf[$uf] = array();
        }
        $balance_of_uf[$uf][$week] = $row['balance'];
    }
    return array($balance_of_uf, $date_of_week);
}

function make_balances()
{
    //    global $firephp;
    list($balance_of_uf, $date_of_week) = collect_balances_data();
    $line = array();
    $style = 'stroke-width:2';
    foreach ($balance_of_uf as $uf => $balances) {
        $line[$uf] = 'M';
        foreach ($balances as $week => $balance) {
            $line[$uf] .= 2000-100*$week . ',' . $balance . 'L';
        }
        $line[$uf] = rtrim($line[$uf], 'L');
    }

    $lines = '';
    foreach ($line as $uf => $l) {
        $lines .= '<path d="' . $l . '"/>';
        $lines .= outlined_text(2000, $balance_of_uf[$uf][0], $uf, 'blue', 'start');
    }
    $svg =
        '<?xml version="1.0" standalone="no"?>' 
        . '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" '
        . '"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'
        . '<svg><g stroke="blue" fill="none" stroke-width="2">' 
        . $lines . '</g></svg>';
    $svgname = 'local_config/balances.svg';
    $outhandle = @fopen($svgname, 'w');
    if (!$outhandle)
        throw new Exception("Couldn't open $svgname for writing");
    fwrite($outhandle, $svg);
    fclose($outhandle);
    return $svgname;
}


/**
 * 
 * Looks like that this is never called...
 * @param unknown_type $the_date
 * @throws Exception
 */
function spending_per_provider_JSON($the_date) 
{
    $data = array(); 
    $cum_sum = 0;
    $cum = array();
    $curr_prov = '';
    $first_prov = true;
    $min_month = 13;
    $max_month = 0;
    $rs = do_stored_query('spending_per_provider', $the_date);
    while ($row = $rs->fetch_assoc()) {
        if ($row['name'] != $curr_prov) {
            $curr_prov = $row['name'];
            $prov_name = $row['id'] . ' ' . $curr_prov;
            $data[$prov_name] = array();
            $cum[$prov_name] = array();
            $cum_sum = 0;
        } 
        $month = $row['m'];
        $data[$prov_name][$month] = $row['s'];
        $cum[$prov_name][$month] = $cum_sum;
        $cum_sum += $row['s'];
        $min_month = min($min_month, $month);
        $max_month = max($max_month, $month);
    }
    $json = '[';
    $first_prov = true;
    foreach($data as $prov_name => $spending) {
        if (count($spending) < 2) continue;
        if (!$first_prov) {
            $json .= ',';
        } else {
            $first_prov = false;
        }
        //        $json .= '"' . $prov_name . '":[';
        $json .= '[';
        $first_data = true;
        $cum_sum = 0;
        for ($m=$min_month; $m<=$max_month; $m++) {
            if (!$first_data) {
                $json .= ',';
            } else {
                $first_data = false;
            }
            if (isset($spending[$m])) {
                $s = $spending[$m];
                $c = $cum[$prov_name][$m];
                $cum_sum = $c;
            } else {
                $s = 0;
                $c = $cum_sum;
            }
            $json .= '{"x":' . $m . ',"y":' . $c 
                //                . ',"y0":' . $c
                . '}';
        }
        $json .= ']';
    }
    $json .= ']';
    return $json;
}

	
?>
