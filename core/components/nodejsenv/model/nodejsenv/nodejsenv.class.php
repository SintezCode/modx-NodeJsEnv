<?php

class NodeJsEnv
{
    const NAMESPACE='nodejsenv';
    public $modx;
    public $authenticated = false;
    public $errors = array();

    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;
        
        $localPath='components/'.static::NAMESPACE.'/';
        $corePath = $this->modx->getOption(static::NAMESPACE.'.core_path', $config, $this->modx->getOption('core_path') . $localPath);
        $assetsPath = $this->modx->getOption(static::NAMESPACE.'.assets_path', $config, $this->modx->getOption('assets_path') . $localPath);
        $assetsUrl = $this->modx->getOption(static::NAMESPACE.'.assets_url', $config, $this->modx->getOption('assets_url') . $localPath);
        $connectorUrl = $assetsUrl . 'connector.php';
        $context_path = $this->modx->context->get('key')=='mgr'?'mgr':'web';

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . $context_path . '/css/',
            'jsUrl' => $assetsUrl . $context_path . '/js/',
            'jsPath' => $assetsPath . $context_path . '/js/',
            'imagesUrl' => $assetsUrl . $context_path . '/img/',
            'connectorUrl' => $connectorUrl,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',
            'projectsPath' => $corePath . 'projects/'
        ), $config);

        $this->modx->lexicon->load(static::NAMESPACE.':default');
        $this->authenticated = $this->modx->user->isAuthenticated($this->modx->context->get('key'));
        spl_autoload_register(array($this,'autoload'));
    }

    public function initialize($scriptProperties = array(),$ctx = 'web')
    {
        $this->config['options'] = $scriptProperties;
        $this->config['ctx'] = $ctx;
        return true;
    }
    
    public function autoload($class){
        $class = explode('/',str_replace("\\", "/", $class));
        $className = array_pop($class);
        $classPath = strtolower(implode('/',$class));
        
        $path = $this->config['modelPath'].'/'.$classPath.'/'.$className.'.php';
        if(!file_exists($path))return false;
        include $path;
    }
    
    public function loadAssets($ctx){
        if(!$this->modx->controller)return false;
        $this->modx->controller->addLexiconTopic(static::NAMESPACE.':default');
        switch($ctx){
            case 'mgr':{
                $this->modx->controller->addJavascript($this->config['assetsUrl'].'mgr/js/'.static::NAMESPACE.'.js');
            }
        }
    }
}
