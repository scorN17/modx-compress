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
