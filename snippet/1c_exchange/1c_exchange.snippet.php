<?php
/**
 * 1c_exchange
 *
 * Выгрузка данных из 1С
 *
 * 1C:Exchange
 *
 * @version 5.0-dev
 * @date    23.05.2019
 *
 *
 */
	
$config = array(
	'dir' => '1c_exchange/',
	
	'file_limit' => (1024*1024*5),
	
	'catalog_root'     => 4,
	'catalog_template' => 6,
	'tv_1cid'          => 13,
	'tv_images'        => 6,
	'tv_article'       => 9,
	'tv_price'         => 12,
	'tv_in_stock'      => 11,
	'pricetype'        => 'fadd4aa8-a5bc-11e3-a9a5-00115b889893',
);
	
// -----------------------------------------------------------
	
include_once(MODX_BASE_PATH.$config['dir'].'odinc_exchange.class.php');

$oc = new odinc_exchange($modx, MODX_BASE_PATH);
	
// -----------------------------------------------------------

if ($cron == 'y') {
	if ( ! $oc) exit();
	
	$res = $oc->get_file();
	if ( ! $res) exit();
	
	if ('import' == $oc->efile['type']) {
		if ($oc->efile['step'] === '1') {
			$res = $oc->groups();
			if ( ! $res) exit();
			exit();
		}
		
		if ($oc->efile['step'] === '2') {
			$res = $oc->goods();
			if ( ! $res) exit();
			exit();
		}
		
		exit();
	}
	
	exit();
}
	
// -----------------------------------------------------------

if ( ! $oc) exit('failure'."\n");

$oc->init($config);

$oc->sess(session_id(), session_name());
	
// -----------------------------------------------------------

if ('catalog' == $_GET['type']) {
	if ('checkauth' == $_GET['mode']) {
		print 'success'."\n";
		print $oc->session_name."\n";
		print $oc->session_id."\n";
		
		$oc->log('catalog::checkauth');
		exit();
	}
	
	if ('init' == $_GET['mode']) {
		print "zip=no\n";
		print "file_limit=".$oc->c('file_limit')."\n";
		
		$oc->log('catalog::init');
		exit();
	}
	
	if ('file' == $_GET['mode'] && $_GET['filename']) {
		$filename = $_GET['filename'];
		$filedata = file_get_contents("php://input");
		
		$oc->log('catalog::file::'.$filename);
		
		$res = $oc->savefiledata($filename, $filedata);
		
		if ( ! $res) {
			exit('failure'."\n");
		}
		
		$oc->log('catalog::file::ok::'.$filename);
		exit('success'."\n");
	}
	
	if ('import' == $_GET['mode']) {
		$oc->log('catalog::import');
		$res = $oc->import_init();
		if ( ! $res) {
			exit('failure'."\n");
		}
		$oc->log('catalog::import::ok');
		exit('success'."\n");
	}
	
	$oc->log('error::02');
	exit('failure'."\n");
}

$oc->log('error::01');
exit('failure'."\n");
