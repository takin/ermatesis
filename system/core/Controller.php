<?php

/*
 * Controller utama
 * semua kontroller yang ada di dalam direktori app/controller 
 * harus meng-extends kontroller utama ini
 * 
 * kontroller yang di definisikan sebagai DEF_CONTROLLER dalam file system/config/config.php
 * harus memiliki method dengan nama index() karena method inilah yang secara otomatis akan dituju sebagai hompage
 * 
 * created by syamsul muttaqin, ST.
 * 17 Juni 2013
 */


class Controller {
    
    protected $_core_location;
    protected $_model_location;
    protected $_controller_location;
    protected $module = array('sidebar'=>null,'mainbody'=>null);
    protected $load;
    
    function __construct() {
        
        $this->template = new Template();
        
        $this->load = new Loader();

        $this->_core_location       = ROOT . DS . 'system' . DS . 'core' . DS;
        $this->_model_location      = ROOT . DS . 'app' . DS . 'model' . DS;
        $this->_controller_location = ROOT . DS . 'app' . DS . 'controller' . DS;
    }

    protected function is_ajax_request() {
        if( array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) ) {
            return ( $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') ? TRUE : FALSE; 
        }
        return FALSE;
    }
    
    public function loadModel($modelName, $modelAlias = null) {

        if (file_exists($this->_model_location . $modelName . '.php')) {
            require_once $this->_core_location . 'Model.php';
            require_once $this->_core_location . 'Database.php';
            require_once $this->_model_location . $modelName . '.php';
            $modelName = ucwords($modelName);
            if (empty($modelAlias)) {
                $this->{$modelName} = new $modelName;
            } else {
                $this->{$modelAlias} = new $modelName;
            }
        }
    }
    
    public function loadHelper($helperName, $helperAlias = null) {

        if (file_exists($this->_helper_location . $helperName . '.php')) {
            require_once $this->_helper_location . $helperName . '.php';
            $helperName = ucwords($helperName);
            if (empty($helperAlias)) {
                $this->{$helperName} = new $helperName;
            } else {
                $this->{$helperAlias} = new $helperName;
            }
        }
    }

    public function loadView($view, $data)
    {
        foreach ($data as $key => $value) 
        {
            $$key = $value;
        }
        include ROOT . DS . 'app' .DS. 'views'.DS.$view.'.php';
    }

}