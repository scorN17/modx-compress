<?php
/**
 * LK_UserPanel
 *
 * Панель в шапке
 *
 * @version 1.6
 * @date    13.06.2017
 *
 */
	
//----------------------------------------------------------------------------------
$lk= 70;
$reg= 71;
$auth= 71;
$restorepassword= 73;
$agreed= 0;
$shophistory= 72;
//----------------------------------------------------------------------------------



$topage= ($topage ? intval($topage) : $modx->documentIdentifier);
$topage_url= $modx->makeUrl($topage);


$webuserinfo= $_SESSION['webuserinfo']['info'];


print '<div class="lk_auth">';
if($_SESSION['webuserinfo']['auth'])
{
	print '<a href="[~'.$lk.'~]"><div class="lka_ico">[[ico?&n=`user`]]</div>[[ico?&n=`galochka`]]<div class="lka_tit font2"><span>Кабинет</span></div></a>';
}else{
	print '<a href="[~'.$auth.'~]"><div class="lka_ico">[[ico?&n=`user`]]</div><div class="lka_tit font2"><span>Войти</span></div></a>';
}
print '</div><!--.lk_auth-->';
