<?php
/**
 * GetIdOnLvl
 *
 * @version   6.0
 * @date      16.06.2017
 *
 *
 *
 */
	
$doc= $modx->getDocument( $id, 'id,parent'.( $fields ? ',' : '' ).$fields );
$list[]= $doc;
while( $id != $koren && $doc[ 'parent' ] != $koren && $doc[ 'parent' ] != 0 )
{
	$doc= $modx->getDocument( $doc[ 'parent' ], 'id,parent'.( $fields ? ',' : '' ).$fields );
	$list[]= $doc;
}
if( $doc[ 'parent' ] == 0 )
{
	$list[]= array( 'id'=>0 );
}elseif( $doc[ 'parent' ] == $koren ){
	$doc= $modx->getDocument( $doc[ 'parent' ], 'id,parent'.( $fields ? ',' : '' ).$fields );
	$list[]= $doc;
}
$list[]= false;
$list= array_reverse( $list );
return ( $lvl ? $list[ $lvl ][ ( $prm ? $prm : 'id' ) ] : $list );
