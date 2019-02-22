<?php
$modx->getService('commerce','commerce',MODX_CORE_PATH.'components/commerce/model/commerce/');
$directory = $modx->getOption('directory',$scriptProperties,'components/commerce/src/Modules/Pure360/');
$dir = MODX_CORE_PATH.$directory;
$namespace = $modx->getOption('namespace',$scriptProperties,"modmore\Commerce\Modules\Pure360\\");
$register = $modx->commerce->loadModulesFromDirectory($dir,$namespace,$dir);
return $register;