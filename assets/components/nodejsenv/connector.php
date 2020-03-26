<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('nodejsenv.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/nodejsenv/');
$nodejsenv = $modx->getService(
    'nodejsenv',
    'NodeJsEnv',
    $corePath . 'model/nodejsenv/',
    array(
        'core_path' => $corePath
    )
);

/* handle request */
$path = $modx->getOption('processorsPath',$nodejsenv->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
