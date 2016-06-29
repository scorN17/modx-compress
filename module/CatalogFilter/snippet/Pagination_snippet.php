<?php
//v005
//============================================================
if( $docs_items_count > 0 )
{
	$pages_count= ceil( $docs_items_count / $MaxItemsInPage );
	
	if( $pages_count > 1 )
	{
		$pages .= '<div class="catalog_pages_tit">Страницы:</div>';
		$pages .= '<div class="catalog_pages">';
		
		$pp_prev= $active_page - 1; if( $pp_prev <= 1 ) $pp_prev= 0;
		if( $pp_prev ) $pages .= '<a class="cp_item" href="'. $modx->makeUrl( $myid ) . ( $filterprms ? 'x/'.$filterprms.'/' : '' ) .'page_'. $pp_prev .'/"> < </a>';
		
		$visible_ot= $active_page - 5; if( $visible_ot < 1 ) $visible_ot= 1;
		$visible_do= $active_page + 5; if( $visible_do > $pages_count ) $visible_do= $pages_count;
		
		for( $pp= 1; $pp <= $pages_count; $pp++ )
		{
			if( $pp >= 3 && $pp < $visible_ot )
			{
				if( ! $first )
				{
					$pages .= '<div class="cp_troetochie">...</div>';
					$first= true;	
				}
				continue 1;
			}
			if( $pp <= $pages_count - 2 && $pp > $visible_do )
			{
				if( ! $second )
				{
					$pages .= '<div class="cp_troetochie">...</div>';
					$second= true;	
				}
				continue 1;
			}
				
			$pages .= '<a class="cp_item '.( $pp == $active_page ? 'active' : '' ).'" href="'. $modx->makeUrl( $myid ) . ( $filterprms ? 'x/'.$filterprms.'/' : '' ) . ( $pp > 1 ? 'page_'. $pp .'/' : '' ) .'">'. $pp .'</a>';
		}
		
		$pp_next= $active_page + 1; if( $pp_next >= $pages_count ) $pp_next= 0;
		if( $pp_next ) $pages .= '<a class="cp_item" href="'. $modx->makeUrl( $myid ) . ( $filterprms ? 'x/'.$filterprms.'/' : '' ) .'page_'. $pp_next .'/"> > </a>';
		
		$pages .= '<div class="clr">&nbsp;</div>';
		$pages .= '</div>';
	}
}

return $pages;
?>
