<?php
//SuperPuperForms
//v003
//===============================================================================
/*
&form=`2`
&popup=`1;2`
&class=`className1;className21,className22`
[!SuperPuperForms? &form=`2`!]
[!SuperPuperForms? &popup=`1;2` &class=`className1;className21,className22`!]
*/
//
//
//
//
//
//
//===============================================================================
$js= 'superpuperforms/superpuperforms.js'; // Путь к файлу JS
$css= 'superpuperforms/superpuperforms.css'; // Путь к файлу CSS
$telefonchik__flag= false; // Прыгающий телефончик
$veriword__flag[ 1 ]= false; // Captcha 1-й формы
$veriword__flag[ 2 ]= false; // Captcha 2-й формы
//===============================================================================
//smtp//mail//default//
$mailtype= 'mail';

//КОМУ (через запятую)
$mailto= '';

//Видимые копии (через запятую)
$mailcc= false;

//Скрытые копии (через запятую)
$mailbcc= false;

//ОТ (если SMTP, то это поле - логин)
$mailfrom= '';

//Пароль от почты (если SMTP)
$mailpassw= '';
//Любимый киногерой: ----- //Секретный вопрос от почты

//Сервер SMTP (если SMTP)
$smtp= 'smtp.yandex.ru';

//Порт SMTP (если SMTP)
$smtpport= 465;

//SNIPPET SuperPuperForms //SNIPPET BasketOrder //MODULE scorn_orders //SNIPPET _LK_Restore_Password //SNIPPET _LK_Reg //MODULE scorn_subscription
//===============================================================================
//
//
//
//
//
//
//
//
//
//
//===============================================================================
include_once( MODX_MANAGER_PATH .'includes/controls/class.phpmailer.php' );
$popup= explode( ';', $popup );
$class= explode( ';', $class );
if( $popup ) foreach( $popup AS $key => $row ) if( intval( trim( $row ) ) ) $popup_forms[ intval( trim( $row ) ) ]= trim( $class[ $key ] );
$form= intval( trim( $form ) );
if( $form ) $form_flag= true;
//===============================================================================

if( $_GET[ 'act' ] == 'superpuperforms_captcha' )
{
	print '{"result":"ok","text":"superpuperforms/dmt_captcha/veriword.php?id='. addslashes( $_GET[ 'dmtcaptchaid' ] ) .'"}';
	if( isset( $_GET[ 'ajax' ] ) ){ exit(); }
}
if( $_GET[ 'act' ] == 'superpuperforms_send' )
{
	$spfs_formid= intval( $_POST[ 'spfs_formid' ] );
	$spfs_name= addslashes( trim( $_POST[ 'spfs_name' ] ) );
	$spfs_email= addslashes( trim( $_POST[ 'spfs_email' ] ) );
	$spfs_phone= addslashes( trim( $_POST[ 'spfs_phone' ] ) );
	$spfs_kogda= addslashes( trim( $_POST[ 'spfs_kogda' ] ) );
	$spfs_pageid= intval( $_POST[ 'spfs_pageid' ] );
	$spfs_text= addslashes( trim( $_POST[ 'spfs_text' ] ) );
	$spfs_text2= str_replace( "\r\n", "<br />", $spfs_text );
	
	if( $veriword__flag[ $spfs_formid ] && $_POST[ 'spfs_veriword' ] != $_SESSION[ 'DMTCaptcha' ][ 'superpuperforms_'.$spfs_formid ] )
	{
		$result= '{"result":"error","text":"Введен неверный текст с картинки!"}';
	}elseif( ! $spfs_email && ! $spfs_phone ){
		$result= '{"result":"error","text":"Необходимо указать контактные данные!"}';
	}
	if( ! $result )
	{
		$subject= ( $spfs_formid == 2 ? 'Заказ звонка' : 'Письмо' ) ." с сайта www.". $_SERVER[ 'HTTP_HOST' ];
		
		$message= '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>'. $subject .'</title></head><body><h2>'. $subject .'</h2>';
		
		if( $spfs_pageid ) $message .= '<p><b>Отправлено со страницы:</b> '. $modx->makeUrl( $spfs_pageid, '', '', 'full' ) .'</p>';
		
		if( $spfs_name ) $message .= '<p><b>Имя Отчество:</b> '. $spfs_name .'</p>';
		
		if( $spfs_email ) $message .= '<p><b>E-mail:</b> '. $spfs_email .'</p>';
		
		if( $spfs_phone ) $message .= '<p><b>Контактный телефон:</b> '. $spfs_phone .'</p>';
		
		if( $spfs_kogda ) $message .= '<p><b>Когда позвонить?</b> '. $spfs_kogda .'</p>';
		
		$message .= '<p><b>Дата и время сообщения:</b> '. date( 'd.m.Y - H:i' ) .'</p>';
		
		if( $spfs_text2 ) $message .= '<p><b>Сообщение:</b><br />'. $spfs_text2 .'</p>';
		
		$message .= '</body></html>';

// ============================================================================
					if( $mailtype == 'smtp' || $mailtype == 'mail' )
					{
						$phpmailer= new PHPMailer();
						if( false )
						{
							$phpmailer->SMTPDebug= 2;
							$phpmailer->Debugoutput = 'html';
						}
						if( $mailtype == 'smtp' )
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
						$phpmailer->FromName= "";
						$phpmailer->isHTML( true );
						$phpmailer->Subject= $subject;
						$phpmailer->Body= $message;
						$mailto= explode( ',', $mailto ); foreach( $mailto AS $row ) $phpmailer->addAddress( trim( $row ) );
						if( $mailcc ){ $mailcc= explode( ',', $mailcc ); foreach( $mailcc AS $row ) $phpmailer->addCC( trim( $row ) ); }
						if( $mailbcc ){ $mailbcc= explode( ',', $mailbcc ); foreach( $mailbcc AS $row ) $phpmailer->addBCC( trim( $row ) ); }
						$phpmailer_result= $phpmailer->send();
					}else{
						$headers= "Content-type: text/html; charset=utf-8\n";
						$headers .= "From: <". $mailfrom .">\n";
						$phpmailer_result= mail( $mailto, $subject, $message, $headers );
					}
// ============================================================================	
		
		if( $phpmailer_result )
		{
			$result= '{"result":"ok","text":"Сообщение успешно отправлено!<br />С Вами свяжется наш специалист."}';
		}else{
			$result= '{"result":"error","text":"Ошибка сервера! Повторите попытку позже."}';
		}
	}
	print $result;
	if( isset( $_GET[ 'ajax' ] ) ){ header( 'Content-Type:text/html; charset=UTF-8' ); exit(); }
}
?>
<link rel="stylesheet" type="text/css" href="<?= $css ?>" />
<script type="text/javascript" src="<?= $js ?>"></script>
<script type="text/javascript">
$(document).ready(function(){
<?php
	if( ! $form_flag )
	{
		foreach( $popup_forms AS $formId => $className )
		{
			$className= str_replace( ',', ', .', $className );
			print '$( ".'. $className .'" ).click(function(){ superpuperforms_show( '. $formId .' ); return false; });';
		}
	}
?>
});
</script>

<?php if( $telefonchik__flag && ! $form_flag ){ ?>
	<div class="superpuperforms_wrapper superpuperforms_wrapper_krzhk"><div class="spfs_krzhk">&nbsp;</div></div>
<?php } ?>

<div class="superpuperforms_wrapper <?=( ! $form_flag ? 'superpuperforms_wrapper_popup' : 'superpuperforms_wrapper_default' )?>">
	<?php if( ! $form_flag ){ ?>
	<div class="spfs_black"><div class="spfs_white"><div class="spfs_krestik"><span>Закрыть</span></div>
	<?php } ?>
	
	<?php if( ( $form_flag && $form == 1 ) || ( ! $form_flag && $popup_forms[ 1 ] ) ){ ?>
		<div class="spfs_formwrapper spfs_formwrapper_1 <?=( ! $form_flag ? 'spfs_formwrapper_popup' : '' )?>" data-formid="1">
			<?=( $form_flag ? '<div class="spfs_label"> </div>' : '' )?><div class="spfs_tit">Напишите нам</div><div class="clr">&nbsp;</div>
			<div class="spfs_result"></div>
			<form action="<?= $modx->makeUrl( $modx->documentIdentifier ); ?>" method="post">
				<div class="spfs_label">Имя Отчество:</div><div class="spfs_input"><input class="form_elem" type="text" name="spfs_name" /></div>
				<div class="clr">&nbsp;</div>
				<div class="spfs_label"><?=( $form_flag ? 'Адрес электронной почты' : 'E-mail' )?>:<div class="zvd">*</div></div><div class="spfs_input"><input class="form_elem" type="text" name="spfs_email" /></div>
				<div class="clr">&nbsp;</div>
				<div class="spfs_label">Номер телефона:<div class="zvd">*</div></div><div class="spfs_input"><input class="form_elem" type="text" name="spfs_phone" /></div>
				<div class="clr">&nbsp;</div>
				<div class="spfs_label">Сообщение:</div><div class="spfs_input">
					<textarea class="form_elem" name="spfs_text"><?php print $mail_text;?></textarea>
				</div>
				<div class="clr">&nbsp;</div>
				<?php if( $veriword__flag[ 1 ] ){ ?>
				<div class="spfs_captcha">
					<div class="spfs_label spfs_br"><img src="superpuperforms/dmt_captcha/veriword.php?id=superpuperforms_1" /><div class="spfs_change">Изменить число</div><div class="zvd">*</div></div>
					<div class="spfs_input spfs_br">Введите текст с картинки:<br /><input class="form_elem" type="text" name="spfs_veriword" /></div>
					<div class="clr">&nbsp;</div>
				</div>
				<?php } ?>
				<div class="spfs_label"><div class="zvd">*</div></div><div class="spfs_input">- поля обязательные для заполнения!</div>
				<div class="clr">&nbsp;</div>
				<input class="spfs_pageid" type="hidden" name="spfs_pageid" value="[*id*]" />
				<input type="hidden" name="spfs_formid" value="1" />
				<div class="spfs_label"> </div><div class="spfs_input"><button class="spfs_submit" type="button">Отправить сообщение</button></div>
				<div class="clr">&nbsp;</div>
			</form>
		</div>
	<?php } ?>
		
		
		
		
		
	<?php if( ( $form_flag && $form == 2 ) || ( ! $form_flag && $popup_forms[ 2 ] ) ){ ?>
		<div class="spfs_formwrapper spfs_formwrapper_2 <?=( ! $form_flag ? 'spfs_formwrapper_popup' : '' )?>" data-formid="2">
			<?=( $form_flag ? '<div class="spfs_label"> </div>' : '' )?><div class="spfs_tit">Заказ обратного звонка</div>
			<div class="spfs_result"></div>
			<form action="<?= $modx->makeUrl( $modx->documentIdentifier ); ?>" method="post">
				<div class="spfs_label">Имя Отчество:</div><div class="spfs_input"><input class="form_elem" type="text" name="spfs_name" /></div>
				<div class="clr">&nbsp;</div>
				<div class="spfs_label">Номер телефона:<div class="spfs_txt">с кодом города</div></div><div class="spfs_input"><input class="form_elem" type="text" name="spfs_phone" /></div>
				<div class="clr">&nbsp;</div>
				<div class="spfs_label">Когда позвонить?</div><div class="spfs_input"><input class="form_elem" type="text" name="spfs_kogda" /></div>
				<div class="clr">&nbsp;</div>
				<?php if( $veriword__flag[ 2 ] ){ ?>
				<div class="spfs_captcha">
					<div class="spfs_label spfs_br"><img src="superpuperforms/dmt_captcha/veriword.php?id=superpuperforms_2" /><div class="spfs_change">Изменить число</div></div>
					<div class="spfs_input spfs_br">Введите текст с картинки:<br /><input class="form_elem" type="text" name="spfs_veriword" /></div>
					<div class="clr">&nbsp;</div>
				</div>
				<?php } ?>
				<input class="spfs_pageid" type="hidden" name="spfs_pageid" value="[~[*id*]~]" />
				<input type="hidden" name="spfs_formid" value="2" />
				<div class="spfs_label"> </div><div class="spfs_input"><button class="spfs_submit" type="button">Отправить заявку</button></div>
				<div class="clr">&nbsp;</div>
			</form>
		</div>
	<?php } ?>
		
	<?php if( ! $form_flag ){ ?>
	</div></div>
	<?php } ?>
</div>
<?php
//
?>
