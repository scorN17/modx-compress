<?php
//v002
//GetLvl
//================== Определение уровня вложенности ====================
	if( $id == $koren )
	{
		print '1';
	
	}else{
		$lvl= 2;
		$doc= $modx->getDocument( $id, 'parent' );
		
		while( $doc[ 'parent' ] != $koren && $doc[ 'parent' ] != 0 )
		{
			$lvl++;
			$doc= $modx->getDocument( $doc[ 'parent' ], 'parent' );
		}
		
		if( $koren != 0 && $doc[ 'parent' ] == 0 )
		{
			print '0';
		}else{
			print $lvl;	
		}
	}
//====================================================================
?>
