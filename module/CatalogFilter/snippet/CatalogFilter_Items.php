<?php
//-----------------------------------------------------------------------------------

if( ! $_x) return array('',0);


$props= $modx->runSnippet('CatalogFilterProps', array('id'=>$id));

$znachs= array();
if(is_array($props) && count($props))
{
	foreach($props AS $row)
	{
		if($row['type'] == 'price' || $row['type'] == 'interval')
		{
			$znachs[$row['cf_id'] ]= true;
		}
	}
}




$price_vals= false;

$qq= "";
$pr= explode('/', $_x);
foreach($pr AS $val)
{
	$prvals= explode('-', $val);
	$prid= intval(array_shift($prvals));
	
	if(false && $znachs[$prid])
	{
		/*if($prid == $price_filter_id )
		{
			$price_vals[0]= intval( $prvals[0] );
			$price_vals[1]= intval( $prvals[1] );

		}else{
			$prvals[0]= intval( $prvals[0] );
			$prvals[1]= intval( $prvals[1] );
			if( $prvals[0] || $prvals[1] )
				$qq .= ( $qq ? " OR " : "" ) ."( idfilter=".intval($prid)." AND ( ROUND(`value`)>='".( $prvals[0] )."' ".( $prvals[1] ? "AND ROUND(`value`)<='".( $prvals[1] )."'" : "" )." ) )";
		}*/

	}else{
		$qqq= "";
		foreach($prvals AS $prval) $qqq .= ($qqq ? " OR " : "") ."id=".$prval;
		$qq .= ($qq ? " OR " : "") ."(cf_id=".intval($prid)." AND ( {$qqq} ) )";
	}
}




$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_catfilter_value')." WHERE {$qq}");
if($rr && $modx->db->getRecordCount($rr))
{
	while( $row= $modx->db->getRow($rr))
	{
		$vals[$row[ 'cf_id'] ] .= ($vals[$row[ 'cf_id' ] ] ? " OR " : "") ."`value`='".$row['value']."'";
	}
}






$ids= false;
if($vals)
{
	foreach($vals AS $param => $qqqq)
	{
		$qq= "cf_id={$param} AND ( {$qqqq} )";
		$rr= $modx->db->query("SELECT itemid FROM ".$modx->getFullTableName('_catfilter_value')." WHERE {$qq}");
		if( $rr && $modx->db->getRecordCount($rr))
		{
			while($row= $modx->db->getRow($rr))
			{
				$ids[$param][]= $row['itemid'];
			}
		}
	}
}




$tmparr= false;
if($ids)
{
	$first= true;
	foreach($ids AS $row)
	{
		if($first)
		{
			$first= false;
			$tmparr= $row;
			continue;
		}
		$tmparr= array_intersect($tmparr, $row);
	}
}
$ids= $tmparr;






if(false &&  $price_vals && ( $price_vals[0] || $price_vals[1] ) )
{
	if( ! $ids )
	{
		$docs= $modx->runSnippet( 'GetDoc6', array( 'ids'=>$id, 'type'=>'childs', 'depth'=>'0', 'isf'=>'0' ) );
		if( $docs )
		{
			foreach( $docs AS $doc )
			{
				$ids[]= $doc['id'];
			}
		}
	}

	$ids_prices= array();
	$rr= mysql_query( "SELECT contentid FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')."
						WHERE tmplvarid={$tv_price} AND ROUND(`value`)>='".$price_vals[0]."' ".( $price_vals[1] ? "AND ROUND(`value`)<='".$price_vals[1]."'" : "" )."" );
	if( $rr && mysql_num_rows( $rr ) > 0 )
	{
		while( $row= mysql_fetch_assoc( $rr ) )
		{
			$ids_prices[]= $row['contentid'];
		}
	}

	$ids= array_intersect( $ids, $ids_prices );
}






$result= '';
if(is_array($ids) && count($ids))
{
	foreach($ids AS $val)
		$result .= ($result ? ',' : '') .$val;
}



return $result;
