//v02
//Очистка кэша фильтра
//Event: OnDocFormSave
//===================================================================
$e= &$modx->Event;
if( $e->name == 'OnDocFormSave' )
{
	$myid= $e->params[ 'id' ];
	$myparent= $modx->getDocument( $myid, 'parent' );
	$myparent= $myparent[ 'parent' ];
	mysql_query( "UPDATE ".$modx->getFullTableName( '_cat_filter_values_cache' )." SET dt=0, enabled='n' WHERE catid={$myparent}" );
}
