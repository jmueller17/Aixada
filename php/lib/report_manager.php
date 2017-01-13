<?php

/** 
 * @package Aixada
 */ 



require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php'.DS.'utilities'.DS.'general.php');

if (!isset($_SESSION)) {
    session_start();
 }

require_once(__ROOT__ . 'local_config'.DS.'lang'.DS. get_session_language() . '.php');
require_once(__ROOT__ . 'php/inc/database.php');



/**
 * The class that manages reports
 *
 * @package Aixada
 * @subpackage Reports
 */
class report_manager {

  /**
   * Report all orders for a given provider and date, extended (old-style) format
   */
  protected function extended_ordersHTML($rs)
  {
    global $Text;
    $matrix = array();
    $uf_list = array();
    $product_changed = true;
    while ($row = $rs->fetch_assoc()) {
        $product_name = $row['product_name'];
        if (!$product_name) // the last row of the output
            break;
        $uf = $row['uf'];
        if ($product_changed) {
            $product_changed = false;
            $matrix[$product_name] = array();
        } else {
            if ($uf == '') { // the product has changed
                $product_changed = true;
                $matrix[$product_name]['Total_qty'] = clean_zeros($row['total_quantity']);
                $matrix[$product_name]['unit'] = $row['unit'];
                continue; 
            }
        }
        $matrix[$product_name][$uf] = clean_zeros($row['qty']);
        $uf_list[] = $uf;
    }    
    $uf_list = array_unique($uf_list);
    natsort($uf_list);
    $strHTML = "<table><thead><tr><th><p class='textAlignLeft'>{$Text['product_name']}</p></th>";
    foreach ($uf_list as $uf) {
        $strHTML .= "<th>" //{$Text['uf_short']} 
            . "$uf</th>";
    }
    $strHTML .= '<th class="report_total_quantity_num">' . $Text['total_qty'] . "</th></tr></thead><tbody>\n";
    foreach (array_keys($matrix) as $product) {
        $strHTML .= "<tr><td><p class='productName'>{$product} [{$matrix[$product]['unit']}]</p></td>";
        foreach ($uf_list as $uf) {
            $strHTML .= '<td>';
            if (isset($matrix[$product][$uf])) {
                $strHTML .= $matrix[$product][$uf];
            }
            $strHTML .= '</td>';
        }
        $strHTML .= '<td class="report_total_quantity_num">' . $matrix[$product]['Total_qty'] . "</td></tr>\n";
    }
    return $strHTML . '</tbody></table>';
  }

  /**
   * Report all orders for a given provider and date, compact (sparse) format
   */
  protected function compact_ordersHTML($rs, $standalone_HTML=false, $with_rollup=true)
  {
    global $Text;
    $headings = array('product_name'  => $Text['product_name'], 
		      'uf'            => 'UF',
		      'qty'           => $Text['quantity']);
    $totals = array('total_quantity'          => $Text['total_qty'],
		    'total_price'        => $Text['total_price'],
                    'iva'             => $Text['iva']);
    $styles   = array('product_name'  => 'style1',
		      'uf'            => 'style2',
		      'qty'     => 'style3',
		      'total_quantity'   => 'report_total_quantity',
		      'total_price'   => 'report_total_price',
                      'iva'       => 'report_total_quantity');
    $options = array('title' => "All orders",
		     'standalone_HTML'    => $standalone_HTML,
		     'pagebreak_after_h1' => true,
		     'additional_h1_info' => array('email' => 'email'),
		     'additional_pagebreak_info' => array('email' => 'email'),
		     'additional_last_info' => 'unit');
    $strHTML = ($with_rollup ? 
		$this->rowset_to_HTML_with_rollup($rs, $headings, $totals, $styles, $options) :
		$this->rowset_to_HTML_without_rollup($rs, $headings, $styles, $options));
    DBWrap::get_instance()->free_next_results();
    return $strHTML;
  }

  protected function compact_orders_for_provider_and_dateHTML($provider_id, $date)
  {
      $rs = do_stored_query('detailed_orders_for_provider_and_date', $provider_id, $date);
      $result = $this->compact_ordersHTML($rs);
      DBWrap::get_instance()->free_next_results();
      return $result;
  }

  protected function extended_orders_for_provider_and_dateHTML($provider_id, $date)
  {
      $rs = do_stored_query('detailed_orders_for_provider_and_date', $provider_id, $date);
      $result = $this->extended_ordersHTML($rs);
      DBWrap::get_instance()->free_next_results();
      return $result;
  }

  protected function write_summarized_orders_html($id, $the_date)
  {
      global $Text;
      $html = '<table style="margin-left:30px; border-collapse:collapse;">'
      	  . '<thead>'	
      	  . '<tr>'	
          . '<th>' . $Text['product_name'] . "</th>\n"
          . '<th>' . $Text['description'] . "</th>\n"
          . '<th>' . $Text['total_qty'] . "</th>\n"
          . '<th>' . $Text['unit'] . "</th>\n"
          . '<th>' . $Text['total_price'] . "</th>\n"
          . "</tr>\n</thead>\n<tbody>\n";
      $rs = do_stored_query('summarized_orders_for_provider_and_date', $id, $the_date);
      while ($row = $rs->fetch_assoc()) {
          $html .=
              '<tr>'
              . '<td style="border:1px solid #ccc;padding:0px 2px;"><p class="productName">' . $row['product_name'] . "</p></td>\n"
              . '<td style="border:1px solid #ccc;padding:0px 2px;">'
              . ($row['description'] ? 
                 '<p class="productDescription">' . $row['description'] . '</p>'
                 : '&nbsp;')
              . "</td>\n"
              . '<td style="border:1px solid #ccc;padding:0px 4px;text-align:right;"><span class="totalQuantity">' 
              . clean_zeros($row['total_quantity']) 
              . "</span></td>\n"
              . '<td style="border:1px solid #ccc;padding:0px 4px;text-align:left;"><span class="units">' . $row['unit'] . "</span></td>\n"
              . '<td style="border:1px solid #ccc;padding:0px 4px;text-align:right;"><span class="totalPrice">' . $row['total_price'] . "</span></td>\n"
              . "</tr>\n";
      }
      DBWrap::get_instance()->free_next_results();
      return $html . "</tbody></table>\n";
  }
  
 public function bundle_orders($arr_providers,$arr_dates, $arr_order_ids)
  {
	  global $Text;
	  
	  //delete old files: 
  	  $files = glob(__ROOT__.'local_config/orders/*'); // get all file names
	  foreach($files as $file){ // iterate files
  			if(is_file($file))
    			unlink($file); // delete file
	  }
		  
      $report_files = $this->create_selected_summarized_orders_html($arr_providers, $arr_dates, $arr_order_ids);
      $zip = new ZipArchive();
      $filename = 'local_config/orders/'.$Text['order_pl'].'X'.$arr_dates[0].'.zip';
      if ($zip->open(__ROOT__.$filename, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
          throw new Exception("cannot open <$filename>\n");
      }      
      foreach($report_files as $file) {
          $localfile = substr(strrchr($file, '/'), 1); // only filename, no directory
          $zip->addFile($file, $localfile);
      }
      $zip->close();
      return $filename;
  }
  
  
  private function create_selected_summarized_orders_html($arr_provider_ids, $arr_dates, $arr_order_ids)
  {
      global $Text;
      $prov_ids = array();
      $prov_order_ids = array();
      $prov_name = array();
      $prov_email = array();
      $prov_phone = array();
      $resp_uf = array();
      $resp_uf_name = array();
      $resp_uf_phone = array();
      $total_price = array();

       if (  count($arr_provider_ids)!= count($arr_dates) )
       	 throw new Exception("Array size mismatch in report_manager.php");

       	 
       	 
      for ($i=0; $i < count($arr_provider_ids); ++$i) {
      	$rs = do_stored_query('get_detailed_order_info', 0,  $arr_provider_ids[$i], $arr_dates[$i]);
      	 while ($row = $rs->fetch_assoc()) {
	          $prov_name[$i]       = $row['name'];
	          $prov_email[$i]      = $row['email'];
	          $prov_phone[$i]      = $row['phone1'];
	          $resp_uf[$i]         = $row['uf_id'];
	          $resp_uf_name[$i]    = $row['uf_name'];
	          //$resp_uf_phone[$id]   = $row['responsible_uf_phone'];
	          //$total_price[$id]     = $row['total_price'];
      	}    
      	DBWrap::get_instance()->free_next_results();      	
      }
       
      $headerfile = __ROOT__.'php/inc/report_header.html';
      $inhandle = @fopen($headerfile, 'r');
      if (!$inhandle)
          throw new Exception("Couldn't open {$headerfile} for reading");
      $header = fread($inhandle, 4096);
      $report_files = array();
      
      
      //foreach($arr_providers as $id) {
      for ($i=0; $i < count($arr_provider_ids); ++$i) {
      	
       //foreach($arr_provider_ids as $id) {

		$report_file =   __ROOT__.'local_config/orders/'.$Text['order_pl'].'.' 
              . str_replace(' ', '_', $prov_name[$i]) . '.'
              . $arr_dates[$i]  . '.html';
          
        $report_file = htmlentities($report_file);
        $report_files[] = $report_file;
        $outhandle = @fopen($report_file, 'w');
        
        if (!$outhandle)
              throw new Exception("Couldn't open {$report_file} for writing");
        
        $html = $header 
              . '<title>' 
              . $Text['order'].' '.$Text['for'].' "'.$prov_name[$i].'" '.
                    $Text['for'].' '.$arr_dates[$i]
              . '</title></head><body>';
          $html .= 
              '<h1>'.$Text['order'].' #'.$arr_order_ids[$i].' '.$Text['for']
              . ' <span class="providerName">'
              . $prov_name[$i]
              . '</span> '.$Text['for'].' '
              . $arr_dates[$i] 
              . '</h1><p><span class="responsibleUF">' 
              . $Text['responsible_uf'] . ': ' .$Text['uf_short'].$resp_uf[$i] . ' ' . $resp_uf_name[$i] 
              //. ' (Tel. ' 
              //. $resp_uf_phone[$id]. ')'
              . '</span><br/><span class="providerInfo">Info '.$Text['provider'].' "'.$prov_name[$i]
              . '": telf: '
              . $prov_phone[$i]
              . ' / email: '
              . $prov_email[$i]
              . '</span></p>';

          $html .= '<h2>' . $Text['summarized_orders'] . ' </h2>';
          $html .= $this->write_summarized_orders_html($arr_provider_ids[$i], $arr_dates[$i] );
          $html .= '<div/>';
          $html .= '<h2>' . $Text['detailed_orders']  .', '. $prov_name[$i] . ', '. $arr_dates[$i].'</h2>';
          $html .= $this->compact_orders_for_provider_and_dateHTML($arr_provider_ids[$i], $arr_dates[$i]);
          $html .= '<div/>';
          $html .= '<h2>' . $Text['detailed_orders']  .', '. $prov_name[$i] . ', '. $arr_dates[$i].'</h2>';
          $html .= $this->extended_orders_for_provider_and_dateHTML($arr_provider_ids[$i], $arr_dates[$i]);
          $html .= '</body></html>';
          fwrite($outhandle, $html);
          
      }      
      return $report_files;      
  }
  
  
  
  /**
   * This function creates HTML code from a rowset iterator.
   * It presupposes a field 'total' in the rowset iterator, and that a WITH ROLLUP 
   * clause IS present in the SQL.
   * @param $rs rowset_iterator
   * @param $hierarchy array of fields in the rowset iterator whose order corresponds to the nesting of the output
   */
  private function rowset_to_HTML_with_rollup($rs, $headings, $totals, $styles, $options)
  {

    if (isset($options['standalone_HTML']) and $options['standalone_HTML']) {
      $strHTML = "<html><head><title>{$options['title']}</title></head>\n" 
	. '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'
	. "\n<body>";
    } else {
      $strHTML = '';
    }

    $hkeys = array_keys($headings);
    $skeys = array_keys($styles);
    $hct = count($hkeys);
    $table = array();
    $stack = array();
    while ($row = $rs->fetch_assoc()) {
      if ($row[$hkeys[0]] == '') continue;  // invalid entry
      $printed_total = false;
      $printed_table = false;
      
      $i=1;
      while($i < $hct and $row[$hkeys[$i]] != '') {
	$i++;
      }
      if ($i == $hct) {
	$table[$row[$hkeys[$hct-2]]] = $row[$hkeys[$hct-1]];
	continue;
      }
      if ($i == $hct-2) {
	$stack[] = '</p>' . 
	  $this->transposed_table_to_HTML($table,
					  $headings[$hkeys[$hct-2]],
					  $headings[$hkeys[$hct-1]],
					  '[' . $row[$options['additional_last_info']] . ']');
	$table = array();
	$printed_table = true;
      } 
      $tmp = '';
      if ($i == $hct-2) {

	$tmp 
	  .= "\n" . '<span class="' . $styles['total_quantity'] . '">'
	  .  $totals['total_quantity'] . ': ' . clean_zeros($row['total_quantity']) 
	  . ' ' . $row[$options['additional_last_info']]
	  .  '</span>';
      }
      $tmp 
	.= "\n" 
          //          . '<span class="' . $styles['iva'] . '">'
          //          . $Text['iva'] . ': ' . $row['iva'] . '</span>'
          . '<span class="' . $styles['total_price'] . '">'
          .  $totals['total_price'] . ': ' . $row['total_price']
          .  '</span>';
      //      $tmp .= "<br/>";
      $stack[] = $tmp;
      $stack[] 
	= "\n<p>" //<br/><span class="' . $styles[$skeys[$i-1]] . '">'
	. "{$row[$hkeys[$i-1]]}";
      
      if ($i==1) {
	for ($j=count($stack)-1; $j>=0; $j--) {
	  $strHTML .= $stack[$j];
	}
	$stack = array();
      }
    } //while ($row = $rs->fetch_assoc())

    return $strHTML;
  }


  /**
   * This function creates HTML code from a rowset iterator.
   * It presupposes a field 'total' in the rowset iterator, and that a WITH ROLLUP 
   * clause IS NOT present in the SQL.
   * @param $rs rowset_iterator
   * @param $hierarchy array of fields in the rowset iterator whose order corresponds to the nesting of the output
   */
  private function rowset_to_HTML_without_rollup($rs, $headings, $styles, $options)
  {

    if (isset($options['standalone_HTML']) and $options['standalone_HTML']) {
      $strHTML = "<html><head><title>{$options['title']}</title></head>\n" 
	. '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'
	. "\n<body>";
    } else {
      $strHTML = '';
    }
    $page_count = 0;
    $current_headings = array();
    $next_to_last_heading = '';
    $last_heading = '';
    foreach (array_keys($headings) as $h) {
      $current_headings[$h] = '';
      $next_to_last_heading = $last_heading;
      $last_heading = $h;
    }
    $table = array();
    $last_heading_label = '';
    $prev_last_heading_label = '';

    while ($row = $rs->fetch_assoc()) {
//       $firephp->log($row, 'row');
      $current_level = 1;
      $prev_last_heading_label = $last_heading_label;
      $last_heading_label = '';
      foreach($options['additional_last_info'] as $o)
	$last_heading_label .= '[' . $row[$o] . ']';
      $table_printed = false;
      foreach($headings as $h => $t) {
	if ($row[$h] != $current_headings[$h]) {
	  $current_headings[$h] = $row[$h];
	  if ($current_level < count($headings) - 1) {
	    if (count($table) > 0) {
	      $strHTML .= $this->transposed_table_to_HTML($table,
							  $headings[$next_to_last_heading],
							  $headings[$last_heading],
							  $prev_last_heading_label);
	      $table = array();
	      $table_printed = true;
	    }
	  }
	  if ($options['pagebreak_after_h1'] and $current_level == 1 and $table_printed) {
	    $strHTML .= $this->_do_pagebreak($row, $h,
					     $options['additional_pagebreak_info']);
	  }
	  if ($current_level < count($headings) - 1) {
	    $strHTML .= "<h{$current_level} style=\"{$styles[$h]}\">"
	      . "{$row[$h]}"
	      . "</h{$current_level}>\n";
	    if ($current_level == 1 and isset($options['additional_h1_info'])) {
	      $info=$options['additional_h1_info'];
	      $strHTML .= "<div>{$row[$info]}</div>";
	    }
	  }
	} // if ($row[$h] != $current_headings[$h])
	$current_level++;
      }
      $table[$row[$next_to_last_heading]] = $row[$last_heading]; 
    }
    if (count($table) > 0)
      $strHTML .= $this->transposed_table_to_HTML($table,
						  $headings[$next_to_last_heading],
						  $headings[$last_heading],
						  $prev_last_heading_label);
    if (isset($options['standalone_HTML']) and $options['standalone_HTML']) {
      $strHTML .= '</body></html>';
    }
    return $strHTML;
  }
  
  private function transposed_table_to_HTML($table, $ntl_h, $l_h, $last_label)
  {
    $strHTML = "\n<table><tbody><tr><td>{$ntl_h}</td>";
    foreach ($table as $next_to_last => $last) {
      $strHTML .= "<td>{$next_to_last}</td>";
    }
    $strHTML .= "</tr><tr><td>{$l_h} {$last_label}</td>";
    foreach ($table as $next_to_last => $last) {
      $strHTML .= '<td>' . clean_zeros($last) .'</td>';
    }
    $strHTML .= '</tr></tbody></table>';
    return $strHTML;
  }

  private function _do_pagebreak($row, $h, $additional_info)
  {
    return '---insert pagebreak here---';
  }
}

?>