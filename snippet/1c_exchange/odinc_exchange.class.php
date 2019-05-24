<?php
/**
 * 1C:Exchange
 *
 * @class odinc_exchange
 *
 * Класс выгрузки из 1С
 *
 * @version 5.0
 * @date    23.05.2019
 *
 *
 */

/*
	Статусы:
	downloading
	- файлы выгружаются из 1С

	downloaded
	- файлы выгружены из 1С

	parsing
	- инфа из файлов выгружается в базу данных сайта

	completed
	- выгрузка файла завершена

	canceled
	- отмена
*/

class odinc_exchange
{
	private $modx;
	private $db;
	private $droot;

	private $c;

	private $tb_files;
	private $tb_log;

	public $dir_f;
	public $dir_d;

	public $session_id;
	public $session_name;

	public $efile;
	public $xmlf;

	public $microtimestart = 0;

	function __construct(&$modx, $droot)
	{
		if ( ! $modx instanceof DocumentParser) {
			return false;
		}

		$this->modx  = $modx;
		$this->db    = $modx->db;
		$this->droot = $droot;

		$this->tb_files = $this->modx->getFullTableName('_1c_');
		$this->tb_log   = $this->modx->getFullTableName('_1c_log');

		return true;
	}

	function init($config)
	{
		$this->c = $config;

		$this->db->query("CREATE TABLE
			IF NOT EXISTS {$this->tb_log} (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`dth` varchar(31) NOT NULL,
			`log` text NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

		$this->db->query("CREATE TABLE
			IF NOT EXISTS {$this->tb_files} (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`session` varchar(127) NOT NULL,
			`file` varchar(127) NOT NULL,
			`type` set('import','offers') NOT NULL DEFAULT 'import',
			`status` set('from_1c','new','to_db','complete','cancel') NOT NULL DEFAULT 'from_1c',
			`step` tinyint(4) NOT NULL DEFAULT '1',
			`point` int(11) NOT NULL DEFAULT '0',
			`dth` varchar(31) NOT NULL,
			`status_dth` varchar(31) NOT NULL,
			`cancel_dth` varchar(31) NOT NULL,
			`dt` bigint(20) NOT NULL,
			`status_dt` bigint(20) NOT NULL,
			`cancel_dt` bigint(20) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

		$this->dir_f = $this->c['dir'].'files/';
		$this->dir_d = date('Y-m').'/';
		if ( ! file_exists($this->droot.$this->dir_f.$this->dir_d)) {
			mkdir($this->droot.$this->dir_f.$this->dir_d, 0777, true);
		}
	}

	function goods()
	{
		$this->limit_control('start');

		$xml = &$this->xmlf;

		$flag = false;
		while ($xml->read()) {
			if ($xml->nodeType !== 1 || $xml->name != 'Товары') continue;
			$xml->read();
			$flag = true;
			break;
		}
		if ( ! $flag) return false;

		$point = $this->efile['point'];

		$count = 0;
		$ii    = 0;

		while ($xml->next()) {
			if ($xml->nodeType !== 1) continue;
			if ($xml->name != 'Товар') break;

			$ii++;
			if ($ii <= $point) continue;
			$count++;

			$item = $xml->readOuterXml();
			if ( ! $item) continue;
			$sxml = new SimpleXmlIterator($item);
			if ( ! $sxml) continue;

			$this->good_save($sxml);

			// $res = $this->limit_control('check');
		}

		if ($count) {
			$this->db->query("UPDATE {$this->tb_files}
				SET point='".($point+$count)."' WHERE id={$this->efile['id']} LIMIT 1");
		} else {
			$this->db->query("UPDATE {$this->tb_files}
				SET status='completed' WHERE id={$this->efile['id']} LIMIT 1");
		}

		return true;
	}

	function groups()
	{
		$this->limit_control('start');

		$xml = &$this->xmlf;

		$groups = false;
		while ($xml->read()) {
			if ($xml->nodeType !== 1 || $xml->name != 'Группы') continue;
			$groups = $xml->readOuterXml();
			break;
		}
		if ( ! $groups) return false;
		$sxml = new SimpleXmlIterator($groups);
		if ( ! $sxml) return false;

		$res = $this->group_save($sxml);
		if ( ! $res) return false;

		$this->db->query("UPDATE {$this->tb_files}
			SET step='2' WHERE id={$this->efile['id']} LIMIT 1");

		return true;
	}

	function get_file()
	{
		$efile = false;
		$res = $this->db->query("SELECT *
			FROM {$this->tb_files}
			WHERE status='downloaded' OR status='parsing'
			ORDER BY IF(status='parsing',0,1), IF(type='import',0,1), dt DESC LIMIT 1");
		if ($res && $this->db->getRecordCount($res)) {
			$efile = $this->db->getRow($res);
		}
		if ( ! $efile) return false;
		$this->efile = $efile;
		$xmlf = new XMLReader();
		$res = $xmlf->open($this->droot.$efile['file']);
		if ($res) $this->xmlf = $xmlf;
		return $res ? true : false;
	}

	function savefiledata($filename, $filedata)
	{
		if ( ! $filedata) {
			$this->log('error::04');
			return false;
		}

		$filename = trim($filename);

		$ext = substr($filename, strrpos($filename,'.'));
		if ($ext == '.xml') {
			$filename = date('Y-m-d-H-i-s').'__'.$filename;
		}

		$filestep = $this->s('step', $filename);

		if ($filestep < 2) {
			if ($ext == '.xml') {
				$filetype = strpos($filename,'offers') !== false ? 'offers' : 'import';
		
				if ($filetype == 'import') {
					$this->db->query("UPDATE
						{$this->tb_files} SET
						status     = 'canceled',
						cancel_dth = '".date('Y-m-d-H-i-s')."',
						cancel_dt  = ".time()."
						WHERE status<>'completed' AND status<>'canceled'
					");
				}

				$file = $this->dir_f.$this->dir_d.$filename;

				if (file_exists($this->droot.$file)) {
					return false;
				}

				$file_q = $this->db->escape($file);
				$this->db->query("INSERT
					INTO {$this->tb_files} SET
					file   = '{$file_q}',
					type   = '{$filetype}',
					status = 'downloading',
					dth    = '".date('Y-m-d-H-i-s')."',
					dt     = ".time().",
					sess   = '{$this->session_id}'
				");

				$this->s('step', $filename, 2);
				$this->s('filepath', $filename, $file);

			} else {
				$filename2 = md5($filename).$ext;

				$file = 'assets/';
				$file .= $ext=='.txt' ? 'files' : 'images';
				$file .= '/1c/';
				$file .= 'x'.substr($filename2,0,2).'/';
				$file .= $filename2;
				
				if (file_exists($this->droot.$file)) {
					unlink($this->droot. $file);
				} else {
					mkdir($this->droot.$file,0777,true);
				}

				$this->s('step', $filename, 2);
				$this->s('filepath', $filename, $file);
			}

			$this->log('catalog::file::01::'.$file);

		} else {
			$file = $this->s('filepath', $filename);

			$this->log('catalog::file::02::'.$file);
		}

		if ( ! $file) return false;

		$fh = fopen($this->droot.$file, 'ab');
		if ($fh === false) {
			$this->log('error::05');
			return false;
		}

		$byte = fwrite($fh, $filedata);
		if ( ! $byte) {
			$this->log('error::06');
			return false;
		}

		return true;
	}

	function import_init()
	{
		$step = $this->s('step');
		if ($step >= 2) {
			$this->log('error::07');
			return false;
		}

		$res = $this->db->query("UPDATE
			{$this->tb_files} SET
			status     = 'downloaded',
			status_dth = '".date('Y-m-d-H-i-s')."',
			status_dt  = ".time()."
			WHERE status = 'downloading'
		");

		if ( ! $res) {
			$this->log('error::08');
			return false;
		}

		$this->s('step', false, 2);

		return true;
	}

	function s($field, $file=false, $value=false)
	{
		if ($file) $s = &$_SESSION['1c_exchange'][$file];
		else $s = &$_SESSION['1c_exchange'];
		if ($value) {
			$s[$field] = $value;
		} else {
			return $s[$field];
		}
	}

	function log($txt)
	{
		$txt = $this->db->escape($txt);
		$this->db->query("INSERT INTO {$this->tb_log} SET
			dth  = '".date('Y-m-d-H-i-s')."',
			log  = '{$txt}',
			sess = '".$this->session_id."'
		");
	}

	function sess($id, $name)
	{
		$this->session_id   = $id;
		$this->session_name = $name;
	}

	function c($nm)
	{
		return $this->c[$nm];
	}

	function limit_control($type='start')
	{
		if ('start' == $type) {
			$this->microtimestart = microtime(true);
			return;
		}
	}
}
