<?php
/**
 * Formochki
 * 
 * Формочки
 * 
 * @version     1.1
 * @date        23.05.2017
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
	include_once( MODX_MANAGER_PATH .'includes/controls/class.phpmailer.php' );
	
	$frm_formid= intval($_POST['frm_formid']);
	$frm_name= trim($_POST['frm_name']);
	$frm_email= trim($_POST['frm_email']);
	$frm_phone= trim($_POST['frm_phone']);
	$frm_kogda= trim($_POST['frm_kogda']);
	$frm_pageid= intval($_POST['frm_pageid']);
	$frm_text= trim($_POST['frm_text']);
	$frm_text2= str_replace("\r\n", '<br>', $frm_text);

	if($result){
	}elseif( ! $frm_email && ! $frm_phone) $result= '{"result":"error","text":"Укажите контактные данные"}';
	
	if( ! $result)
	{
		$subject= ($frm_formid==2 ? 'Заказ звонка' : 'Сообщение') .' с сайта www.'.getenv('HTTP_HOST');

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
			$result= '{"result":"error","text":"Ошибка сервера! Повторите попытку позже."}';
		}
	}
	print $result;
	if(isset($_GET['ajax'])){ header('Content-Type:text/html; charset=UTF-8'); exit(); }
}
