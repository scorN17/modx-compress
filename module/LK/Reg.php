<?php
/**
 * LK_Reg
 *
 * Форма Регистрации
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




if(isset($_POST['reg_reg']))
{
	// CAPTCHA --------------------------------------------------------------------------
	$reg_err_flag['reg_captcha']= true;
 	$reg_captcha= $_POST['g-recaptcha-response'];
	$postdata= array(
		'secret'    => '6LfemiIUAAAAAKcBiAoS613AwooM-MPvsWMawdGe',
		'response'  => $reg_captcha,
		'remoteip'  => $_SERVER['REMOTE_ADDR']
	);
	$curloptions= array(
		CURLOPT_URL               => 'https://www.google.com/recaptcha/api/siteverify',
		CURLOPT_RETURNTRANSFER    => true,
		CURLOPT_POST              => true,
		CURLOPT_POSTFIELDS        => $postdata
	);
	$curl= curl_init();
	curl_setopt_array($curl, $curloptions);
	$curlresult= curl_exec($curl);
	if( ! curl_errno($curl))
	{
		$curlresult= json_decode($curlresult,true);
		if($curlresult['success']===true) $reg_err_flag['reg_captcha']= false;
			else $reg_err .= '<p>- Вы робот?</p>';
	}else $reg_err .= '<p>- Ошибка. Попробуйте позже или обратитесь к администратору.</p>';
	curl_close($curl);
	// CAPTCHA --------------------------------------------------------------------------
	
	
	
	
	$reg_email= trim($_POST['reg_email']);
	$reg_phone= trim($_POST['reg_phone']);
	$reg_password= md5($_POST['reg_password']);
	$reg_agreed= ($_POST['reg_agreed']=='y' ? 'y' : 'n');
	
	$reg_email_qq= $modx->db->escape($reg_email);
	$reg_phone_qq= $modx->db->escape($reg_phone);
	
	if( ! $reg_err_flag['reg_captcha'])
	{
		if($reg_agreed != 'y')
		{
			//$reg_err .= '<p>- Необходимо принять <a target="_blank" href="[~'.$agreed.'~]">условия пользовательского соглашения</a></p>';
		}
		if( ! preg_match("/^[a-z0-9-_\.]{1,}@[a-z0-9-\.]{1,}\.[a-z]{2,10}$/i", $reg_email))
		{
			$reg_err .= '<p>- Адрес электронной почты неверного формата</p>';
			$reg_err_flag['reg_email']= true;
		}
		if( ! preg_match("/^\+7 \([0-9]{3}\) [0-9]{3}-[0-9]{4}$/i", $reg_phone))
		{
			$reg_err .= '<p>- Номер телефона неверного формата</p>';
			$reg_err_flag['reg_phone']= true;
		}
		if(empty($_POST['reg_password']))
		{
			$reg_err .= '<p>- Задайте пароль</p>';
			$reg_err_flag['reg_password']= true;
		}
	}
	if( ! $reg_err)
	{
		$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_user')." WHERE email='{$reg_email_qq}' LIMIT 1");
		if($rr && $modx->db->getRecordCount($rr))
		{
			$reg_err .= '<p>- Этот Адрес электронной почты уже зарегистрирован</p>';
			$reg_err_flag['reg_email']= true;
		}
		
		$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_user')." WHERE mobile='{$reg_phone_qq}' LIMIT 1");
		if($rr && $modx->db->getRecordCount($rr))
		{
			$reg_err .= '<p>- Этот Номер мобильного телефона уже зарегистрирован</p>';
			$reg_err_flag['reg_phone']= true;
		}
		
		if( ! $reg_err)
		{
			$rr= $modx->db->query("INSERT INTO ".$modx->getFullTableName('_user')." SET email='{$reg_email_qq}', phone='{$reg_phone_qq}', password='{$reg_password}', dt_reg='".time()."'");
			if($rr)
			{
				header('location: '.$topage_url.'?ok');
				exit();
			}else{
				$reg_err .= '<p>- Ошибка базы данных (002)</p>';
			}
		}
	}
}

?>
<div class="_LK_form _LK_form_reg">
	<?php if(isset($_GET['ok'])){ ?>
	<div class="_LK_ok">
		<p><b>Вы успешно зарегистрированы на сайте!</b></p>
	</div>

	<?php }else{ ?>

	<?php if( ! empty($reg_err)) print '<div class="_LK_error">'.$reg_err.'<br></div>'; ?>

	<form action="<?=$topage_url?>" method="post">
		<div class="_LK_form_line">
			<div class="_LK_form_lbl">Адрес электронной почты</div>
			<div class="_LK_form_inp <?=($reg_err_flag['reg_email'] ? '_LK_form_inp_error' :'')?>"><input type="text" name="reg_email" value="<?=$reg_email?>" /></div>
		</div>

		<div class="_LK_form_line _LK_form_line_br">
			<div class="_LK_form_lbl">Номер телефона</div>
			<div class="_LK_form_inp <?=($reg_err_flag['reg_phone'] ? '_LK_form_inp_error' : '')?>"><input class="phonemask" type="text" name="reg_phone" value="<?=$reg_phone?>" /></div>
		</div>

		<div class="_LK_form_line _LK_form_line_br">
			<div class="_LK_form_lbl">Задайте пароль</div>
			<div class="_LK_form_inp <?=($reg_err_flag['reg_password'] ? '_LK_form_inp_error' : '')?>"><input type="password" name="reg_password" /></div>
		</div>

		<div class="_LK_form_line">
			<div class="_LK_form_lbl"> </div>
			<div class="_LK_form_inp"><div class="g-recaptcha" data-sitekey="6LfemiIUAAAAAE-dC9NniRzvbNY9hz4R4bVNrO0w"></div></div>
		</div>

		<div class="_LK_form_line _LK_form_line_butt">
			<div class="_LK_form_inp"><button class="mainbutton buttonsubmit font2" name="reg_reg" type="submit">Зарегистрироваться</button></div>
		</div>
	</form>
	<?php } ?>
</div>
