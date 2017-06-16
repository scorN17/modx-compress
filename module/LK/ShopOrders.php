<?php
/**
 * LK_ShopOrders
 *
 * История заказов в магазине
 *
 * @version 1.6
 * @date    13.06.2017
 *
 */
	
	
$webuserinfo= $_SESSION['webuserinfo']['info'];
$userid= $webuserinfo['id'];


/*
	1   - Неоформленный заказ
	5   - Заказ оформлен - готов к оплате
	10  - Производство
	20  - Отправка
	30  - Доставка
	40  - Ожидание заказчика в ПВЗ
	50  - Доставка по адресу
	60  - Ожидание оплаты
	70  - Завершено
	100 - Возврат товара
	200 - Отмена заказа заказчиком
	210 - Отмена заказа администратором
*/



$code= isset($_GET['c']) ? $modx->db->escape($_GET['c']) : false;



$print .= '<div class="shophistory">';

$rr= $modx->db->query( "SELECT ord.*, ml.mail FROM ". $modx->getFullTableName( '_shop_order' ) ." AS ord
		LEFT JOIN ". $modx->getFullTableName( '_shop_mail' ) ." AS ml ON ml.code=ord.code
			WHERE ord.userid={$userid} ".($code ? "AND ord.code='{$code}'" : "")." ORDER BY ord.id DESC" );

if( $rr && $modx->db->getRecordCount( $rr ) )
{
	
	
	
	
	
	
	if($code)
	{
		$row= $modx->db->getRow( $rr );
		
		$print .= '<div class="_LK_buttons"><div class="lkb_b"><a href="[~72~]">Все заказы</a></div><br></div>';
		
		$print .= '<div class="shop_order_mail">';
		$print .= $row['mail'];
		$print .= '</div>';
		
		
		
		
		
		
		
		
	}else{
		$print .= '<div class="shphstr_title">
								<div class="sphr_tit sphrt_podr">&nbsp;</div>
								<div class="sphr_tit sphrt_code">№ заказа</div>
								<div class="sphr_tit sphrt_allsumm">Сумма, руб.</div>
								<div class="sphr_tit sphrt_date">Дата заказа</div>
								<div class="sphr_tit sphrt_adres">Адрес</div>
								<div class="sphr_tit sphrt_fio">Имя</div>
								<div class="clr">&nbsp;</div>
							</div>';

		$oo= 0;
		while( $row= $modx->db->getRow( $rr ) )
		{
			$oo++;
			$print .= '<div class="shphstr_item '.($oo%2!=0?'shphstr_item_alt':'').'">
							<div class="sphri_inf sphri_podr"><a href="[~72~]?c='.$row['code'].'">Подробнее</a></div>
							<div class="sphri_inf sphri_code">'. $row[ 'code' ] .'</div>
							<div class="sphri_inf sphri_allsumm"><nobr><span class="price">'. $modx->runSnippet( 'Price', array( 'price' => $row[ 'itogo' ] ) ) .'</span> <span class="ruble">руб.</span></nobr></div>
							<div class="sphri_inf sphri_date">'. date( 'd.m.Y', $row[ 'checkout' ] ) .'<br />'. date( 'H:i', $row[ 'checkout' ] ) .'</div>
							<div class="sphri_inf sphri_adres">'. $row[ 'city' ] .'<br />'. $row[ 'address' ] .'</div>
							<div class="sphri_inf sphri_fio">'. $row[ 'fio' ] .'</div>
							<div class="clr">&nbsp;</div>
					</div>';
		}
	}
	
	
	
}

$print .= '</div>';

return $print;
