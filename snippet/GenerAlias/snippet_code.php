<?php
/**
 * GenerAlias
 *
 * Генерация уникального псевдонима
 *
 * @version 4.0
 * @date    15.06.2017
 *
 *
 *
 *
 *
 *
 *
 *
 */
	
$trans= array("а"=>"a", "б"=>"b", "в"=>"v", "г"=>"g", "д"=>"d", "е"=>"e",
				   "ё"=>"jo", "ж"=>"zh", "з"=>"z", "и"=>"i", "й"=>"jj", "к"=>"k", "л"=>"l",
				   "м"=>"m", "н"=>"n", "о"=>"o", "п"=>"p", "р"=>"r", "с"=>"s", "т"=>"t", "у"=>"u",
				   "ф"=>"f", "х"=>"kh", "ц"=>"c", "ч"=>"ch", "ш"=>"sh", "щ"=>"shh", "ы"=>"y",
				   "э"=>"eh", "ю"=>"yu", "я"=>"ya", "А"=>"a", "Б"=>"b", "В"=>"v", "Г"=>"g",
				   "Д"=>"d", "Е"=>"e", "Ё"=>"jo", "Ж"=>"zh", "З"=>"z", "И"=>"i", "Й"=>"jj",
				   "К"=>"k", "Л"=>"l", "М"=>"m", "Н"=>"n", "О"=>"o", "П"=>"p", "Р"=>"r", "С"=>"s",
				   "Т"=>"t", "У"=>"u", "Ф"=>"f", "Х"=>"kh", "Ц"=>"c", "Ч"=>"ch", "Ш"=>"sh",
				   "Щ"=>"shh", "Ы"=>"y", "Э"=>"eh", "Ю"=>"yu", "Я"=>"ya", " "=>"-", "."=>"-",
				   ","=>"-", "_"=>"-", "+"=>"-", ":"=>"-", ";"=>"-", "!"=>"-", "?"=>"-");
$max= intval($max);
if( ! $max) $max= 20;
$alias= strip_tags(strtr($alias,$trans));
$alias= strtolower($alias);
$alias= preg_replace("/[^a-zA-Z0-9-]/", "", $alias);
$alias= preg_replace("/([-]){2,}/", '\1', $alias);
$alias= trim($alias, '-');
if(strlen($alias)>$max) $alias= trim(substr($alias,0,$max), '-');

do{
	$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_content') ." WHERE alias='{$alias}' LIMIT 1");
	if($rr && $modx->db->getRecordCount($rr)) $alias .= rand(1,9);

}while(($rr && $modx->db->getRecordCount($rr)));

if( ! $rr) $alias= false;

return $alias;
