<?php
/**
 * El proyecto en `/php/reports/` propone incorporar los deserrollos propios de
 * la coop *La Cistella* en *Aixada* y así dejar de mantenerlo como un rama
 * independiente.
 *
 * #### Commit-1:
 *
 * Incorporar la gestión de turnos de UFs de *La Cistella* a *Aixada*
 *      
 * Se añade el código para la gestión de turno de *La Cistella* que estaba
 * basado en una tabla equivalente a `aixada_torns`.
 *
 * `install.php` migra el contenido de la tabla de turnos de *La Cistella*
 * a *Aixada*.  
 *
 * El informe de turnos y edición gestión vía url:
 * - `php/reports/torns_report.php[?manage_data.php[?from_date=aaaa-mm-dd]` (informe)
 * - `manage_data.php?table=aixada_torns` (consulta y edición)
 *
 * Para que `manage_data.php?table=aixada_torns` permita editar los datos es 
 * necesario incluir `'may_edit_torns'` en *Roles and their privileges* 
 * (`$rights_of`) de `$config.php`. 
 * Ver ejemplo en `/local_config/config.php.sample`
 *
 */
 
function write_turn_fromDate($from_date) {
    return write_turn_where(
        "uf.active = m.active and dataTorn >='{$from_date}'"
    );
}

function write_turn_uf($uf_id, $from_date) {
    return write_turn_where("t.dataTorn in(
        select dataTorn
        from aixada_torns tuf
        where tuf.ufTorn={$uf_id} and dataTorn >='{$from_date}'
        group by dataTorn
    )");
}

function write_turn_where($where) {
    $db = DBWrap::get_instance();
    $strSQL = 'select dataTorn,'.
        ' t.ufTorn, uf.active uf_active, uf.name uf_name,'.
        ' min(m.name) min_m_name, max(m.name) max_m_name'.
        ' from aixada_torns t'.
        ' join (aixada_uf uf, aixada_member m)'.
        ' on (t.ufTorn=uf.id and uf.id=m.uf_id)'.
        ' where '.$where.
        ' group by dataTorn, t.ufTorn, uf.name'.
        ' order by dataTorn, uf.name';
    $rs = $db->Execute($strSQL);
    
    $brk = [ '1_brk' => '' ];
    $html = '';
    while ($row = $rs->fetch_array()) {
        if ($brk['1_brk'] != $row['dataTorn']) {
            if ( $brk['1_brk'] != '' ) {
                $html .= "</div></div>\n";
            }
            $brk['1_brk'] = $row['dataTorn'];
            $html .= '<div class="block" style="margin: 0.1cm 0.3cm; border-top:1px solid #ccc; padding-top: 0.1cm;">';
            $html .= '<div class="cel4">' . $row['dataTorn'] . '</div>';
            $html .= '<div class="cel12">' . "\n";
        } 
        if ($row['uf_active'] == 0) {
            $html .= '<span style="color:red">*' . i18n('uf_ES_BAIXA') . '*</span> ';
        }
        $html .= $row['uf_name'] . '#'.$row['ufTorn'];
        if ($row['min_m_name'] == $row['max_m_name']) {
            $html .= ' (' . $row['max_m_name'] . ')';
        } else {
            $html .= ' (' . $row['min_m_name'] . ' - ' . $row['max_m_name'] . ')';
        }
        $html .= "<br>\n";
    }
    if ( $brk['1_brk'] != '' ) {
        $html .= "</div></div>\n";
    }
    return $html;
}
