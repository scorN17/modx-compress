<?php
if(isset($_GET['c']) && isset($_GET['s']))
{
	$code= $modx->db->escape($_GET['c']);
	$secret= $modx->db->escape($_GET['s']);
	
	$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_shop_order')." WHERE code='{$code}' AND secret='{$secret}' LIMIT 1");
	if($rr && $modx->db->getRecordCount($rr))
	{
		$row= $modx->db->getRow($rr);
		
		$pp .= '<div class="shop_checkout_ok">
			<div class="shck_ico">[[ico?&n=`plane`]]</div>';
			
		$pp .= '<div class="shck_spas">Спасибо,</div>';
			
		if($row['fio']) $pp .= '<div class="shck_fio">'.$row['fio'].'</div>';
		
		$pp .= '<div class="shck_code">заказ <span class="font2">№'.substr($row['code'],1).'</span></div>';
		
		$pp .= '<div class="shck_ok">&mdash; успешно оформлен!</div>';
		
		$pp .= '</div>';
		
		
		return $pp;
	}
}
	
	
	
	
	
	
	
	
	
	
// -----------------------------------------------------------------------------------

	
	
	
	
	
	
	

$orderinfo= $modx->runSnippet('ShopOrderCode',array('fields'=>'*'));

if( ! $orderinfo)
{
	return '<p>В Вашей корзине пусто!</p>';	
}
	
$pp .= '<div class="shop_basket_box">';

$pp .= '<div class="shop_basket_items">';
$pp .= $modx->runSnippet('ShopBasket_Items');
$pp .= '</div><!--.shop_basket_items-->';

$pp .= '<div class="shop_basket_itogo">';
$pp .= $modx->runSnippet('ShopBasket_Itogo');
$pp .= '</div><!--.shop_basket_itogo-->';

$pp .= '<div class="shop_basket_form">
			<form action="[~[*id*]~]" method="post">
				<div class="chbf_inp">
					<div class="chbf_lab font2">Фамилия Имя</div>
					<div class="chbf_val"><input type="text" name="fio" value="'.htmlspecialchars($orderinfo['fio']).'" /></div>
				</div>

				<div class="chbf_inp">
					<div class="chbf_lab font2">Телефон<span>*<span></div>
					<div class="chbf_val"><input class="phonemask" type="text" name="phone" value="'.htmlspecialchars($orderinfo['phone']).'" /></div>
				</div>

				<div class="chbf_inp">
					<div class="chbf_lab font2">E-mail<span>*<span></div>
					<div class="chbf_val"><input type="text" name="email" value="'.htmlspecialchars($orderinfo['email']).'" /></div>
				</div>

				<div class="chbf_inp">
					<div class="chbf_lab font2">Город</div>
					<div class="chbf_val"><input type="text" name="city" value="'.htmlspecialchars($orderinfo['city']).'" /></div>
				</div>

				<div class="chbf_inp">
					<div class="chbf_lab font2">Адрес</div>
					<div class="chbf_val"><input type="text" name="address" value="'.htmlspecialchars($orderinfo['address']).'" /></div>
				</div>

				<div class="chbf_inp">
					<div class="chbf_lab font2">Комментарий</div>
					<div class="chbf_val"><textarea name="message">'.htmlspecialchars($orderinfo['message']).'</textarea></div>
				</div>
			</form>
		</div><!--.shop_basket_form-->';
	
	
$pp .= '<br></div><!--.shop_basket_box-->';




return $pp;
