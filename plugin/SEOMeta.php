/**
 * SEOMeta
 *
 * СЕО Мета
 *
 * @version     5.0
 * @date        16.06.2017
 * @internal    @events OnWebPagePrerender
 *
 * <plugin_seo_meta />
 *
 *
 * 
 *
 *
 */

$html= $modx->documentOutput;

if(true || isset($_GET['test']))
{
	$base= MODX_SITE_URL;
	
	$siteurl= rtrim(MODX_SITE_URL, "/");
	$url= ( ! empty($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : '/');
	
	$get= strrpos($_SERVER['REQUEST_URI'], '?');
	$get= $get!==false ? substr($_SERVER['REQUEST_URI'], $get) : '';
	
	$canonical= $siteurl . $url . $get;
	
	if($base) $seometa .= '<base href="'.$base.'" />';
	if($canonical) $seometa .= "\n\t". '<link rel="canonical" href="'.$canonical.'" />';
	
	$html= str_replace("<plugin_seo_meta />", $seometa, $html);
}

$modx->documentOutput= $html;
