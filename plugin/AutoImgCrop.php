/**
 * AutoImgCrop
 *
 * Авто кроп картинок
 *
 * @version     1.0
 * @date        16.06.2017
 * @internal    @events OnWebPagePrerender
 *
 * Автоматически кропит картинки в контенте по атрибутам width="" и height=""
 * Ищет картинки <img width="" height="" /> в контейнере с классом class="content"
 * Обрамляет картинку скриптом увеличения
 *
 * Зависимости:
 * Snippet ImgCrop8
 * assets/lib/phpQuery/
 * jQuery Plugin fancyBox
 *
 *
 *
 *
 *
 *
 */

$html= $modx->documentOutput;

if(true)
{
	include_once('assets/lib/phpQuery/phpQuery.php');
	$pq= phpQuery::newDocument($html);
	$imgs= pq($pq)->find('.content img');
	foreach($imgs AS $img)
	{
		$width= trim(pq($img)->attr('width'));
		$width= preg_replace("/[^0-9]/",'',$width);
		$height= trim(pq($img)->attr('height'));
		$height= preg_replace("/[^0-9]/",'',$height);
		if( ! $width && ! $height) continue;
		
		$src= trim(pq($img)->attr('src'));
		
		$crop= $modx->runSnippet('ImgCrop8',array('img'=>$src, 'w'=>($width?$width:0), 'h'=>($height?$height:0), 'fill'=>($width && $height?true:false)));
		
		pq($img)->attr('src', $crop);
		
		if( ! pq($img)->parent()->is('a'))
			pq($img)->wrap('<a data-fancybox="content" href="'.$src.'" style="cursor:zoom-in;border:none;"></a>');
		
		$crop= true;
	}
	
	if($crop) $html= $pq->html();
}

$modx->documentOutput= $html;
