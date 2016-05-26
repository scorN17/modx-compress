$html= $modx->documentOutput;
if( true )
{
	preg_match_all( '/<img(.*)class="(.*)highslidezoom(.*)"(.*)>/imU', $html, $result );
	
	if( $result )
	{
		foreach( $result[ 0 ] AS $row )
		{
			preg_match_all( '/src="(.*)"/imU', $row, $src );
			$img= $modx->runSnippet( 'ImgCrop6', array( 'img'=>$src[ 1 ][ 0 ], 'wm'=>true ) );
			$row2= str_replace( $src[ 1 ][ 0 ], $img, $row );
			if( $src[ 1 ] ) $html= str_replace( $row, '<a class="highslide" onclick="return hs.expand(this)" href="'. $img .'">'. $row2 .'</a>', $html );
		}
	}
}
$modx->documentOutput= $html;
