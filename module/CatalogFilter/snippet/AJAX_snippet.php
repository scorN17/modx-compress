<?php
$id= intval( $_GET[ 'id' ] );

if( $_GET[ 'act' ] == 'catalogfilter_get_items' )
{
	return $modx->runSnippet( 'CATALOG', array( 'id'=>$id, 'type'=>'content', 'filterpr'=>$_REQUEST[ 'filterpr' ] ) );
}
?>
