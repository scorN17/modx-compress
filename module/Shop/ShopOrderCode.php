<?php
$result= false;

$user= $modx->db->escape('s'.session_id());
if( ! $fields) $fields= 'code';
$fieldsqq= $modx->db->escape($fields);
$rr= $modx->db->query("SELECT {$fieldsqq} FROM ".$modx->getFullTableName('_shop_order')." WHERE user='{$user}' AND status='1' ORDER BY id LIMIT 1");
if($rr && $modx->db->getRecordCount($rr))
{
	$row= $modx->db->getRow($rr);
	if(strpos($fields,',')===false && strpos($fields,'*')===false) $result= $row[trim($fields,"`")];
	else $result= $row;
}

if($result !== false) return $result;

if($create)
{
	// LK ---------------------------------------------
	$webuserinfo= $_SESSION['webuserinfo']['info'];
	$lkqq= "";
	if($webuserinfo['fio'])     $lkqq .= ",fio=       '{$webuserinfo[fio]}'";
	if($webuserinfo['phone'])   $lkqq .= ",phone=     '{$webuserinfo[phone]}'";
	if($webuserinfo['email'])   $lkqq .= ",email=     '{$webuserinfo[email]}'";
	if($webuserinfo['city'])    $lkqq .= ",city=      '{$webuserinfo[city]}'";
	if($webuserinfo['address']) $lkqq .= ",address=   '{$webuserinfo[address]}'";
	// LK ---------------------------------------------
	
	
	
$modx->db->query("CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName('_shop_order')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `code` varchar(15) NOT NULL,
  `itogo` decimal(10,2) NOT NULL,
  `fio` varchar(63) NOT NULL,
  `email` varchar(63) NOT NULL,
  `phone` varchar(31) NOT NULL,
  `city` varchar(63) NOT NULL,
  `address` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `logs` text NOT NULL,
  `checkout` bigint(20) NOT NULL,
  `user` varchar(63) NOT NULL,
  `secret` varchar(63) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
	
$modx->db->query("CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName('_shop_mail')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(15) NOT NULL,
  `mail` text NOT NULL,
  `result` set('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
	
$modx->db->query("CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName('_shop_items')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(15) NOT NULL,
  `id_item` int(11) NOT NULL,
  `name` varchar(63) NOT NULL,
  `art` varchar(63) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `count` int(11) NOT NULL,
  `ed` varchar(15) NOT NULL,
  `prms` text NOT NULL,
  `uniq` varchar(63) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
	
	
	
	
	$code= 'w'.(date('Y')-2016).date('md');
	$num= 0;
	$rr= $modx->db->query("SELECT COUNT(id) AS cc FROM ".$modx->getFullTableName('_shop_order')." WHERE code LIKE '{$code}%'");
	if($rr && $modx->db->getRecordCount($rr)) $num= $modx->db->getValue($rr);
	do{
		$num++;
		$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('_shop_order')." WHERE code='{$code}{$num}' LIMIT 1");
	}while($rr && $modx->db->getRecordCount($rr));
	$code .= $num;
	$modx->db->query("INSERT INTO ".$modx->getFullTableName('_shop_order')." SET status='1', code='{$code}', logs='".date('d.m.Y, H:i')." | Корзина создана', user='{$user}'". $lkqq);
	
	$result= $modx->runSnippet('ShopOrderCode',array('fields'=>$fields));
	
}else $result= false;

return $result;
