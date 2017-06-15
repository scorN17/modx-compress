<?php
/**
 * LK_UserInfo
 *
 * Страница редактирования данных
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




if(isset($_POST['save_1']))
{
	$save_fio= trim($_POST['save_fio']);
	$save_email= trim($_POST['save_email']);
	$save_phone= trim($_POST['save_phone']);
	$save_city= trim($_POST['save_city']);
	$save_address= trim($_POST['save_address']);
	
	$save_fio_qq= $modx->db->escape($save_fio);
	$save_email_qq= $modx->db->escape($save_email);
	$save_phone_qq= $modx->db->escape($save_phone);
	$save_city_qq= $modx->db->escape($save_city);
	$save_address_qq= $modx->db->escape($save_address);
	
	$qq= '';
	
	if($save_fio && $save_fio != $webuserinfo['fio']) $qq .= (!empty($qq)?", ":"")."fio='{$save_fio_qq}'";
	if($save_city && $save_city != $webuserinfo['city']) $qq .= (!empty($qq)?", ":"")."city='{$save_city_qq}'";
	if($save_address && $save_address != $webuserinfo['address']) $qq .= (!empty($qq)?", ":"")."address='{$save_address_qq}'";
	
	if( ! preg_match("/^[a-z0-9-_\.]{1,}@[a-z0-9-\.]{1,}\.[a-z]{2,10}$/i", $save_email))
	{
		$save_err .= '<p>- Адрес электронной почты неверного формата</p>';
		$save_err_flag['save_email']= true;
		
	}elseif($save_email != $webuserinfo['email']){
		$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_user')." WHERE email='{$save_email_qq}' AND id<>".$webuserinfo['id']." LIMIT 1");
		if($rr && $modx->db->getRecordCount($rr))
		{
			$save_err .= '<p>- Этот Адрес электронной почты уже зарегистрирован</p>';
			$save_err_flag['save_email']= true;
			
		}elseif($rr){
			$qq .= (!empty($qq)?", ":"")."email='{$save_email_qq}'";
		}
	}
	
	if( ! preg_match("/^\+7 \([0-9]{3}\) [0-9]{3}-[0-9]{4}$/i", $save_phone))
	{
		$save_err .= '<p>- Номер телефона неверного формата</p>';
		$save_err_flag['save_phone']= true;
		
	}elseif($save_phone != $webuserinfo['phone']){
		$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_user')." WHERE phone='{$save_phone_qq}' AND id<>".$webuserinfo['id']." LIMIT 1");
		if($rr && $modx->db->getRecordCount($rr))
		{
			$save_err .= '<p>- Этот Номер мобильного телефона уже зарегистрирован</p>';
			$save_err_flag['save_phone']= true;
			
		}elseif($rr){
			$qq .= (!empty($qq)?", ":"")."phone='{$save_phone_qq}'";
		}
	}
	
	if($qq)
	{
		$modx->db->query("INSERT INTO ".$modx->getFullTableName('_user_h')." SELECT * FROM ".$modx->getFullTableName('_user')." WHERE id=".$webuserinfo['id']." LIMIT 1");
		
		$modx->db->query("UPDATE ".$modx->getFullTableName('_user')." SET {$qq}, dt_edit=".time()." WHERE id=".$webuserinfo['id']." LIMIT 1");
		
		header('location: '.$topage_url.'?ok1');
		exit();
	}
}
?>



<div class="_LK_buttons">
	<div class="lkb_b">
		<a class="font2" href="[~<?=$shophistory?>~]">История заказов</a>
	</div>
	
	<div class="lkb_b">
		<a class="font2" href="[~<?=$auth?>~]?exit">Выйти из аккаунта</a>
	</div>
	
	<br>
</div>




<h2>Изменение личных данных</h2>


<div class="_LK_form">
	<?php if(isset($_GET['ok1'])){ ?>
	<div class="_LK_ok">
		<p>Данные успешно сохранены!</p>
	</div>
	<?php } ?>

	<?php if( ! empty($save_err)) print '<div class="_LK_error">'.$save_err.'<br></div>';?>
	<form action="<?=$topage_url?>" method="post">
		<div class="_LK_form_line">
			<div class="_LK_form_lbl">Фамилия Имя</div>
			<div class="_LK_form_inp <?=($save_err_flag['save_fio'] ? '_LK_form_inp_error' :'')?>"><input type="text" name="save_fio" value="<?=$webuserinfo['fio']?>" /></div>
		</div>

		<div class="_LK_form_line">
			<div class="_LK_form_lbl">Адрес электронной почты</div>
			<div class="_LK_form_inp <?=($save_err_flag['save_email'] ? '_LK_form_inp_error' :'')?>"><input type="text" name="save_email" value="<?=$webuserinfo['email']?>" /></div>
		</div>
		<div class="_LK_form_line _LK_form_line_br">
			<div class="_LK_form_lbl">Номер телефона</div>
			<div class="_LK_form_inp <?=($save_err_flag['save_phone'] ? '_LK_form_inp_error' :'')?>"><input class="phonemask" type="text" name="save_phone" value="<?=$webuserinfo['phone']?>" /></div>
		</div>

		<div class="_LK_form_line _LK_form_line_br">
			<div class="_LK_form_lbl">Город</div>
			<div class="_LK_form_inp <?= ($save_err_flag['save_city'] ? '_LK_form_inp_error' :'')?>"><input type="text" name="save_city" value="<?=$webuserinfo['city']?>" /></div>
		</div>
		<div class="_LK_form_line _LK_form_line_br">
			<div class="_LK_form_lbl">Адрес</div>
			<div class="_LK_form_inp <?= ($save_err_flag['save_address'] ? '_LK_form_inp_error' :'')?>"><input type="text" name="save_address" value="<?=$webuserinfo['address']?>" /></div>
		</div>

		<div class="_LK_form_line _LK_form_line_butt">
			<div class="_LK_form_inp"><button class="mainbutton buttonsubmit font2" name="save_1" type="submit">Сохранить</button></div>
		</div>
	</form>
</div>
