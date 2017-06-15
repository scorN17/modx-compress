<?php
/**
 * LK_Restore
 *
 * Восстановление доступа
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


//----------------------------------------------------------------------------------
//SMTP - true OR false
$mailtype_smtp= false;

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




$topage= ($topage ? intval($topage) : $modx->documentIdentifier);
$topage_url= $modx->makeUrl($topage);








$etap= intval($etap);
if( ! $etap) $etap= 1;

if(isset($_GET['e1ok'])) $etap= 2;

if(isset($_GET['u']) && isset($_GET['s'])) $etap= 3;



if(isset($_POST['restp_restp']))
{
	$restp_email= trim($_POST['restp_email']);
	$restp_password= md5($_POST['restp_password']);
	
	$restp_email_qq= $modx->db->escape($restp_email);
	
	if(empty($restp_email) || empty($_POST['restp_password']))
	{
		$restp_err .= '<p>- Необходимо заполнить все поля формы</p>';
	}
	
	if(empty($restp_err))
	{
		$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_user')." WHERE email='{$restp_email_qq}' LIMIT 1");
		if($rr && $modx->db->getRecordCount($rr))
		{
			$row= $modx->db->getRow($rr);
			
			srand(time());
			$secret= md5($restp_password . rand(100,999) . $restp_email . time() . $row['dt_reg']);
			
			$secret_link= $modx->makeUrl($restorepassword,'','','full').'?u='.$row['id'].'&s='.$secret;
			
			$modx->db->query("UPDATE ".$modx->getFullTableName('_user')." SET password_new='{$restp_password}', password_dt='".time()."', secret='{$secret}' WHERE email='{$restp_email_qq}' LIMIT 1");
			
			
			
			$subject= 'Восстановление доступа на сайте www.'.$_SERVER['HTTP_HOST'];
			
			$message= '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>'.$subject.'</title>
			</head><body>
				<h2>'.$subject.'</h2>

				<p>Через форму на сайте было запрошено восстановление доступа к Вашему личному кабинету.</p>
				<p>Новый пароль: <b>'.htmlspecialchars($_POST['restp_password']).'</b></p>
				<p>— пароль не активирован и не действителен!</p>
				<p>Чтобы активиривать новый пароль, перейдите по ссылке или скопируйте ее в адресную строку браузера:<br />
				<a target="_blank" href="'.$secret_link.'">'.$secret_link.'</a></p>
				<p>Если вы не запрашивали восстановление доступа, просто проигнорируйте это письмо.</p>
			</body></html>';
			
			
			
			$mailto= $restp_email;
			$mailcc= false;
			$mailbcc= false;
			
			
			
			//------------------------------------------------------------------------------
			include_once(MODX_MANAGER_PATH.'includes/controls/class.phpmailer.php');
			if(true)
			{
				$phpmailer= new PHPMailer();
				if(true)
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
				$phpmailer->FromName= '';
				$phpmailer->isHTML(true);
				$phpmailer->Subject= $subject;
				$phpmailer->Body= $message;

				$mailto= explode(',', $mailto); foreach($mailto AS $row) $phpmailer->addAddress(trim($row));
				if($mailcc){ $mailcc= explode(',', $mailcc); foreach($mailcc AS $row) $phpmailer->addCC(trim($row)); }
				if($mailbcc){ $mailbcc= explode(',', $mailbcc); foreach($mailbcc AS $row) $phpmailer->addBCC(trim($row)); }
				$phpmailer_result= $phpmailer->send();
			}
			//------------------------------------------------------------------------------
			
			header('location: '.$topage_url.'?e1ok');
			exit();
			
		}elseif($rr){
			$restp_err .= '<p>- Пользователь не найден!</p>';
		}else{
			$restp_err .= '<p>- Ошибка базы данных (001)</p>';
		}
	}
}





if($etap == 1)
{
?>

<div class="_LK_form _LK_form_restp">
	<?php if( ! empty($restp_err)) print '<div class="_LK_error">'.$restp_err.'<br></div>';?>

	<form id="form" action="<?=$topage_url?>" method="post">
		<div class="_LK_form_txt">На указанный адрес электронной почты<br />будет выслана инструкция по восстановлению доступа к аккаунту.</div>
		<br><br>

		<div class="_LK_form_line">
			<div class="_LK_form_lbl">Адрес электронной почты</div>
			<div class="_LK_form_inp"><input type="text" name="restp_email" /></div>
		</div>

		<div class="_LK_form_line">
			<div class="_LK_form_lbl">Задайте новый пароль</div>
			<div class="_LK_form_inp"><input type="password" name="restp_password" /></div>
		</div>

		<div class="_LK_form_line _LK_form_line_butt">
			<div class="_LK_form_inp"><button class="mainbutton buttonsubmit font2" name="restp_restp" type="submit">Продолжить</button></div>
		</div>
	</form>
</div>

<?php
}






if($etap == 2)
{
?>


<div class="_LK_form _LK_form_restp">
	<div class="_LK_ok">
		<p>На указанный адрес электронной почты<br />выслана инструкция по восстановлению доступа к аккаунту.</p>
	</div>
</div>

<?php
}





if($etap == 3)
{
	$userid= intval($_GET['u']);
	$secret= $modx->db->escape($_GET['s']);
	
	$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_user')." WHERE id={$userid} AND secret='{$secret}' AND password_new<>'' AND password_dt>0 LIMIT 1");
	if($rr && $modx->db->getRecordCount($rr))
	{
		$row= $modx->db->getRow($rr);
		
		if(time() - $row['password_dt'] < 60*60*24)
		{
			$modx->db->query("UPDATE ".$modx->getFullTableName('_user')."
				SET password='".$row['password_new']."', password_new='', secret='', password_dt='' WHERE id={$userid} LIMIT 1");
			
			$result= '<div class="_LK_ok"><p>Новый пароль успешно <b>активирован!</b></p><p><a href="'.$modx->makeUrl($auth).'">Вход в систему</a></p></div>';
			
		}else{
			$result= '<div class="_LK_error"><p>Срок действия ссылки на активацию нового пароля истек!</p><p>Запросите <a href="'.$modx->makeUrl($restorepassword).'">восстановление еще раз</a>.</p></div>';
		}
		
	}elseif($rr){
		$result= '<div class="_LK_error"><p>Неверные данные!</p></div>';
		
	}else{
		$result= '<div class="_LK_error"><p>Ошибка базы данных (002)</p></div>';
	}
?>




<div class="_LK_form _LK_form_restp">
	<?= $result ?>
</div>

<?php
}
