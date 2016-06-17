<?php
//v03
//PageContacts - snippet
//17.06.2016
//=====================================================================================
$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( 'page_contacts' )." ORDER BY block, `index`" );
if( $rr && mysql_num_rows( $rr ) > 0 )
{
	$block= 0;
	while( $row= mysql_fetch_assoc( $rr ) )
	{
		if( $row[ 'block' ] && $row[ 'block' ] != $block )
		{
			$block= $row[ 'block' ];
			$print .= '</div>'; // закрываем предыдущий BLOCK
			if( $block%2!=0 ) $print .= '<div class="clr">&nbsp;</div>';
			$print .= '<div class="cont_block cont_block_'. $block .' '.( $block%2==0 ? 'cont_block_alt' : '' ).'">'; // открываем новый BLOCK
		}
		
		if( $row[ 'type' ] != 3 )
		{
			$print .= '<div class="cont_1">'. $row[ 'left' ] .'</div>';
			
			$print .= '<div class="cont_2 '.( $row[ 'br' ] == '1' ? 'cont_br' : '' ).'" '.( $row[ 'type' ] == '4' ? 'style="font-size: 24px;"' : '' ).' '.( $row[ 'type' ] == '6' ? 'style="font-weight:bold;"' : '' ).'>';
			
			if( $row[ 'type' ] == 1 || $row[ 'type' ] == 4 || $row[ 'type' ] == 6 ) $print .= $row[ 'right' ];
			if( $row[ 'type' ] == 2 ) $print .= '<a class="as1 padd2" target="_blank" href="mailto:'. $row[ 'right' ] .'">'. $row[ 'right' ] .'</a>';
			if( $row[ 'type' ] == 5 ) $print .= '<h3>'. $row[ 'right' ] .'</h3>';
			
			$print .= '</div><div class="clr">&nbsp;</div>';
			
		}else{
			$print .= '<br /><div class="map">'. $row[ 'right' ] .'</div>';
			if( $row[ 'br' ] == '1' ) $print .= '<br /><br />';
		}
	}
	$print .= '</div><div class="clr">&nbsp;</div>'; // закрываем последний BLOCK
}
return $print;
?>
