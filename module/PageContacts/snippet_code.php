<?php
//v045
//PageContacts
//07.07.2016
//=====================================================================================
if( $blocks )
{
	$blocks_qq= "";
	$blocks= explode( ',', $blocks );
	foreach( $blocks AS $row )
	{
		$blocks_qq .= ( ! empty( $blocks_qq ) ? " OR " : "" ) ."block=". intval($row);
	}
}
$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( 'page_contacts' )." ".( $blocks_qq ? "WHERE ".$blocks_qq : "" )." ORDER BY block, `index`" );
if( $rr && mysql_num_rows( $rr ) > 0 )
{
	$block= 0;
	$ii= 0;
	while( $row= mysql_fetch_assoc( $rr ) )
	{
		if( $row[ 'block' ] && $row[ 'block' ] != $block )
		{
			if( $onlymain ) break;
			
			$ii++;
			if( $block ) $print .= '</div>'; // закрываем предыдущий BLOCK
			$block= $row[ 'block' ];
			if( $ii%2!=0 ) $print .= '<div class="clr">&nbsp;</div>';
			$print .= '<div class="cont_block cont_block_'. $block .' '.( $ii%2==0 ? 'cont_block_alt' : '' ).'">'; // открываем новый BLOCK
		}
		
		if( $onlyblock && ! $block ) continue;
		
		if( $array )
		{
			$result_array[ $row['block'] ][]= $row;
			continue;
		}
		
		if( $row[ 'type' ] == 7 )
		{
			$print .= '<div class="cont_img"><a class="highslide" onclick="return hs.expand(this)" href="'. $row[ 'right' ] .'"><img src="'.$modx->runSnippet( 'ImgCrop6', array( 'img'=>$row[ 'right' ], 'w'=>90, 'h'=>90, 'fill'=>true, 'ellipse'=>'max', 'degstep'=>15, 'dopimg'=>'template/images/kruzhok1.png', 'dopimg_xy'=>'0:0' ) ).'" alt="" /></a></div>';
			
		}elseif( $row[ 'type' ] == 8 ){
			
		}elseif( $row[ 'type' ] != 3 ){
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
	if( $block ) $print .= '</div><div class="clr">&nbsp;</div>'; // закрываем последний BLOCK
}
if( $array ) return $result_array;
return $print;
?>
