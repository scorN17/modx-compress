<?php
/**
 * Typograph
 *
 * Типограф
 *
 * @version     1.1
 * @date        05.04.2017
 *
 *
 *
 */
$modx->db->query("CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName('typograph')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` text NOT NULL,
  `to` text NOT NULL,
  `hash` varchar(63) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

$t= trim($t);
if( ! $t) return;

$text= $t;

$hash= md5($t);
$typografResponse= $modx->db->getValue("SELECT `to` FROM ".$modx->getFullTableName('typograph')." WHERE `hash`='{$hash}' LIMIT 1");

if(true && ! $typografResponse)
{
	$text = str_replace ('&', '&amp;', $text);
	$text = str_replace ('<', '&lt;', $text);
	$text = str_replace ('>', '&gt;', $text);

	$SOAPBody = '<?xml version="1.0" encoding="UTF-8"?>
	<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	  <soap:Body>
		<ProcessText xmlns="http://typograf.artlebedev.ru/webservices/">
		  <text>' . $text . '</text>
		  <entityType>4</entityType>
		  <useBr>0</useBr>
		  <useP>0</useP>
		  <maxNobr>3</maxNobr>
		  <quotA>laquo raquo</quotA>
		  <quotB>bdquo ldquo</quotB>
		</ProcessText>
	  </soap:Body>
	</soap:Envelope>';

	$host = 'typograf.artlebedev.ru';
	$SOAPRequest = 'POST /webservices/typograf.asmx HTTP/1.1
Host: typograf.artlebedev.ru
Content-Type: text/xml
Content-Length: ' . strlen ($SOAPBody). '
SOAPAction: "http://typograf.artlebedev.ru/webservices/ProcessText"

'.
		$SOAPBody;

	$remoteTypograf = fsockopen ($host, 80);
	fwrite ($remoteTypograf, $SOAPRequest);
	$typografResponse = '';
	while (!feof ($remoteTypograf))
	{
		$typografResponse .= fread ($remoteTypograf, 8192);
	}
	fclose ($remoteTypograf);

	$startsAt = strpos ($typografResponse, '<ProcessTextResult>') + 19;
	$endsAt = strpos ($typografResponse, '</ProcessTextResult>');
	$typografResponse = substr ($typografResponse, $startsAt, $endsAt - $startsAt - 1);

	$typografResponse = str_replace ('&amp;', '&', $typografResponse);
	$typografResponse = str_replace ('&lt;', '<', $typografResponse);
	$typografResponse = str_replace ('&gt;', '>', $typografResponse);
	
	if($typografResponse)
	{
		$t_q= $modx->db->escape($t);
		$typografResponse_q= $modx->db->escape($typografResponse);
		$modx->db->getValue("INSERT INTO ".$modx->getFullTableName('typograph')." SET `from`='{$t_q}', `to`='{$typografResponse_q}', `hash`='{$hash}'");
	}
}

if( ! $typografResponse) $typografResponse= $t;

return $typografResponse;
