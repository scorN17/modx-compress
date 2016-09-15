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
		"=/^(.*)\/item\/(.*)$/" => '${1}/${2}',
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

if($redirect[1][$url])
{
	header('HTTP/1.1 301 Moved Permanently');
	header('location: '.$redirect[1][$url]);
	exit();
}else{
	foreach($redirect[1] AS $key => $row)
	{
		if(substr($key,0,1)=='=')
		{
			$key= substr($key,1);
			preg_match($key, $url, $matches);
			if(is_array($matches) && count($matches))
			{
				$url= preg_replace($key, $row, $url);
				header('HTTP/1.1 301 Moved Permanently');
				header('location: '.$url);
				exit();
			}
		}
	}
}
