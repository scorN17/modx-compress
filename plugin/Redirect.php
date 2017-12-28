/**
 * Redirect
 *
 * Редиректы
 *
 * @version   11.1
 * @date 28.12.2017
 * @internal    @events OnWebPageInit
 *
 *
 *
 *
 *
 */

//-----------------------------------------------------------------
$redirect= array(
	1 => array(
		'/index.html' => '/',
		'/index.php' => '/',
	)
);
//-----------------------------------------------------------------
//
//
//
//
//
//-----------------------------------------------------------------

$e= &$modx->Event;
//if($modx->isFrontend()) $modx->logEvent(5, 1, '', microtime() .' -- '. $e->name);
if($e->name != 'OnWebPageInit') return;

$site= rtrim(MODX_SITE_URL,DIRECTORY_SEPARATOR);
$base= rtrim(MODX_BASE_URL,DIRECTORY_SEPARATOR);
if($base=='/') $base= '';
$url= $_SERVER['REQUEST_URI'];
if($base) $url= str_replace($base, '', $url);
$url2= false;

if($redirect[1][$url])
{
	$url2= $redirect[1][$url];
}else{
	foreach($redirect[1] AS $key => $row)
	{
		if(substr($key,0,1)=='=')
		{
			$key= substr($key,1);
			if(strpos($url, $key)!==false)
			{
				$url2= str_replace($key, $row, $url);
			}
		}
		
		if(substr($key,0,1)=='+')
		{
			$key= substr($key,1);
			$key2= preg_quote($key,"/");
			if(preg_match($key2, $url, $matches)===1)
			{
				$alias= mysql_real_escape_string($matches[$row]);
				$rr= mysql_query("SELECT id FROM ".$modx->getFullTableName('site_content')." WHERE alias='{$alias}' LIMIT 1");
				if($rr && mysql_num_rows($rr)==1)
				{
					$url2= $modx->makeUrl(mysql_result($rr, 0, 'id'));
				}
			}
		}
	}
}
if($url2)
{
	header('HTTP/1.1 301 Moved Permanently');
	header('location: '.$site.$base.$url2);
	exit();
}
