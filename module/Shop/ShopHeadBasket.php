<?php
$cc= $modx->runSnippet('ShopAction',array('act'=>'items_count'));
return '<div class="shb_cc" '.($cc>=1 ? '' : 'style="display:none;"').'>'.$cc.'</div>';
