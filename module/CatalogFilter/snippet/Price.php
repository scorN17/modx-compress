<?php
/**
 * Price
 *
 * Цена
 *
 * @version     10.0
 *
 *
 *
 *
 */
	
if(empty($delimiter)) $delimiter= '&thinsp;';
if(empty($round)) $round= 0;
$price= str_replace(",", ".", $price);
$price= preg_replace("/[^0-9\.]/", "", $price);
$price= round($price, $round);
if($price<=0 || $price=='') return "&mdash;";
$tmp= explode(",", $price);
$itogo_price= '';
$ii= 0;
for($kk=strlen($tmp[0])-1; $kk>=0; $kk--)
{
	$ii++;
	$itogo_price= substr($tmp[0], $kk, 1).$itogo_price;
	if($ii%3==0 && $kk>0)
	{
		$itogo_price= $delimiter.$itogo_price;
	}
}
if($tmp[1]>0 || $print_if_nol_kopeek)
{
	if(strlen($tmp[1])<$round) $tmp[1]= str_pad($tmp[1], $round, '0', STR_PAD_RIGHT);
	if($tmp[1]) $itogo_price .= ','.$tmp[1];
}
return $itogo_price;
