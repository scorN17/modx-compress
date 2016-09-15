//Redirect
//15.09.2016
//v10
//Events: OnWebPageInit, OnWebPagePrerender
//-----------------------------------------------------------------
$redirect= array(
	1 => array(
		'/index.html' => '/',
		'/index.php' => '/',
		
		"=/^(.*)\/category\/(.*)$/" => '${1}/${2}',
		"+/^(.*)\/item\/(.*)$/" => 2,
	)
);
//-----------------------------------------------------------------
//
//
//
//
//
//-----------------------------------------------------------------
$url= $_SERVER['REQUEST_URI'];
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
			preg_match($key, $url, $matches);
			if(is_array($matches) && count($matches))
			{
				$url2= preg_replace($key, $row, $url);
			}
		}
		
		if(substr($key,0,1)=='+')
		{
			$key= substr($key,1);
			preg_match($key, $url, $matches);
			if(is_array($matches) && count($matches))
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
	header('location: '.$url2);
	exit();
}
