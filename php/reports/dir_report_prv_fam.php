<?php 
require_once '../inc/header.inc.php';

function write_ufs() {
    $db = DBWrap::get_instance();
    $strSQL = 'select uf.id uf_id, uf.name uf_name, m.name m_name,'.
        ' us.login, us.email, m.phone1 from aixada_uf uf'.
        ' join (aixada_user us, aixada_member m)'.
        ' on (uf.id=m.uf_id and m.id=us.member_id)'.
        ' where uf.active=1 and m.active=1'.
        ' order by uf.name, m.name, us.login';
    $rs = $db->Execute($strSQL);
    $brk = array(
        '1_uf_id' => null
    );
    $html = '';
    $uf_count = 0;
    while ($row = $rs->fetch_array()) {
        if ($brk['1_uf_id'] != $row['uf_id']) {
            if ($brk['1_uf_id'] != null) {
                $html .= "</div>\n";
            }
            $brk['1_uf_id'] = $row['uf_id'];
            $uf_count++;
            $html .= '<div class="block" style="margin: 0.1cm 0.3cm; border-top:1px solid #ccc; padding-top: 0.1cm;">';
            $html .= '<div>';
            $html .= '<span class="cel4">'.$row['uf_name'].
                        '#'.$row['uf_id'].'</span>';
        } else {
            $html .= '<div>';
            $html .= '<span class="cel4 nbsp">&nbsp;</span>';
        }
        $html .= '<span class="cel2">'.$row['phone1'].'</span>';
        $html .= '<span class="cel6">'.$row['m_name'].
                            ' ('.$row['login'].')</span>';
        $html .= '<span class="cel6">'.str_replace('@','<span style="display:inline-block; width:0px"></span>@',$row['email']).'</span>';
        $html .= "</div>\n";
    }
    if ($brk['1_uf_id'] != null) {
        $html .= "</div>\n";
        $html .= "<div style=\"text-align: center; margin:0.1cm 0.3cm; border-top:1px solid #ccc; padding-top: 0.2cm;\">" . i18n('active_ufs2') . " = {$uf_count}</div>\n";
    }
    return $html;
}
function write_prv() {
    $db = DBWrap::get_instance();
    $strSQL = 'select id, name, contact, email, phone1'.
        ' from aixada_provider'.
        ' where active=1'.
        ' and (contact <> \'\' '.
            ' or email <> \'\' '.
            ' or phone1 <> \'\') '.
        ' order by name';
    $rs = $db->Execute($strSQL);

    $html = '';
    while ($row = $rs->fetch_array()) {
        $html .= '<div class="block" style="margin: 0.1cm 0.3cm; border-top:1px solid #ccc; padding-top: 0.1cm">';
        $html .= '<div class="cel4">'.$row['name'].'</div>';
        $html .= '<div class="cel2">'.$row['phone1'].'</div>';
        $html .= '<div class="cel6">'.$row['contact'].'</div>';
        $html .= '<div class="cel6">'.str_replace('@','<span style="display:inline-block; width:0px"></span>@',$row['email']).'</div>';
        $html .= "</div>\n";
    }
    $html .= '<div style="margin: 0.1cm 0.3cm; border-top:1px solid #ccc;">&nbsp;</div>';

    return $html;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo 'Dir: Prv-Fam';?></title>
	<link rel="stylesheet" type="text/css"               href="css/reports_paper.css"/>
    <link rel="stylesheet" type="text/css"               href="css/reports_layout.css"/>
  	<link rel="stylesheet" type="text/css" media="print" href="css/reports-print.css"/>
</head>
<body>
    <div class="page-A4p lite">
    <h2 class="start"><?php echo $Text['nav_mng_providers']; ?></h2>
    <?php echo write_prv(); ?>
    <h2 class="start"><?php echo $Text['list_ufs']; ?></h2>
    <?php echo write_ufs(); ?>
    </div>
</body>
</html>
