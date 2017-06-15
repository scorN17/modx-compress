<?php
/**
 * LK_Auth
 *
 * Форма Аутентификации
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



if(isset($_POST['auth_auth']))
{
	$auth_email= trim($_POST['auth_email']);
	$auth_password= md5($_POST['auth_password']);
	
	$auth_email_qq= $modx->db->escape($auth_email);
	
	$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_user')." WHERE email='{$auth_email_qq}' AND password='{$auth_password}' LIMIT 1");
	if($rr && $modx->db->getRecordCount($rr))
	{
		$_SESSION['webuserinfo' ]['auth']= true;
		$_SESSION['webuserinfo' ]['dt']= time();
		$_SESSION['webuserinfo' ]['info']= $modx->db->getRow($rr);
		$_SESSION['webuserinfo' ]['id']= $_SESSION['webuserinfo']['info']['id'];
		
		header('location: '. $topage_url);
		exit();
		
	}elseif($rr) $auth_err .= '<p>- Неверно введен Логин или Пароль</p>';
	else $auth_err .= '<p>- Ошибка базы данных (003)</p>';
}


?>
<div class="_LK_form _LK_form_auth">
	<?php if( ! empty( $auth_err)) print '<div class="_LK_error">'.$auth_err.'<br></div>';?>

	<form action="<?=$topage_url?>" method="post">
		<div class="_LK_form_line">
			<div class="_LK_form_lbl">Адрес электронной почты</div>
			<div class="_LK_form_inp"><input type="text" name="auth_email" value="<?=$auth_email?>" /></div>
		</div>

		<div class="_LK_form_line _LK_form_line_br">
			<div class="_LK_form_lbl">Пароль</div>
			<div class="_LK_form_inp"><input type="password" name="auth_password" /></div>
		</div>

		<div class="_LK_form_line">
			<div class="_LK_form_lbl"></div>
			<div class="_LK_form_inp"><a href="<?=$modx->makeUrl($restorepassword)?>">Забыли пароль?</a></div>
		</div>

		<div class="_LK_form_line _LK_form_line_butt">
			<div class="_LK_form_inp"><button class="mainbutton buttonsubmit font2" name="auth_auth" type="submit">Войти</button></div>
		</div>
	</form>
</div>
