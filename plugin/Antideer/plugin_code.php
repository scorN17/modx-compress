// - v3.00

$tb_1= 'site_content';
$tb_2= 'site_htmlsnippets';
$tb_3= 'site_snippets';
$tb_4= 'site_templates';
$tb_5= 'site_tmplvar_contentvalues';
$tb_6= 'site_tmplvars';

mysql_query( "
CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName( 'aaa_' . $tb_1 )." (
  `idm` int(10) NOT NULL AUTO_INCREMENT,
  `id` int(10) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'document',
  `contentType` varchar(50) NOT NULL DEFAULT 'text/html',
  `pagetitle` varchar(255) NOT NULL DEFAULT '',
  `longtitle` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) DEFAULT '',
  `link_attributes` varchar(255) NOT NULL DEFAULT '',
  `published` int(1) NOT NULL DEFAULT '0',
  `pub_date` int(20) NOT NULL DEFAULT '0',
  `unpub_date` int(20) NOT NULL DEFAULT '0',
  `parent` int(10) NOT NULL DEFAULT '0',
  `isfolder` int(1) NOT NULL DEFAULT '0',
  `introtext` text,
  `content` mediumtext,
  `richtext` tinyint(1) NOT NULL DEFAULT '1',
  `template` int(10) NOT NULL DEFAULT '1',
  `menuindex` int(10) NOT NULL DEFAULT '0',
  `searchable` int(1) NOT NULL DEFAULT '1',
  `cacheable` int(1) NOT NULL DEFAULT '1',
  `createdby` int(10) NOT NULL DEFAULT '0',
  `createdon` int(20) NOT NULL DEFAULT '0',
  `editedby` int(10) NOT NULL DEFAULT '0',
  `editedon` int(20) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  `deletedon` int(20) NOT NULL DEFAULT '0',
  `deletedby` int(10) NOT NULL DEFAULT '0',
  `publishedon` int(20) NOT NULL DEFAULT '0',
  `publishedby` int(10) NOT NULL DEFAULT '0',
  `menutitle` varchar(255) NOT NULL DEFAULT '',
  `donthit` tinyint(1) NOT NULL DEFAULT '0',
  `haskeywords` tinyint(1) NOT NULL DEFAULT '0',
  `hasmetatags` tinyint(1) NOT NULL DEFAULT '0',
  `privateweb` tinyint(1) NOT NULL DEFAULT '0',
  `privatemgr` tinyint(1) NOT NULL DEFAULT '0',
  `content_dispo` tinyint(1) NOT NULL DEFAULT '0',
  `hidemenu` tinyint(1) NOT NULL DEFAULT '0',
  `alias_visible` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idm`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;" );


mysql_query( "
CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName( 'aaa_' . $tb_5 )." (
  `idm` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `tmplvarid` int(10) NOT NULL DEFAULT '0',
  `contentid` int(10) NOT NULL DEFAULT '0',
  `value` text,
  PRIMARY KEY (`idm`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;" );


mysql_query( "
CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName( 'aaa_' . $tb_2 )." (
  `idm` int(10) NOT NULL AUTO_INCREMENT,
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT 'Chunk',
  `editor_type` int(11) NOT NULL DEFAULT '0',
  `category` int(11) NOT NULL DEFAULT '0',
  `cache_type` tinyint(1) NOT NULL DEFAULT '0',
  `snippet` mediumtext,
  `locked` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idm`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;" );


mysql_query( "
CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName( 'aaa_' . $tb_3 )." (
  `idm` int(10) NOT NULL AUTO_INCREMENT,
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT 'Snippet',
  `editor_type` int(11) NOT NULL DEFAULT '0',
  `category` int(11) NOT NULL DEFAULT '0',
  `cache_type` tinyint(1) NOT NULL DEFAULT '0',
  `snippet` mediumtext,
  `locked` tinyint(4) NOT NULL DEFAULT '0',
  `properties` text,
  `moduleguid` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`idm`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;" );


mysql_query( "
CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName( 'aaa_' . $tb_4 )." (
  `idm` int(10) NOT NULL AUTO_INCREMENT,
  `id` int(10) NOT NULL,
  `templatename` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT 'Template',
  `editor_type` int(11) NOT NULL DEFAULT '0',
  `category` int(11) NOT NULL DEFAULT '0',
  `icon` varchar(255) NOT NULL DEFAULT '',
  `template_type` int(11) NOT NULL DEFAULT '0',
  `content` mediumtext,
  `locked` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idm`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;" );



$e= &$modx->Event;
$mid= $e->params[ 'id' ];



if( $e->name == "OnBeforeDocFormSave" || $e->name == "OnBeforeDocFormDelete" ) // СТРАНИЦЫ
{
	mysql_query( "INSERT INTO ".$modx->getFullTableName( 'aaa_' . $tb_1 )."
		( `id`,`type`,`contentType`,`pagetitle`,`longtitle`,`description`,`alias`,
		`link_attributes`,`published`,`pub_date`,`unpub_date`,`parent`,`isfolder`,`introtext`,
		`content`,`richtext`,`template`,`menuindex`,`searchable`,`cacheable`,`createdby`,
		`createdon`,`editedby`,`editedon`,`deleted`,`deletedon`,`deletedby`,`publishedon`,
		`publishedby`,`menutitle`,`donthit`,`haskeywords`,`hasmetatags`,`privateweb`,`privatemgr`,`content_dispo`,`hidemenu`,`alias_visible` ) SELECT * FROM ".$modx->getFullTableName( $tb_1 )."
		WHERE id={$mid} LIMIT 1" );
	
	$rr= mysql_query( "SELECT id FROM ".$modx->getFullTableName( $tb_6 )." ORDER BY id" );
	
	if( $rr && mysql_num_rows( $rr ) > 0 )
	{
		while( $row= mysql_fetch_assoc( $rr ) )
		{
			mysql_query( "INSERT INTO ".$modx->getFullTableName( 'aaa_' . $tb_5 )." ( `id`, `tmplvarid`, `contentid`, `value` ) SELECT * FROM ".$modx->getFullTableName( $tb_5 )." WHERE contentid={$mid} AND tmplvarid={$row[id]}" );
		}
	}
	
	
	
//----------------------------
	
}elseif( $e->name == "OnBeforeChunkFormSave" || $e->name == "OnBeforeChunkFormDelete" ){ // ЧАНКИ

	mysql_query( "INSERT INTO ".$modx->getFullTableName( 'aaa_' . $tb_2 )." ( `id`, `name`, `description`, `editor_type`, `category`, `cache_type`, `snippet`, `locked` ) SELECT * FROM ".$modx->getFullTableName( $tb_2 )."
		WHERE id={$mid} LIMIT 1" );
	
	
	
//----------------------------
	
}elseif( $e->name == "OnBeforeSnipFormSave" || $e->name == "OnBeforeSnipFormDelete" ){ // СНИППЕТЫ

	mysql_query( "INSERT INTO ".$modx->getFullTableName( 'aaa_' . $tb_3 )." ( `id`, `name`, `description`, `editor_type`, `category`, `cache_type`, `snippet`, `locked`, `properties`, `moduleguid` ) SELECT * FROM ".$modx->getFullTableName( $tb_3 )."
		WHERE id={$mid} LIMIT 1" );

	
	
//----------------------------
	
}elseif( $e->name == "OnBeforeTempFormSave" || $e->name == "OnBeforeTempFormDelete" ){ // ШАБЛОНЫ

	mysql_query( "INSERT INTO ".$modx->getFullTableName( 'aaa_' . $tb_4 )." ( `id`, `templatename`, `description`, `editor_type`, `category`, `icon`, `template_type`, `content`, `locked` ) SELECT * FROM ".$modx->getFullTableName( $tb_4 )."
		WHERE id={$mid} LIMIT 1" );
}
