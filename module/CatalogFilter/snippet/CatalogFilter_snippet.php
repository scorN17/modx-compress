<?php
//v03
//============================================================================



$myid= $modx->documentIdentifier;
if( empty( $id ) ) $id= $myid;


$page= intval( $_GET[ 'p' ] );



$propsvalues= array();
$cats= $modx->runSnippet( 'GetDoc6', array( 'ids'=>$id, 'type'=>'all', 'isf'=>'1' ) );
if( $cats )
{
	foreach( $cats AS $row )
	{
		$catsqq .= ( ! empty( $catsqq ) ? ' OR ' : '' ) . "catid={$row[id]}";
	}
}
if( $catsqq )
{
	$rr= mysql_query( "SELECT * FROM ". $modx->getFullTableName( '_cat_filter_values_cache' ) ." WHERE ( {$catsqq} ) AND enabled='y' GROUP BY cfid, `value` ORDER BY cfid, `value`" );
	if( $rr && mysql_num_rows( $rr ) > 0 )
	{
		while( $row= mysql_fetch_assoc( $rr ) )
		{
			$propsvalues[ $row[ 'cfid' ] ][]= $row;
		}
	}
}





$docs= $modx->runSnippet( 'GetDoc6', array( 'ids'=>$id, 'type'=>'childs', 'isf'=>'0' ) );
if( $docs )
{
	foreach( $docs AS $row )
	{
		$docsqq .= ( ! empty( $docsqq ) ? ' OR ' : '' ) . "iddoc={$row[id]}";
	}
}




$props= $modx->runSnippet( 'CatalogFilterProps', array( 'id'=>$id, 'onlyid'=>true, 'md5props'=>false ) );


$znachs= array();

if( $props )
{
	foreach( $props AS $row )
	{
		if( $row['type'] == 2 || $row['type'] == 4 )
		{
			$znachs[ $row['cfid'] ]= true;
		}
	}
}




if( ! empty( $props ) )
{
	$pr= explode( "/", $modx->catalogFilterListX );
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
					$propssel[ $prid ][0]= intval( $prvals[0] );
					$propssel[ $prid ][1]= intval( $prvals[1] );
				}else{
					foreach( $prvals AS $prval ) $propssel[ $prid ][ $prval ]= true;
				}
			}
		}
	}
	
	
	
	
	
	
	foreach( $props AS $row )
	{
		if( $row[ 'type' ] == 2 )
		{
			$print .= '<div class="blf_prm catalogfilter__param">
					<div class="blfp_tit"><span class="famicon">&nbsp;&nbsp;</span><span class="nm">'. $row[ 'name' ] .''.( $row[ 'ed' ] ? '<span><nobr>, '.$row[ 'ed' ].'</nobr></span>' : '' ).'<span></div>';
			$print .= '<div class="blfp_itms">';
				$print .= '<div class="blfp_cena blfp_cenaot">
					<div class="blfp_cena_tit">от</div>
					<div class="blfp_cena_val"><input type="text" class="catalogfilter__val catalogfilter__val_'. $row[ 'cfid' ] .'" data-tp="'. $row[ 'type' ] .'" data-nm="'. $row[ 'cfid' ] .'" value="'. $propssel[ $row[ 'cfid' ] ][0] .'" /></div>
				</div>';
				$print .= '<div class="blfp_cena blfp_cenado">
					<div class="blfp_cena_tit">до</div>
					<div class="blfp_cena_val"><input type="text" class="catalogfilter__val catalogfilter__val_'. $row[ 'cfid' ] .'" data-tp="'. $row[ 'type' ] .'" data-nm="'. $row[ 'cfid' ] .'" value="'. $propssel[ $row[ 'cfid' ] ][1] .'" /></div>
				</div>';
			$print .= '<div class="clr">&nbsp;</div></div></div>';
			
			
		}elseif( $row[ 'type' ] == 4 ){
			$print .= '<div class="blf_prm catalogfilter__param">
					<div class="blfp_tit"><span class="famicon">&nbsp;&nbsp;</span><span class="nm">'. $row[ 'name' ] .''.( $row[ 'ed' ] ? '<span><nobr>, '.$row[ 'ed' ].'</nobr></span>' : '' ).'<span></div>';
			$print .= '<div class="blfp_itms">';
				$print .= '<div class="blfp_znach blfp_znachot">
					<div class="blfp_znach_tit">от</div>
					<div class="blfp_znach_val"><input type="text" class="catalogfilter__val catalogfilter__val_'. $row[ 'cfid' ] .'" data-tp="'. $row[ 'type' ] .'" data-nm="'. $row[ 'cfid' ] .'" value="'. $propssel[ $row[ 'cfid' ] ][0] .'" /></div>
				</div>';
				$print .= '<div class="blfp_znach blfp_znachdo">
					<div class="blfp_znach_tit">до</div>
					<div class="blfp_znach_val"><input type="text" class="catalogfilter__val catalogfilter__val_'. $row[ 'cfid' ] .'" data-tp="'. $row[ 'type' ] .'" data-nm="'. $row[ 'cfid' ] .'" value="'. $propssel[ $row[ 'cfid' ] ][1] .'" /></div>
				</div>';
			$print .= '<div class="clr">&nbsp;</div></div></div>';
		
			
		}else{
			if( count( $propsvalues[ $row[ 'cfid' ] ] ) >= 2 )
			{
				$print .= '<div class="blf_prm catalogfilter__param">
					<div class="blfp_tit"><span class="famicon">&nbsp;&nbsp;</span><span class="nm">'. $row[ 'name' ] .''.( $row[ 'ed' ] ? '<span><nobr>, '.$row[ 'ed' ].'</nobr></span>' : '' ).'<span></div>';
				$print .= '<div class="blfp_itms">';
				foreach( $propsvalues[ $row[ 'cfid' ] ] AS $propid => $propval )
				{
					$print .= '<div class="blfp_itm '.( $propssel[ $row[ 'cfid' ] ][ $propval[ 'cfvid' ] ] ? 'blfp_itm_active' : '' ).' catalogfilter__val catalogfilter__val_'. $row[ 'cfid' ] .'" data-tp="'. $row[ 'type' ] .'" data-nm="'. $row[ 'cfid' ] .'" data-val="'. $propval[ 'cfvid' ] .'" data-sel="'.( $propssel[ $row[ 'cfid' ] ][ $propval[ 'cfvid' ] ] ? 'da' : 'net' ).'"><nobr>'. $propval[ 'value' ] .''.( $row[ 'ed' ] ? ' '.$row[ 'ed' ] : '' ).'</nobr></div>';
				}
				$print .= '</div><div class="clr">&nbsp;</div></div>';
			}
		}
	}
	
	
	
	if( ! empty( $print ) )
	{
		$print= '<div class="blockfilter wbl catalogfilter" data-pageid="'. $id .'" data-url="'. $modx->makeUrl( $id ) .'" data-pagenum="'. $page .'"><div class="blf_tit">Задайте параметры</div>'. $print;
		$print .= '<div class="clr">&nbsp;</div>';
		$print .= '<div><div><button class="blfp_button">Подобрать</button></div></div>';
		$print .= '</div>';
	}
}



return $print;
?>
