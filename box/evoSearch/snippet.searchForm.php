<?php
mysql_query("CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName('_search')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `search` varchar(255) CHARACTER SET utf8 NOT NULL,
  `dth` varchar(31) CHARACTER SET utf8 NOT NULL,
  `dt` bigint(20) NOT NULL,
  `user` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;");

$search= mysql_real_escape_string(trim($_GET['search']));
mysql_query("INSERT INTO ".$modx->getFullTableName('_search')." SET search='{$search}', user='".mysql_real_escape_string(session_name().'='.session_id())."', dth='".date('Y-m-d-H-i')."', dt=".time());

$pp .= '<form action="'.$modx->makeUrl($modx->documentIdentifier).'" method="get">
	<input type="text" name="search" value="'.$_GET['search'].'" />
	<input type="submit" value="Искать" />
</form><p>&nbsp;</p>';
return $pp;
?>
