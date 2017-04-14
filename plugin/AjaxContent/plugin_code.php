/**
 * AjaxContent
 *
 * AJAX Постзагрузка
 *
 * @category    plugin
 * @version     2.0
 * @date        05.04.2017
 * @internal    @events OnWebPagePrerender
 *
 *
 *
 */
$_= $modx->documentOutput;

$ii= intval($_GET['ajaxcontentii']);
$kk= intval($_GET['ajaxcontentkk']);

if($ii && $kk)
{
	preg_match_all("/<!-- ajaxcontent_{$ii}_{$kk}_ -->(.*)<!-- _ajaxcontent_{$ii}_{$kk} -->/s", $_, $content);
	$_= $content[1][0];
}

for($xx=($ii?$ii:1); $xx<=($ii?$ii:99); $xx++)
{
	for($yy=($kk?$kk+1:1); $yy<=99; $yy++)
	{
		if(strpos($_, "ajaxcontent_{$xx}_{$yy}_") === false) break;
		$_= preg_replace("/<!-- ajaxcontent_{$xx}_{$yy}_ -->(.*)<!-- _ajaxcontent_{$xx}_{$yy} -->/s",
						 '<div style="height:1000px;" class="ajaxcontent" id="ajaxcontent_'.$xx.'_'.$yy.'" data-ii="'.$xx.'" data-kk="'.$yy.'" data-page="'.$modx->makeUrl($modx->documentIdentifier).'"></div>', $_);
	}
}

$modx->documentOutput= $_;
