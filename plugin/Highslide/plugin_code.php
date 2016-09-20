//v002
//14.09.2016
//Highslide
//Event: OnWebPagePrerender
//Snippet: ImgCrop72
//<a class="highslide" onclick="return hs.expand(this)" href><img class="zoom" /></a>
//======================================================================================
$html= $modx->documentOutput;
if(true || isset($_GET['test']))
{
	preg_match_all('/<img(.*)class="(.*)zoom(.*)"(.*)>/imU', $html, $result);
	
	if( $result )
	{
		foreach($result[0] AS $key => $row)
		{
			if(strpos($result[2][$key], 'fill') !== false || strpos($result[3][$key], 'fill') !== false) $fill= true; else $fill= false;
			if(strpos($result[2][$key], 'backgr') !== false || strpos($result[3][$key], 'backgr') !== false) $backgr= true; else $backgr= false;
			preg_match_all('/src="(.*)"/imU', $row, $src);
			$img= $modx->runSnippet('ImgCrop71', array('img'=>$src[1][0]));
			$img_mini= $modx->runSnippet('ImgCrop72', array('img'=>$src[1][0], 'w'=>300, 'h'=>200, 'backgr'=>$backgr, 'fill'=>$fill));
			$row2= str_replace( $src[ 1 ][ 0 ], $img_mini, $row );
			$row2= preg_replace("/(width|height)=\"(.*)\"/imU", "", $row2);
			if($src[1]) $html= str_replace($row, '<a class="highslide" onclick="return hs.expand(this)" href="'.$img.'">'.$row2.'</a>', $html);
		}
	}
}
$modx->documentOutput= $html;
