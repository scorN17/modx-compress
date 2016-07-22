<?php
//v03
//================================================================
	
	$tv_price= 4;
	$price_filter_id= 5;
//================================================================


if( $filterpr )
{
	$props= $modx->runSnippet( 'CatalogFilterProps', array( 'id'=>$id, 'onlyid'=>true, 'md5props'=>false ) );

	$znachs= array();

	foreach( $props AS $row )
	{
		if( $row['type'] == 2 || $row['type'] == 4 )
		{
			$znachs[ $row['cfid'] ]= true;
		}
	}
	
	
	
	
	$price_vals= false;
	
	$qq= "";
	$pr= explode( "/", $filterpr );
	if( $pr )
	{
		foreach( $pr AS $val )
		{
			$prid= explode( "_", $val );
			$prvals= explode( "-", $prid[ 1 ] );
			$prid= $prid[ 0 ];
			if( $prvals )
			{
				if( $znachs[ $prid ] )
				{
					if( $prid == $price_filter_id )
					{
						$price_vals[0]= intval( $prvals[0] );
						$price_vals[1]= intval( $prvals[1] );
						
					}else{
						$prvals[0]= intval( $prvals[0] );
						$prvals[1]= intval( $prvals[1] );
						if( $prvals[0] || $prvals[1] )
							$qq .= ( $qq ? " OR " : "" ) ."( idfilter=".intval($prid)." AND ( ROUND(`value`)>='".( $prvals[0] )."' ".( $prvals[1] ? "AND ROUND(`value`)<='".( $prvals[1] )."'" : "" )." ) )";
					}
					
				}else{
					$qqq= "";
					foreach( $prvals AS $prval ) $qqq .= ( $qqq ? " OR " : "" ) ."id=". $prval;
					$qq .= ( $qq ? " OR " : "" ) ."( idfilter=".intval($prid)." AND ( {$qqq} ) )";
				}
			}
		}
	}
	
	
	
	
	
	/*$qq= "";
	foreach( $filterpr AS $propsval => $val )
	{
		$val= explode( ";", $val );
		if( $val )
		{
			$qqq= "";
			foreach( $val AS $val2 )
			{
				$qqq .= ( $qqq ? " OR " : "" ) ."id=". $val2;
			}
			$qq .= ( $qq ? " OR " : "" ) ."( idfilter={$propsval} AND ( {$qqq} ) )";
		}
	}*/
	
	
	
	$rr= mysql_query( "SELECT * FROM ". $modx->getFullTableName( '_cat_filter_value' ) ." WHERE {$qq}" );
	if( $rr && mysql_num_rows( $rr ) > 0 )
	{
		while( $row= mysql_fetch_assoc( $rr ) )
		{
			$vals[ $row[ 'idfilter' ] ] .= ( $vals[ $row[ 'idfilter' ] ] ? " OR " : "" ) ."`value`='". $row[ 'value' ] ."'";
		}
	}
	
	
	
	
	
	
	$ids= false;
	if( $vals )
	{
		foreach( $vals AS $param => $qqqq )
		{
			$qq= "idfilter={$param} AND ( {$qqqq} )";
			$rr= mysql_query( "SELECT iddoc FROM ". $modx->getFullTableName( '_cat_filter_value' ) ." WHERE {$qq}" );
			if( $rr && mysql_num_rows( $rr ) > 0 )
			{
				while( $row= mysql_fetch_assoc( $rr ) )
				{
					$ids[ $param ][]= $row[ 'iddoc' ];
				}
			}
		}
	}
	
	
	
	
	$tmparr= false;
	//sort( $ids );
	if( $ids )
	{
		$first= true;
		foreach( $ids AS $row )
		{
			if( $first )
			{
				$first= false;
				$tmparr= $row;
				continue 1;
			}
			$tmparr= array_intersect( $tmparr, $row );
		}
	}
	$ids= $tmparr;
	
	
	
	
	
	
	if( $price_vals && ( $price_vals[0] || $price_vals[1] ) )
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
		$rr= mysql_query( "SELECT contentid FROM ".$modx->getFullTableName( 'site_tmplvar_contentvalues' )."
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
	
	
	
	
	
	
	$cc= 0;
	$result= '';
	if( $ids )
	{
		$cc= count( $ids );
		foreach( $ids AS $val )
		{
			$result .= ( $result ? ',' : '' ) . $val;
		}
	}
}


return array( $result, $cc );
?>
