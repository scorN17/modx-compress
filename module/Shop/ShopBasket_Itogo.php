<?php
$orderinfo= $modx->runSnippet('ShopOrderCode',array('fields'=>'*'));

$pp .= '<div class="shb_itogo">
		<div class="shbi_itm shbi_itogosum">
			<div class="shbii_val"><span class="price font2">'.$modx->runSnippet('Price',array('price'=>$orderinfo['itogo'])).'</span> <span class="ruble">руб.</span><div class="svgloading"></div></div>
			<div class="shbii_lab">Сумма заказа</div><br>
		</div>

		<div class="shbi_errors"></div>

		<div class="shbi_checkout">
			<button class="shop_basket_checkout font2" type="button" data-code="'.$orderinfo['code'].'" data-itogo="'.$orderinfo['itogo'].'">Оформить заказ</button>
			<div class="svgloading"></div><br>
		</div>
	</div>';

return $pp;
