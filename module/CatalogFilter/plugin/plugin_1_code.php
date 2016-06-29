//v02
//Кэширование значений фильтра каталога
//Event: OnBeforeCacheUpdate
//===================================================================
$catalog_root= 17;
//===================================================================

$cats= $modx->runSnippet( 'GetDoc6', array( 'ids'=>$catalog_root, 'type'=>'childs', 'depth'=>0, 'isf'=>'1', 'fields'=>'parent,isfolder', 'limit___'=>'50' ) );
$list= $cats;
if( $cats )
{
	foreach( $cats AS $row )
	{
		if( isset( $cats[ $row[ 'parent' ] ] ) ) unset( $list[ $row[ 'parent' ] ] );
	}
}
if( $list )
{
	foreach( $list AS $row )
	{
		$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_cat_filter_values_cache' )." WHERE catid={$row[id]} AND enabled='y' LIMIT 1" );
		if( $rr && mysql_num_rows( $rr ) == 0 )
		{
			$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_cat_filter_values_cache' )." WHERE catid={$row[id]} LIMIT 1" );
			if( $rr && mysql_num_rows( $rr ) == 0 )
			{
				mysql_query( "INSERT INTO ".$modx->getFullTableName( '_cat_filter_values_cache' )." SET catid={$row[id]}" );
			}elseif( $rr ){
				mysql_query( "UPDATE ".$modx->getFullTableName( '_cat_filter_values_cache' )." SET enabled='y' WHERE catid={$row[id]} LIMIT 1" );
			}
		}
	}	
}
$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_cat_filter_values_cache' )." WHERE ".time()."-dt>".(60*60*24*1)." AND enabled='y' GROUP BY catid LIMIT 100" );
if( $rr && mysql_num_rows( $rr ) > 0 )
{
	$tmparray_2= array();
	while( $row= mysql_fetch_assoc( $rr ) )
	{
		if( $tmparray_2[ $row[ 'catid' ] ] ) continue;
		$tmparray_2[ $row[ 'catid' ] ]= true;
		
		mysql_query( "UPDATE ".$modx->getFullTableName( '_cat_filter_values_cache' )." SET enabled='n' WHERE catid={$row[catid]}" );
		
		$tmparray= array();
		
		$items= $modx->runSnippet( 'GetDoc6', array( 'ids'=>$row[ 'catid' ], 'type'=>'childs', 'depth'=>1, 'isf'=>'0', 'fields'=>'' ) );
		if( $items )
		{
			foreach( $items AS $item )
			{
				$rr2= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_cat_filter_value' )." WHERE iddoc={$item[id]}" );
				if( $rr2 && mysql_num_rows( $rr2 ) > 0 )
				{
					while( $row2= mysql_fetch_assoc( $rr2 ) )
					{
						$row2[ 'value' ]= addslashes( trim( $row2[ 'value' ] ) );
						if( ! $row2[ 'value' ] ) continue;
						if( $tmparray[ $row[ 'catid' ] ][ $row2[ 'idfilter' ] ][ $row2[ 'value' ] ] ) continue;
						$tmparray[ $row[ 'catid' ] ][ $row2[ 'idfilter' ] ][ $row2[ 'value' ] ]= true;
						
						$rr3= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_cat_filter_values_cache' )."
							WHERE catid={$row[catid]} AND ( cfid=0 OR ( cfid={$row2[idfilter]} AND `value`='{$row2[value]}' ) ) ORDER BY cfid LIMIT 1" );
						if( $rr3 && mysql_num_rows( $rr3 ) == 1 )
						{
							mysql_query( "UPDATE ".$modx->getFullTableName( '_cat_filter_values_cache' )."
								SET ".( mysql_result( $rr3, 0, 'cfid' ) == '0' ? "cfid={$row2[idfilter]}, `value`='{$row2[value]}'," : "" )." docid={$row2[iddoc]}, cfvid={$row2[id]}, dt=".time().", enabled='y'
									WHERE id=". mysql_result( $rr3, 0, 'id' ) ." LIMIT 1" );
						}elseif( $rr3 ){
							mysql_query( "INSERT INTO ".$modx->getFullTableName( '_cat_filter_values_cache' )." SET
								docid={$row2[iddoc]}
								, dt=".time()."
								, catid={$row[catid]}
								, cfvid={$row2[id]}
								, cfid={$row2[idfilter]}
								, `value`='{$row2[value]}'
								" );
						}
					}
				}
			}
		}else{
			mysql_query( "UPDATE ".$modx->getFullTableName( '_cat_filter_values_cache' )." SET dt=".time()." WHERE id=". mysql_result( $rr, 0, 'id' ) ." LIMIT 1" );
			continue;
		}
		//print '<pre>'.print_r($tmparray,1).'</pre>';
	}
}
