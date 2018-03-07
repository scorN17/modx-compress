<?php
/**
 * Formochki
 * 
 * Формочки
 * 
 * @version     2.3
 * @date        07.03.2018
 *
 *
 *
 */
	
//----------------------------------------------------------------------------------
//КОМУ (через запятую)
$mailto       = 'sergey.it@delta-ltd.ru';

//ОТ
$mailfrom     = 'noreply@web-trend.online'; //tqlwGHvd4_V_fMuEr17M
$mailfromname = '';

//ОТВЕТИТЬ КОМУ
$mailreplyto  = false;

//Видимые копии (через запятую)
$mailcc       = false;

//Скрытые копии (через запятую)
$mailbcc      = false;

//SNIPPET Formochki
//----------------------------------------------------------------------------------


$subjects= array(
	1 => 'Заказ КП',
	2 => 'Бесплатная консультация',
	3 => 'Доступ к портфолио',
);

$debug= false;

$upload_folder= 'assets/files/formochki/';

/**
 *
 *
 *
 *
 *
 *
 *
 *
 */

if (isset($_GET['formochki']) && $modx->hasPermission('save_user')) {
	$limit = isset($_GET['limit']) ? $modx->db->escape($_GET['limit']) : '10';
	$rr = $modx->db->query("SELECT * FROM ".$modx->getFullTableName('formochki')." ORDER BY id DESC LIMIT ".$limit);
	if ($rr) {
		while ($row = $modx->db->getRow($rr)) {
			print $row;
			print '<br><hr><br>';
		}
	}
}

if ($_GET['act'] != 'formochki_send') {
	return;
}

$modx->db->query("CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName('formochki')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail` longtext NOT NULL,
  `dt` varchar(31) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=`utf8` AUTO_INCREMENT=1;");

// CAPTCHA --------------------------------------------------------------------------
if (false) {
	$captcha_flag= false;
	$reg_captcha= $_POST['g-recaptcha-response'];
	if ($_SESSION['g-recaptcha'][$reg_captcha]) {
		$captcha_flag= true;
	} elseif ($reg_captcha) {
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
		if ( ! curl_errno($curl)) {
			$curlresult= json_decode($curlresult, true);
			if ($curlresult['success'] === true) {
				$captcha_flag= true;
				$_SESSION['g-recaptcha'][$reg_captcha]= true;
			}
		} else {
			$result= '{"result":"error","text":"Ошибка. Попробуйте позже или обратитесь к администратору."}';
		}
		curl_close($curl);
	}
	if ( ! $result && ! $captcha_flag) {
		$result= '{"result":"error","text":"Вы робот?"}';
	}
}
// CAPTCHA --------------------------------------------------------------------------


$frm_privacy_policy = $_POST['frm_privacy_policy'] == 'y' ? 'y' : 'n';
$frm_formid         = intval($_POST['frm_formid']);
$frm_name           = trim($_POST['frm_name']);
$frm_email          = trim($_POST['frm_email']);
$frm_phone          = trim($_POST['frm_phone']);
$frm_kogda          = trim($_POST['frm_kogda']);
$frm_pageid         = intval($_POST['frm_pageid']);
$frm_text           = trim($_POST['frm_text']);
$frm_text2          = str_replace("\r", '', $frm_text);
$frm_text2          = str_replace("\n", '<br>', $frm_text2);

$frm_file           = $_FILES['frm_file'];

if ( ! $result && $frm_privacy_policy != 'y') {
	$result = '{"result":"error","text":"Не отправлено! Нет согласия на обработку персональных данных."}';
}

if ( ! $result && $frm_formid == 111 && ( ! $frm_phone || ! $frm_email)) {
	$result = '{"result":"error","text":"Заполните обязательные поля"}';
}

if ($result) {
} elseif ( ! $frm_email && ! $frm_phone) {
	$result = '{"result":"error","text":"Укажите контактные данные"}';
}

if ( ! $result && is_uploaded_file($frm_file['tmp_name'])) {
	if ($frm_file['size'] > 1024*1024*10) {
		$result = '{"result":"error","text":"Файл слишком большой"}';
	} else {
		$folder = md5('_'.$_SERVER['REMOTE_ADDR']);
		$folder = $upload_folder.$folder.'/';
		if ( ! file_exists(MODX_BASE_PATH.$folder)) {
			mkdir(MODX_BASE_PATH.$folder, 0755, true);
		}
		$file = md5($frm_file['name'].time()).'.zip';
		$zip = new ZipArchive();
		$zip->open(MODX_BASE_PATH.$folder.$file, ZIPARCHIVE::CREATE);
		$zip->addFile($frm_file['tmp_name'], $frm_file['name']);
		$zip->close();
		
		$frm_file= MODX_SITE_URL.$folder.$file;
	}
} else {
	$frm_file = null;	
}

if ( ! $result) {
	$subject = $subjects[$frm_formid];
	if ( ! $subject) $subject = 'Сообщение';
	$subject .= ', '.MODX_SITE_URL;

	$message = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>'.$subject.'</title></head><body><h2>'.$subject.'</h2>';

	if ($frm_pageid) $message .= '<p><b>Отправлено со страницы:</b> '.$modx->makeUrl($frm_pageid,'','','full').'</p>';

	if ($frm_name) $message .= '<p><b>Имя Отчество:</b> '.$frm_name.'</p>';

	if ($frm_email) $message .= '<p><b>E-mail:</b> '.$frm_email.'</p>';

	if ($frm_phone) $message .= '<p><b>Контактный телефон:</b> '.$frm_phone.'</p>';

	if ($frm_kogda) $message .= '<p><b>Когда позвонить?</b> '.$frm_kogda.'</p>';

	$message .= '<p><b>Дата и время сообщения:</b> '.date('d.m.Y, H:i').'</p>';

	if($frm_text2) $message .= '<p><b>Сообщение:</b><br />'.$frm_text2.'</p>';
	
	if ($frm_file) $message .= '<p><b>Загруженный файл:</b><br /><a target="_blank" href="'.$frm_file.'">'.$frm_file.'</a></p>';

	$message .= '</body></html>';
	
	$message_db = $modx->db->escape($message);
	$modx->db->query("INSERT INTO ".$modx->getFullTableName('formochki')." SET mail='{$message}', dt='".date('d.m.Y, H:i')."'");

	//------------------------------------------------------------------------------
	if (true) {
		$modx->loadExtension('modxmailer');

		if ($debug) {
			$modx->mail->SMTPDebug   = 2;
			$modx->mail->Debugoutput = 'html';
		}

		$modx->mail->From     = $mailfrom;
		$modx->mail->Sender   = $mailfrom;
		$modx->mail->FromName = $mailfromname;

		$modx->mail->isHTML(true);
		$modx->mail->Subject = $subject;
		$modx->mail->Body    = $message;

		if ($mailreplyto || $frm_email) {
			$modx->mail->addReplyTo($mailreplyto ? $mailreplyto : $frm_email);
		}

		if ($mailto) {
			$mailto= explode(',', $mailto);
			foreach ($mailto AS $row) {
				$modx->mail->addAddress(trim($row));
			}
		}

		if ($mailcc) {
			$mailcc= explode(',', $mailcc);
			foreach ($mailcc AS $row) {
				$modx->mail->addCC(trim($row));
			}
		}

		if ($mailbcc) {
			$mailbcc= explode(',', $mailbcc);
			foreach ($mailbcc AS $row) {
				$modx->mail->addBCC(trim($row));
			}
		}

		$phpmailer_result = $modx->mail->Send();
	}
	//------------------------------------------------------------------------------


	if ($phpmailer_result) {
		$result = '{"result":"ok","text":"Сообщение отправлено!"}';
	} else {
		$result = '{"result":"error","text":"Ошибка! Повторите попытку позже."}';
	}
}

print $result;

if (isset($_GET['ajax'])) {
	header('Content-Type:text/html; charset=UTF-8');
	exit();
}
