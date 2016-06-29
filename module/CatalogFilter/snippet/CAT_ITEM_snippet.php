<?php
if( $type == 'category' )
{
	$result .= '<div class="catcat '.( $last ? 'catcat_last' : '' ).'"><a href="'. $modx->makeUrl( $row[ 'id' ] ) .'">
				<div class="catc_img"><img itemprop="image" src="'. $modx->runSnippet( 'ImgCrop6', array( 'img'=>$row[ 'cat_img' ], 'w'=>( $indexcatalog ? 255 : 235 ), 'h'=>235, 'backgr'=>true, 'rgba'=>'1:1' ) ) .'" alt="" /><div>&nbsp;</div></div>';
	
	//if( $row[ 'lentochka' ] ) $result .= '<div class="lenta lenta_'. $row[ 'lentochka' ] .'">&nbsp;</div>';
	
	$result .= '<div class="catc_tit" itemprop="name"><div>'. $row[ 'pagetitle' ] .'</div></div>
			</a></div>';
	
	if( $last ) $result .= '<div class="clr">&nbsp;</div>';
	
	
	
//=============================================================================
	
	
	
}elseif( $type == 'item' ){
	$img_mini= $modx->runSnippet( 'ImgCrop6', array( 'img'=>$row['cat_img'], 'w'=>164, 'h'=>164, 'backgr'=>true, 'rgba'=>'1:1' ) );
	
	$result .= '<div class="catitem '.( $last ? 'catitem_last' : '' ).'"><a href="'.$modx->makeUrl( $row['id'] ).'">';
	$result .= '<div class="ci_img"><img src="'. $img_mini .'" alt="'. $row['pagetitle'] .'" ></div>';
	$result .= '<div class="ci_tit"><span>'. $row['pagetitle'] .'</span></div>';
	$result .= '<div class="ci_price"><nobr><span class="price">'. $modx->runSnippet( 'Price', array( 'price'=>$row['cat_price'], 'round'=>0 ) ) .'</span> <span class="rubl">a</span></nobr></div>';
	$result .= '</a></div>';
	
	if( $last ) $result .= '<div class="clr">&nbsp;</div>';
	
	
//=============================================================================
	
	
	
}elseif( $type == 'itempage' ){
	$img_mini= $modx->runSnippet( 'ImgCrop6', array( 'img'=>$row['cat_img'], 'w'=>164, 'h'=>164, 'backgr'=>true, 'rgba'=>'1:1' ) );
	$img_big= $modx->runSnippet( 'ImgCrop6', array( 'img'=>$row['cat_img'], 'w'=>1111, 'h'=>1111 ) );
	
	$result .= '<div class="catitem '.( $last ? 'catitem_last' : '' ).'">';
	$result .= '<div class="ci_img"><a class="highslide" onclick="return hs.expand(this)" href="'. $img_big .'"><img src="'. $img_mini .'" alt="'. $row['pagetitle'] .'" ></a></div>';
	$result .= '<div class="ci_tit"><span>'. $row['pagetitle'] .'</span></div>';
	$result .= '<div class="ci_price"><nobr><span class="price">'. $modx->runSnippet( 'Price', array( 'price'=>$row['cat_price'], 'round'=>0 ) ) .'</span> <span class="rubl">a</span></nobr></div>';
	$result .= '</div>';
	
	//-----------------------------------------------------------------------------------
	
	$params_print= '';
	$props= $modx->runSnippet( 'CatalogFilterProps', array( 'id'=>$row[ 'id' ], 'parent'=>$row[ 'parent' ], 'onlyid'=>false, 'md5props'=>true ) );
	if( $props )
	{
		foreach( $props AS $prop )
		{
			if( ! empty( $prop[ 'value' ] ) )
			{
				$params_print .= '<div class="param">'. $prop[ 'name' ] .':</div><span>';
				if( $prop[ 'md5' ] )
				{
					$first= true;
					foreach( $prop[ 'md5' ] AS $vv )
					{
						if( ! $first ) $params_print .= ', ';
						$first= false;
						$params_print .= $vv .( $prop[ 'ed' ] ? ' '.$prop[ 'ed' ] : '' );
					}
				}
				$params_print .= '</span><div class="clr">&nbsp;</div>';
			}
		}
	}
	
	$params= explode( "||", $row[ 'har' ] );
	if( $params )
	{
		foreach( $params AS $param )
		{
			$param= explode( "::", $param );
			if( ! empty( $param[ 0 ] ) || ! empty( $param[ 1 ] ) )
				$params_print .= '<div class="param">'. $param[ 0 ] .':</div><span>'. $param[ 1 ] .'</span><div class="clr">&nbsp;</div>';
		}
	}
	
	$result .= '<div class="itempage_description">';
	$result .= '<div class="itemparameters">'. $params_print .'</div>';
	$result .= $row[ 'cat-opis' ];
	$result .= $row[ 'content' ];
	$result .= '</div>';
}

return $result;
?>
