<?php
/**
 * _LK_
 *
 * Личный Кабинет
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



if(isset($_GET['exit']))
{
	$_SESSION['webuserinfo']= array();
	header('location: '.$topage_url);
	exit();
}




if($_SESSION['webuserinfo']['auth'])
{
	$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_user')." WHERE id=".$_SESSION['webuserinfo']['id']." AND enabled='y' LIMIT 1");
	if($rr && $modx->db->getRecordCount($rr))
	{
		$_SESSION['webuserinfo']['info']= $modx->db->getRow($rr);
	}else{
		$_SESSION['webuserinfo']= array();
		header('location: '.$topage_url);
		exit();
	}
}




$p= '<style>
		[[Compress?&print=`true` &files=`template/css/lk.css`]]
	</style>
	<script src="https://www.google.com/recaptcha/api.js"></script>';
	


if($t != 'panel') $p= '<div class="_LK_wrapper">';

	
	
if($t=='Reg_Auth' || $t=='Reg' || $t=='Auth')
{
	if($_SESSION['webuserinfo']['auth'])
	{
		header('location: '.$modx->makeUrl($lk));
		exit();
	}
	
	if($t=='Reg_Auth' || $t=='Auth')
	{
		$p .= $modx->runSnippet('LK_Auth');	
	}
	
	if($t=='Reg_Auth' || $t=='Reg')
	{
		$p .= $modx->runSnippet('LK_Reg');	
	}
}

if($t == 'restore')
{
	$p .= $modx->runSnippet('LK_Restore');
}

if($t == 'lk')
{
	if( ! $_SESSION['webuserinfo']['auth'])
	{
		header('location: '.$modx->makeUrl($auth));
		exit();
	}
	
	$p .= $modx->runSnippet('LK_UserInfo');
}

if($t == 'panel')
{
	$p .= $modx->runSnippet('LK_UserPanel');
}

if($t == 'shoporders')
{
	if( ! $_SESSION['webuserinfo']['auth'])
	{
		header('location: '.$modx->makeUrl($lk));
		exit();
	}
	$p .= $modx->runSnippet('LK_ShopOrders');
}

	
	
if($t != 'panel') $p .= '<br></div>';




$modx->db->query("CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName('_user')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fio` varchar(127) NOT NULL,
  `email` varchar(127) NOT NULL,
  `phone` varchar(63) NOT NULL,
  `city` varchar(127) NOT NULL,
  `address` varchar(255) NOT NULL,
  `enabled` set('y','n') NOT NULL DEFAULT 'y',
  `dt_reg` bigint(20) NOT NULL,
  `dt_edit` bigint(20) NOT NULL,
  `password` varchar(63) NOT NULL,
  `password_new` varchar(63) NOT NULL,
  `password_dt` bigint(20) NOT NULL,
  `secret` varchar(63) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

// Удали AUTO_INCREMENT в двух местах в запросе снизу
// Удали PRIMARY KEY
$modx->db->query("CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName('_user_h')." (
  `id` int(11) NOT NULL,
  `fio` varchar(127) NOT NULL,
  `email` varchar(127) NOT NULL,
  `phone` varchar(63) NOT NULL,
  `city` varchar(127) NOT NULL,
  `address` varchar(255) NOT NULL,
  `enabled` set('y','n') NOT NULL DEFAULT 'y',
  `dt_reg` bigint(20) NOT NULL,
  `dt_edit` bigint(20) NOT NULL,
  `password` varchar(63) NOT NULL,
  `password_new` varchar(63) NOT NULL,
  `password_dt` bigint(20) NOT NULL,
  `secret` varchar(63) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;" );

return $p;
