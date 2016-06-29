//v02
//Очистка лишних значений фильтра каталога
//Event: OnBeforeCacheUpdate
//====================================================================================
if( ! $_SESSION[ 'CatalogFilterValuesCleared' ] )
{
	$_SESSION[ 'CatalogFilterValuesCleared' ]= false;
	
	print '<br /><br />';
	$rr= mysql_query( "SELECT iddoc FROM ".$modx->getFullTableName( '_cat_filter_value' )." GROUP BY iddoc" );
	if( $rr && mysql_num_rows( $rr ) > 0 )
	{
		while( $row= mysql_fetch_assoc( $rr ) )
		{
			$rr2= mysql_query( "SELECT id FROM ".$modx->getFullTableName( 'site_content' )." WHERE id={$row[iddoc]} LIMIT 1" );
			if( $rr2 && mysql_num_rows( $rr2 ) == 0 )
			{
				mysql_query( "DELETE FROM ".$modx->getFullTableName( '_cat_filter_value' )." WHERE iddoc={$row[iddoc]}" );
				print $row[ 'iddoc' ] .'; ';
				continue;
			}

			$rr2= mysql_query( "SELECT id FROM ".$modx->getFullTableName( 'site_content' )." WHERE id={$row[iddoc]} AND isfolder=1 LIMIT 1" );
			if( $rr2 && mysql_num_rows( $rr2 ) == 1 )
			{
				mysql_query( "DELETE FROM ".$modx->getFullTableName( '_cat_filter_value' )." WHERE iddoc={$row[iddoc]}" );
				print $row[ 'iddoc' ] .'; ';
			}
		}
	}
	print '<br /><br /><br />';
}
