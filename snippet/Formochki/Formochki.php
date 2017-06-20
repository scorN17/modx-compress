<?php
/**
 * Formochki
 * 
 * Формочки
 * 
 * @version     1.2
 * @date        20.06.2017
 *
 *
 *
 */
	
//----------------------------------------------------------------------------------
//SMTP - true OR false
$mailtype_smtp= true;

//КОМУ (через запятую)
$mailto= 'sergey.it@delta-ltd.ru';

//Видимые копии (через запятую)
$mailcc= false;

//Скрытые копии (через запятую)
$mailbcc= false;

//ОТ (если SMTP, то это поле - логин)
$mailfrom= 'feedback.noreply@yandex.ru';

//Пароль от почты (если SMTP)
$mailpassw= 'XSbKjp7ZjdoaesD_o_0j';
//Любимый киногерой: ----------- //Секретный вопрос от почты

//Сервер SMTP (если SMTP)
$smtp= 'smtp.yandex.ru';

//Порт SMTP (если SMTP)
$smtpport= 465;

//SNIPPET Formochki
//----------------------------------------------------------------------------------

/**
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */

if($_GET['act']=='formochki_send')
{
	// CAPTCHA --------------------------------------------------------------------------
	if(true)
	{
		$captcha_flag= false;
		$reg_captcha= $_POST['g-recaptcha-response'];
		if($_SESSION['g-recaptcha'][$reg_captcha]) $captcha_flag= true;
		elseif($reg_captcha){
			$postdata= array(
				'secret'    => '6LeOGiYUAAAAANUTeFo9eT9MEjKltc0lxq9tQLSa',
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
				if($curlresult['success']===true)
				{
					$captcha_flag= true;
					$_SESSION['g-recaptcha'][$reg_captcha]= true;
				}
			}else $result= '{"result":"error","text":"Ошибка. Попробуйте позже или обратитесь к администратору."}';
			curl_close($curl);
		}
		if( ! $result && ! $captcha_flag) $result= '{"result":"error","text":"Вы робот?"}';
	}
	// CAPTCHA --------------------------------------------------------------------------
	
	
	
	$frm_formid= intval($_POST['frm_formid']);
	$frm_name= trim($_POST['frm_name']);
	$frm_email= trim($_POST['frm_email']);
	$frm_phone= trim($_POST['frm_phone']);
	$frm_kogda= trim($_POST['frm_kogda']);
	$frm_pageid= intval($_POST['frm_pageid']);
	$frm_text= trim($_POST['frm_text']);
	$frm_text2= str_replace("\r", '', $frm_text);
	$frm_text2= str_replace("\n", '<br>', $frm_text2);
	
	if( ! $result && $frm_formid == 3 && ( ! $frm_name || ! $frm_email || ! $frm_text))
		$result= '{"result":"error","text":"Заполните обязательные поля"}';
	
	if($result){
	}elseif( ! $frm_email && ! $frm_phone) $result= '{"result":"error","text":"Укажите контактные данные"}';
	
	if( ! $result)
	{
		$subject= ($frm_formid==2 ? 'Заказ звонка' : 'Сообщение') .' с сайта '.MODX_SITE_URL;

		$message= '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>'.$subject.'</title></head><body><h2>'.$subject.'</h2>';

		if($frm_pageid) $message .= '<p><b>Отправлено со страницы:</b> '.$modx->makeUrl($frm_pageid, '', '', 'full').'</p>';

		if($frm_name) $message .= '<p><b>Имя Отчество:</b> '.$frm_name.'</p>';

		if($frm_email) $message .= '<p><b>E-mail:</b> '.$frm_email.'</p>';

		if($frm_phone) $message .= '<p><b>Контактный телефон:</b> '.$frm_phone.'</p>';

		if($frm_kogda) $message .= '<p><b>Когда позвонить?</b> '.$frm_kogda.'</p>';

		$message .= '<p><b>Дата и время сообщения:</b> '.date('d.m.Y, H:i').'</p>';

		if($frm_text2) $message .= '<p><b>Сообщение:</b><br />'.$frm_text2.'</p>';

		$message .= '</body></html>';
		
		
		//------------------------------------------------------------------------------
		include_once( MODX_MANAGER_PATH .'includes/controls/class.phpmailer.php' );
		if(true)
		{
			$phpmailer= new PHPMailer();
			if( false )
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
		
		
		if($phpmailer_result)
		{
			$result= '{"result":"ok","text":"Сообщение отправлено!"}';
		}else{
			$result= '{"result":"error","text":"Ошибка! Повторите попытку позже."}';
		}
	}
	print $result;
	if(isset($_GET['ajax'])){ header('Content-Type:text/html; charset=UTF-8'); exit(); }
}
?>
