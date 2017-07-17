<?php
/**
 * searchForm
 *
 *
 *
 *
 *
 *
 *
 */
	
$modx->db->query("CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName('_search')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `search` varchar(255) NOT NULL,
  `dth` varchar(31) NOT NULL,
  `dt` bigint(20) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `user` varchar(63) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	
$search= $modx->db->escape(trim($_GET['search']));
$modx->db->query("INSERT INTO ".$modx->getFullTableName('_search')." SET
	search='{$search}',
	ip='".$modx->db->escape($_SERVER['REMOTE_ADDR'])."',
	user='".$modx->db->escape(session_name().'='.session_id())."',
	dth='".date('Y-m-d-H-i')."', dt=".time());

$pp .= '<form action="'.$modx->makeUrl($modx->documentIdentifier).'" method="get">
	<input type="text" name="search" value="'.$_GET['search'].'" />
	<input type="submit" value="Искать" />
</form><p>&nbsp;</p>';
return $pp;
