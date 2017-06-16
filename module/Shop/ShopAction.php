<?php
//----------------------------------------------------------------------------------
//SMTP - true OR false
$mailtype_smtp= true;

//КОМУ (через запятую)
$mailto= 'sergey.it@delta-ltd.ru';

//Видимые копии (через запятую)
$mailcc= false;

//Скрытые копии (через запятую)
$mailbcc= 'sergey.it@delta-ltd.ru';

//ОТ (если SMTP, то это поле - логин)
$mailfrom= 'feedback.noreply@yandex.ru';

//Пароль от почты (если SMTP)
$mailpassw= 'XSbKjp7ZjdoaesD_o_0j';

//Любимый киногерой: ----- //Секретный вопрос от почты
//Сервер SMTP (если SMTP)
$smtp= 'smtp.yandex.ru';

//Порт SMTP (если SMTP)
$smtpport= 465;

//SNIPPET Formochki //SNIPPET ShopAction //MODULE scorn_orders //SNIPPET LK_Restore
//----------------------------------------------------------------------------------

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


$selectFields= 'c.id,pagetitle,isfolder,content'; // Для DocLister
$tvList= 'article,descript,new,images,price,ed'; // Для DocLister

// ---------------------------------------------------------------------------------


$id= intval($id);
$count= intval($count);
$itogo= intval($itogo);
$size= $modx->db->escape($size);
$uniq= $modx->db->escape($uniq);
if($count<=0 || $count>999999 ) $count= 1;

$code= $modx->runSnippet('ShopOrderCode');

// Добавление позиции в корзину
if($act == 'add')
{
	$item= $modx->runSnippet('DocLister',array('api'=>true, 'idType'=>'documents', 'documents'=>$id, 'selectFields'=>$selectFields.',template,deleted,published', 'tvList'=>$tvList));
	$item= json_decode($item, true);
	$item= $item[$id];
	
	if($item['id'] && $item['template']==5 && $item['isfolder']==0 && $item['deleted']==0 && $item['published']==1)
	{
		$item['pagetitle']= $modx->db->escape($item['pagetitle']);
		$item['tv_article']= $modx->db->escape($item['tv_article']);
		$item['tv_price']= intval($item['tv_price']);
		$item['tv_ed']= $modx->db->escape($item['tv_ed']);
		
		if(true)
		{
			$uniq= md5($id);

			$stat= $modx->runSnippet('ShopAction',array('act'=>'stat', 'uniq'=>$uniq));
			
			if(!$code) $code= $modx->runSnippet('ShopOrderCode',array('create'=>true));

			if($stat==='n')
			{
				$prms= $modx->runSnippet('ShopAction',array('act'=>'prms', 'id'=>$id));
				$prms= $modx->db->escape($prms);
				
				$modx->db->query("INSERT INTO ".$modx->getFullTableName('_shop_items')." SET
							code='{$code}',
							id_item='{$id}',
							`name`='{$item[pagetitle]}',
							art='{$item[tv_article]}',
							price='{$item[tv_price]}',
							`count`='1',
							ed='{$item[tv_ed]}',
							prms='{$prms}',
							uniq='{$uniq}'
						");
			}else{
				$modx->runSnippet('ShopAction',array('act'=>'plus', 'uniq'=>$uniq, 'count'=>1));
			}

			$modx->runSnippet('ShopAction',array('act'=>'itogo'));
		}
	}
	$cc= $modx->runSnippet('ShopAction',array('act'=>'items_count'));
	return '{"cc":"'.$cc.'"}';
}

// Оформление заказа
if($act == 'checkout')
{
	$modx->runSnippet('ShopAction',array('act'=>'check_items_in_catalog'));
	
	$itogo_db= $modx->runSnippet('ShopOrderCode',array('fields'=>'itogo'));
	
	$fio= $modx->db->escape(trim($post['fio']));
	$email= $modx->db->escape(trim($post['email']));
	$phone= $modx->db->escape(trim($post['phone']));
	$city= $modx->db->escape(trim($post['city']));
	$address= $modx->db->escape(trim($post['address']));
	
	$message= trim($post['message']);
	$message= str_replace("\n\n", "\n", $message);
	$message_db= $modx->db->escape($message);
	$message_br= str_replace("\r", '', $message);
	$message_br= str_replace("\n", '<br>', $message_br);
	
	if( ! preg_match("/^[a-z0-9-_\.]{1,}@[a-z0-9-\.]{1,}\.[a-z]{2,10}$/", $email))
		$errors .= '<div>Введите корректный E-mail</div>';
	
	if( ! preg_match("/^\+7 \([0-9]{3}\) [0-9]{3}-[0-9]{4}$/", $phone))
		$errors .= '<div>Введите корректный телефон</div>';
	
	if($itogo != $itogo_db) $errors .= '<div>Сумма заказа изменилась &mdash; проверьте заказ</div>';
	
	if( ! $errors)
	{
		srand(time().rand());
		$secret= md5($code.'_'.$email.'_'.$itogo.'_'.time().'_'.rand());
		
		//$payment_link= $modx->makeUrl(50,'','','full').'?act=payment&c='.$code.'&s='.$secret;
		
		$subject= 'Заказ №'.substr($code,1).' — успешно оформлен!';
		
		$body= '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>'.$subject.'</title>';
		$body .= '</head><body>';
		
		$body .= '<h2>'.$subject.'</h2>
		<p>'.date('d.m.Y, H:i').'</p>
		<h3>Контактные данные</h3>
		<table border="1" width="100%" cellpadding="4" cellspacing="0" style="font-size:12px;">
			<tr><td width="40%" align="right" valign="middle">ФИО</td>
			<td align="left" valign="middle">'.$fio.'</td></tr>
	
			<tr><td align="right" valign="middle">Адрес электронной почты</td>
			<td align="left" valign="middle">'.$email.'</td></tr>
	
			<tr><td align="right" valign="middle">Телефон</td>
			<td align="left" valign="middle">'.$phone.'</td></tr>
	
			<tr><td align="right" valign="middle">Город</td>
			<td align="left" valign="middle">'.$city.'</td></tr>
	
			<tr><td align="right" valign="middle">Адрес</td>
			<td align="left" valign="middle">'.$address.'</td></tr>
	
			<tr><td align="right" valign="middle">Комментарии</td>
			<td align="left" valign="middle">'.$message_br.'</td></tr>
		</table>';
		
		//$body .= '<p><a style="font-size:140%;" target="_blank" href="'.$payment_link.'">Перейти к оплате банковской картой</a> (скидка 10%)</p>';
		
		$body .= '<h3>Заказ</h3>
		
		<table border="1" width="100%" cellpadding="4" cellspacing="0" style="font-size:12px;">
							<tr>
								<td align="center"><b>№</b></td>
								<td><b>Описание</b></td>
								<td align="right"><nobr><b>Цена</b></nobr></td>
								<td align="center"><nobr><b>Кол-во</b></nobr></td>
								<td align="right"><nobr><b>Сумма</b></nobr></td>
							</tr>';
		
		$items= $modx->runSnippet('ShopAction',array('act'=>'items'));
		foreach($items AS $num => $item)
		{
			$sum= $item['price'] * $item['count'];
		
			$prms_p= '';
			$prms= $modx->runSnippet('CatalogFilter_Props', array('id'=>$item['id_item'], 'values'=>true));
			if(is_array($prms) && count($prms))
			{
				foreach($prms AS $prm)
				{
					$cc= count($prm['values']);
					if(is_array($prm['values']) && $cc)
					{
						$prms_p.= '<p>&ndash; '.$prm['name'].':';
						foreach($prm['values'] AS $vls)
							$prms_p.= ' '.$vls.',';
						$prms_p.= '</p>';
					}
				}
			}
			
			$body .= '
								<tr>
									<td align="center" valign="middle">'.($num+1).'.</td>
									<td>
										<div>['.$item['id_item'].'] <a target="_blank" href="'.$modx->makeUrl($item['id_item'],'','','full').'">'.$item['name'].'</a></div>';
			if($item['art']) $body .= '<div>Арт. '.$item['art'].'</div>';
			if($prms_p) $body .= $prms_p;
			$body .= '</td>
									<td align="right" valign="middle"><nobr>'.$modx->runSnippet('Price',array('price'=>$item['price'])).' руб./'.$item['ed'].'</nobr></td>
									<td align="center" valign="middle"><nobr>'.$item['count'].' '.$item['ed'].'</nobr></td>
									<td align="right" valign="middle"><nobr><b>'.$modx->runSnippet('Price',array('price'=>$sum)).' руб.</b></nobr></td>
								</tr>';
		}
		
		$body .= '<tr>
							<td></td>
							<td></td>
							<td> </td>
							<td align="right" valign="middle"><b>Сумма заказа</b></td>
							<td align="right" valign="middle"><nobr><b>'.$modx->runSnippet('Price',array('price'=>$itogo)).' руб.</b></nobr></td>
						</tr>';
		$body .= '</table>';
		
		$body_qq= $modx->db->escape($body.'</body></html>');
		$modx->db->query("INSERT INTO ".$modx->getFullTableName('_shop_mail')." SET code='{$code}', mail='{$body_qq}'");
		
		//$body .= '<p><a style="font-size:140%;" target="_blank" href="'.$payment_link.'">Перейти к оплате банковской картой</a> (скидка 10%)</p>';
		
		$body .= '<div class="mailpodpis"><p>--<br>
					ООО «Сигма»<br>
					+7 (863) 226-30-56<br>
					E-mail: <a target="_blank" href="mailto:mail@sigma.com.ru">mail@sigma.com.ru</a><br>
					Сайт: <a target="_blank" href="http://www.sigma.com.ru/">www.sigma.com.ru</a></p></div>';
		$body .= '</body></html>';
		
		include_once(MODX_MANAGER_PATH.'includes/controls/class.phpmailer.php');
// ---------------------------------------------------------------------------------
		if($mailtype=='smtp' || $mailtype=='mail')
		{
			$phpmailer= new PHPMailer();
			if(false)
			{
				$phpmailer->SMTPDebug= 2;
				$phpmailer->Debugoutput = 'html';
			}
			if($mailtype_smtp)
			{
				$phpmailer->isSMTP();
				$phpmailer->Host= $smtp;
				$phpmailer->Port= $smtpport;
				$phpmailer->SMTPAuth= true;
				$phpmailer->SMTPSecure= 'ssl';
				$phpmailer->Username= $mailfrom;
				$phpmailer->Password= $mailpassw;
			}
			$phpmailer->CharSet= 'utf-8';
			$phpmailer->From= $mailfrom;
			$phpmailer->isHTML(true);
			$phpmailer->AltBody= 'To view the message, please use an HTML compatible email viewer!';
			$phpmailer->Subject= $subject;
			$phpmailer->msgHTML($body);
			
			$phpmailer->FromName= 'Sigma';
			$phpmailer->addReplyTo('mail@sigma.com.ru', 'Sigma');
			
			$phpmailer->addAddress($email);
			$mailcc= explode(',', $mailto); foreach($mailcc AS $row2) $phpmailer->addCC(trim($row2));
			
			$phpmailer_result= $phpmailer->send();
		}
// ---------------------------------------------------------------------------------
		
		$modx->db->query("UPDATE ".$modx->getFullTableName('_shop_mail')." SET result='".($phpmailer_result?'y':'n')."' WHERE code='{$code}' LIMIT 1");
		
		$modx->db->query("UPDATE ".$modx->getFullTableName('_Shop_Order')." SET
			status=5,
			fio='{$fio}',
			email='{$email}',
			phone='{$phone}',
			city='{$city}',
			address='{$address}',
			message='{$message_db}',
			logs=CONCAT(logs,'\n".date('d.m.Y, H:i')." | Заказ оформлен'),
			checkout='".time()."',
			secret='{$secret}'
			WHERE code='{$code}' LIMIT 1");
		
		return '{"result":"ok", "s":"'.$secret.'"}';
	}
	$errors .= '<br>';
	return '{"result":"error", "errors":"'.addslashes($errors).'"}';
}

// Сохранение данных покупателя
if($act == 'data')
{
	$nm= $modx->db->escape($_GET['nm']);
	$vl= $modx->db->escape(trim($_GET['vl']));
	if(
		$nm == 'fio' ||
		$nm == 'email' ||
		$nm == 'phone' ||
		$nm == 'city' ||
		$nm == 'address' ||
		$nm == 'message'
	){
		$modx->db->query("UPDATE ".$modx->getFullTableName('_Shop_Order')." SET `{$nm}`='{$vl}' WHERE code='{$code}' LIMIT 1");
	}
}

// Прибавление к кол-ву
if($act == 'plus')
{
	$modx->db->query("UPDATE ".$modx->getFullTableName('_shop_items')." SET `count`=`count`+{$count} WHERE code='{$code}' AND uniq='{$uniq}' LIMIT 1");
	$modx->runSnippet('ShopAction',array('act'=>'itogo'));
}

// Изменение кол-ва
if($act == 'count')
{
	$modx->db->query("UPDATE ".$modx->getFullTableName('_shop_items')." SET `count`={$count} WHERE code='{$code}' AND uniq='{$uniq}' LIMIT 1");
	$modx->runSnippet('ShopAction',array('act'=>'itogo'));
}

// Удаление позиции из корзины
if($act == 'delete')
{
	$modx->db->query("DELETE FROM ".$modx->getFullTableName('_shop_items')." WHERE code='{$code}' AND uniq='{$uniq}' LIMIT 1");
	$modx->runSnippet('ShopAction',array('act'=>'itogo'));
}

// Удаление всех позиций из корзины
if($act == 'delete_all')
{
	$modx->db->query("DELETE FROM ".$modx->getFullTableName('_shop_items')." WHERE code='{$code}'");
	$modx->runSnippet('ShopAction',array('act'=>'itogo'));
}

// Получение статуса позиции в корзине
if($act == 'stat')
{
	$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('_shop_items')." WHERE code='{$code}' AND uniq='{$uniq}' LIMIT 1");
	if($rr && $modx->db->getRecordCount($rr)) return 'y';
	elseif($rr) return 'n';
}

// Проверка товаров из корзины в таблице каталога
if($act == 'check_items_in_catalog')
{
	$items= $modx->runSnippet('ShopAction',array('act'=>'items'));
	if($items)
	{
		foreach($items AS $row) $ids .= (!empty($row['id_item'])?',':'').$row['id_item'];
		$catitems= $modx->runSnippet('DocLister',array('api'=>true, 'idType'=>'documents', 'documents'=>$ids,
													   'selectFields'=>$selectFields.',template,deleted,published',
													   'tvList'=>$tvList));
		$catitems= json_decode($catitems, true);
		foreach($items AS $row)
		{
			$flag= false;
			$catitem= $catitems[$row['id_item'] ];
			
			if($catitem['id'] && $catitem['template']==5 && $catitem['isfolder']==0 && $catitem['deleted']==0 && $catitem['published']==1)
			{
				$price= intval($row['price']);
				$tv_price= intval($catitem['tv_price']);
				if($price != $tv_price)
				{
					$name= $modx->db->escape($catitem['pagetitle']);
					$art= $modx->db->escape($catitem['tv_article']);
					$ed= $modx->db->escape($catitem['tv_ed']);
					
					$prms= $modx->runSnippet('ShopAction',array('act'=>'prms', 'id'=>$catitem['id']));
					$prms= $modx->db->escape($prms);
					
					$modx->db->query("UPDATE ".$modx->getFullTableName('_shop_items')." SET
						`name`='{$name}',
						art='{$art}',
						price='{$tv_price}',
						ed='{$ed}',
						prms='{$prms}'
					WHERE code='{$code}' AND uniq='{$row[uniq]}' LIMIT 1");
				}
			}else{
				$modx->db->query("DELETE FROM ".$modx->getFullTableName('_shop_items')." WHERE code='{$code}' AND uniq='{$row[uniq]}' LIMIT 1");
			}
		}
	}
	$modx->runSnippet('ShopAction',array('act'=>'itogo'));
}

// Получение массива всех позиций в корзине
if($act == 'items')
{
	$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_shop_items')." WHERE code='{$code}'");
	if($rr && $modx->db->getRecordCount($rr)) while($row= $modx->db->getRow($rr)) $items[]= $row;
	return (is_array($items) && count($items) ? $items : false);
}

// Кол-во товара в корзине
if($act == 'items_count')
{
	$rr= $modx->db->query("SELECT COUNT(id) AS cc FROM ".$modx->getFullTableName('_shop_items')." WHERE code='{$code}'");
	if($rr && $modx->db->getRecordCount($rr)) return $modx->db->getValue($rr); return '0';
}

// Считаем итого
if($act == 'itogo')
{
	$itogo= 0;
	$items= $modx->runSnippet('ShopAction',array('act'=>'items'));
	if($items) foreach($items AS $row) $itogo += $row['price'] * $row['count'];
	$modx->db->query("UPDATE ".$modx->getFullTableName('_shop_order')." SET itogo='{$itogo}' WHERE code='{$code}' LIMIT 1");
}

if($act == 'prms')
{
	$prms= $modx->runSnippet('CatalogFilter_Props', array('id'=>$id, 'values'=>true));
	if(is_array($prms) && count($prms))
	{
		foreach($prms AS $prm)
		{
			if(is_array($prm['values']) && count($prm['values']))
			{
				$prms_a[$prm['cf_id'] ]['name']= $prm['name'] .($prm['ed']?', '.$prm['ed']:'');

				foreach($prm['values'] AS $vls)
				{
					$prms_a[$prm['cf_id'] ]['values'][]= $vls;
				}
			}
		}
		$prms_a= serialize($prms_a);
	}
	return $prms_a;
}
