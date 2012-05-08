<?php

require_once('inc/database.php');
require_once('local_config/config.php');
//$firephp = FirePHP::getInstance(true);

//DBWrap::get_instance()->debug = true;

function get_all_accounts()
{
  $strXML = '<accounts>'
    . '<row><id f="id">-3</id><name f="name">Caixa</name></row>'
    . '<row><id f="id">-2</id><name f="name">Consum</name></row>'
    . '<row><id f="id">-1</id><name f="name">Manteniment</name></row>';
  $rs = DBWrap::get_instance()->Execute("SELECT id+1000, id, name FROM aixada_uf WHERE active=1");
  while ($row = $rs->fetch_array()) {
    $strXML 
      .= '<row>'
      . '<id f="id">' . $row[0] . '</id>'
      . '<name f="name"><![CDATA[UF ' . $row[1] . ' ' . $row[2] . ']]></name>'
      . '</row>';
  }
  return $strXML . '</accounts>';
}

function account_extract($account_id, $start_date, $num_rows)
{
  $strXML = '<extract>';
  $of = new output_formatter;
  $rs = do_stored_query('account_extract', $account_id, $start_date, $num_rows);
  while ($row = $rs->fetch_assoc()) {
    $strXML .= $of->row_to_XML($row);
  }
  return $strXML . '</extract>';
}

function latest_movements()
{
  $strXML = '<extract>';
  $of = new output_formatter;
  $rs = do_stored_query('latest_movements');
  while ($row = $rs->fetch_assoc()) {
    $strXML .= $of->row_to_XML($row);
  }
  return $strXML . '</extract>';
}

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