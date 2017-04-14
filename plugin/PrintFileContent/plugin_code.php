<?php
/**
 * PrintFileContent
 *
 * Выводит содержимое файла
 *
 * @category    plugin
 * @version     1.0
 * @date        11.04.2017
 * @internal    @events OnWebPagePrerender
 *
 * [:path_to_file/filename.ext:]
 *
 */
$_= $modx->documentOutput;

preg_match_all("/\[:(.*):\]/U", $_, $matches);
if(true && $matches[0][0])
{
	foreach($matches[1] AS $key => $row)
	{
		$filecontent= '';
		$handle= fopen(MODX_BASE_PATH.$row, 'rb');
		if($handle)
		{
			while( ! feof($handle)) $filecontent .= fread($handle, 1024*64);
			fclose($handle);
		}
		
		$_= str_replace($matches[0][$key], $filecontent, $_);
	}
}

$modx->documentOutput= $_;
