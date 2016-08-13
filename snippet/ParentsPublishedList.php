<?php
//v001
//====================================================================
$flag= false;
$doc= $modx->getDocument( $id, 'id,parent' );
if( $doc[ 'parent' ] == 0 ) $flag= true;
while( $doc && $id != $koren && $doc[ 'parent' ] != $koren && $doc[ 'parent' ] != 0 )
{
	$doc= $modx->getDocument( $doc[ 'parent' ], 'id,parent' );
}
if( $id == $koren || $doc[ 'parent' ] == $koren ) $flag= true;
return $flag;
?>
