<?php
// v4
// 24.06.2016
// Antideer
// Для MODx Evo 1.1
//===========================================================================
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
  `link_attributes` varchar(255) NOT NULL DEFAULT '' COMMENT 'Link attriubtes',
  `published` int(1) NOT NULL DEFAULT '0',
  `pub_date` int(20) NOT NULL DEFAULT '0',
  `unpub_date` int(20) NOT NULL DEFAULT '0',
  `parent` int(10) NOT NULL DEFAULT '0',
  `isfolder` int(1) NOT NULL DEFAULT '0',
  `introtext` text COMMENT 'Used to provide quick summary of the document',
  `content` mediumtext,
  `richtext` tinyint(1) NOT NULL DEFAULT '1',
  `template` int(10) NOT NULL DEFAULT '0',
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
  `publishedon` int(20) NOT NULL DEFAULT '0' COMMENT 'Date the document was published',
  `publishedby` int(10) NOT NULL DEFAULT '0' COMMENT 'ID of user who published the document',
  `menutitle` varchar(255) NOT NULL DEFAULT '' COMMENT 'Menu title',
  `donthit` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Disable page hit count',
  `haskeywords` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'has links to keywords',
  `hasmetatags` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'has links to meta tags',
  `privateweb` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Private web document',
  `privatemgr` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Private manager document',
  `content_dispo` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-inline, 1-attachment',
  `hidemenu` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Hide document from menu',
  `alias_visible` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idm`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );


mysql_query( "
CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName( 'aaa_' . $tb_5 )." (
  `idm` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `tmplvarid` int(10) NOT NULL DEFAULT '0' COMMENT 'Template Variable id',
  `contentid` int(10) NOT NULL DEFAULT '0' COMMENT 'Site Content Id',
  `value` text,
  PRIMARY KEY (`idm`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );


mysql_query( "
CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName( 'aaa_' . $tb_2 )." (
  `idm` int(10) NOT NULL AUTO_INCREMENT,
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT 'Chunk',
  `editor_type` int(11) NOT NULL DEFAULT '0' COMMENT '0-plain text,1-rich text,2-code editor',
  `editor_name` varchar(50) NOT NULL DEFAULT 'none',
  `category` int(11) NOT NULL DEFAULT '0' COMMENT 'category id',
  `cache_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Cache option',
  `snippet` mediumtext,
  `locked` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idm`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );


mysql_query( "
CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName( 'aaa_' . $tb_3 )." (
  `idm` int(10) NOT NULL AUTO_INCREMENT,
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT 'Snippet',
  `editor_type` int(11) NOT NULL DEFAULT '0' COMMENT '0-plain text,1-rich text,2-code editor',
  `category` int(11) NOT NULL DEFAULT '0' COMMENT 'category id',
  `cache_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Cache option',
  `snippet` mediumtext,
  `locked` tinyint(4) NOT NULL DEFAULT '0',
  `properties` text COMMENT 'Default Properties',
  `moduleguid` varchar(32) NOT NULL DEFAULT '' COMMENT 'GUID of module from which to import shared parameters',
  PRIMARY KEY (`idm`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );


mysql_query( "
CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName( 'aaa_' . $tb_4 )." (
  `idm` int(10) NOT NULL AUTO_INCREMENT,
  `id` int(10) NOT NULL,
  `templatename` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT 'Template',
  `editor_type` int(11) NOT NULL DEFAULT '0' COMMENT '0-plain text,1-rich text,2-code editor',
  `category` int(11) NOT NULL DEFAULT '0' COMMENT 'category id',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT 'url to icon file',
  `template_type` int(11) NOT NULL DEFAULT '0' COMMENT '0-page,1-content',
  `content` mediumtext,
  `locked` tinyint(4) NOT NULL DEFAULT '0',
  `selectable` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idm`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );



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
?>
