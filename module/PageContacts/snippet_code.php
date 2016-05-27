<?php
$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( 'page_contacts' )." ORDER BY `index`" );
if( $rr && mysql_num_rows( $rr ) > 0 )
{
	while( $row= mysql_fetch_assoc( $rr ) )
	{
		if( $row[ 'type' ] != 3 )
		{
			$print .= '<div class="cont_1">'. $row[ 'left' ] .'</div>';
			
			$print .= '<div class="cont_2 '.( $row[ 'br' ] == '1' ? 'cont_br' : '' ).'" '.( $row[ 'type' ] == '4' ? 'style="font-size: 24px;"' : '' ).'>';
			
			if( $row[ 'type' ] == 1 || $row[ 'type' ] == 4 ) $print .= $row[ 'right' ];
			if( $row[ 'type' ] == 2 ) $print .= '<a class="as1 padd2" target="_blank" href="mailto:'. $row[ 'right' ] .'">'. $row[ 'right' ] .'</a>';
			if( $row[ 'type' ] == 5 ) $print .= '<h3>'. $row[ 'right' ] .'</h3>';
			
			$print .= '</div><div class="clr">&nbsp;</div>';
			
		}else{
			$print .= '<br /><div class="map">'. $row[ 'right' ] .'</div>';
		}
	}
}
return $print;
?>
