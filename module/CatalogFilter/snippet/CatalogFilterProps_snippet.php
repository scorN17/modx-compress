<?php
//v03
//============================================================================
	
$idfilter= intval( $idfilter );

$parentslist= $modx->runSnippet( 'GetIdOnLvl', array( 'id'=>$id, 'koren'=>17 ) );
if( $parentslist ) foreach( $parentslist AS $row ) if( $row[ 'id' ] ) $qq .= ( ! empty( $qq ) ? " OR " : "" ) ."cf.docs LIKE '%,{$row[id]},%'";

if( $qq ) $rr= mysql_query( "SELECT *, cf.id AS cfid, cfv.id AS cfvid
	FROM ". $modx->getFullTableName( '_cat_filter' ) ." AS cf
	LEFT JOIN ". $modx->getFullTableName( '_cat_filter_value' ) ." AS cfv ON cfv.idfilter=cf.id AND cfv.iddoc='{$id}'
	WHERE ( {$qq} ) ".( $idfilter ? " AND cfv.idfilter={$idfilter}" : "" )." AND cf.enabled=1 ORDER BY cf.ii, cf.id" );

if( $rr && mysql_num_rows( $rr ) > 0 )
{
	while( $row= mysql_fetch_assoc( $rr ) )
	{
		if( ! $res[ $row[ 'cfid' ] ][ 'id' ] ) $res[ $row[ 'cfid' ] ]= $row;
		
		if( $md5props )
		{
			if( true )
			{
				$vals= explode( "||", $row[ 'value' ] );
				
				foreach( $vals AS $vv )
				{
					$res[ $row[ 'cfid' ] ][ 'md5' ][ md5( $vv ) ]= $vv;
				}
				
			}elseif( false ){
				$vals= explode( "||", $row[ 'value' ] );
				foreach( $vals AS $vv )
				{
					$mm= explode( "::", $vv );
					if( count( $vals ) >= 2 )
					{
						$res[ $row[ 'cfid' ] ][ 'md5' ][ md5( $mm[ 0 ] ) ]= $mm;
					}else{
						$res[ $row[ 'cfid' ] ][ 'price' ]= $mm[ 1 ];
					}
				}
			}
		}
	}
	return $res;
}else{
	return false;	
}
?>
