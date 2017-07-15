<?php
/**
 * CatalogFilter_Props
 *
 * Параметры фильтра
 *
 * @version 7.0
 * @date    15.07.2017
 *
 *
 *
 *
 *
 *
 *
 *
 */
	
$catalogroot= 15; // Корень каталога

//-----------------------------------------------------------------------------------

$parentslist= $modx->runSnippet('GetIdOnLvl', array('id'=>$id, 'koren'=>$catalogroot));
if(is_array($parentslist) && count($parentslist))
	foreach($parentslist AS $row)
		if($row['id'])
			$qq .= ( ! empty($qq) ? " OR " : "") ."cf.folders LIKE '%,{$row[id]},%'";

if($qq) $rr= $modx->db->query("SELECT *, cf.id AS cfid, cfv.id AS cfvid
	FROM ".$modx->getFullTableName('_catfilter')." AS cf
	LEFT JOIN ".$modx->getFullTableName('_catfilter_value')." AS cfv ON cfv.cf_id=cf.id AND cfv.itemid='{$id}'
	WHERE ( {$qq} ) AND cf.e='y' ORDER BY cf.i, cf.id" );
if($rr && $modx->db->getRecordCount($rr))
{
	while($row= $modx->db->getRow($rr))
	{
		if( ! $res[$row['cfid'] ]['id']) $res[$row['cfid'] ]= $row;
		
		if($values)
		{
			if(true)
			{
				if($row['value'])
				{
					$md5= md5($row['value'] .($row['dop']?'-'.$row['dop']:''));
					$res[$row['cfid'] ]['values'][$md5]= $row['value'] .($row['dop']?' &ndash; '.$row['dop']:'');
				}
				
			}elseif(false){
				$vals= explode('||', $row['value']);
				if(is_array($vals) && count($vals))
					foreach($vals AS $vv)
						if($vv)
							$res[$row['cfid'] ]['values'][md5($vv .($row['dop'])?'-'.$row['dop']:'')]= $vv .($row['dop']?' &ndash; '.$row['dop']:'');
				
				
			}elseif(false){
				$vals= explode('||', $row['value']);
				if(is_array($vals) && count($vals))
					foreach($vals AS $vv)
						if($vv)
							$res[$row['cfid'] ]['values'][md5($vv)]= $vv;
				
				
				
			}elseif( false ){
				$vals= explode( "||", $row[ 'value' ] );
				foreach( $vals AS $vv )
				{
					$mm= explode( "::", $vv );
					if( count( $vals ) >= 2 )
					{
						$res[ $row[ 'cfid' ] ][ 'values' ][ md5( $mm[ 0 ] ) ]= $mm;
					}else{
						$res[ $row[ 'cfid' ] ][ 'price' ]= $mm[ 1 ];
					}
				}
			}
		}
	}
	return $res;
}else return false;
