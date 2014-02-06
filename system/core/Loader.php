<?php

/*
 * Kelas untuk melakukan loading (instansiasi) terhadap semua komponen aplikasi yang dibutuhkan 
 * controller, model, view , template dll
 * created by Syamsul Muttaqin @c 2013
 */

class Loader {

    private $_core_location;
    private $_model_location;
    private $_controller_location;

    function __construct() {
        $this->_core_location = ROOT . DS . 'system' . DS . 'core' . DS;
        $this->_model_location = ROOT . DS . 'app' . DS . 'model' . DS;
        $this->_controller_location = ROOT . DS . 'app' . DS . 'controller' . DS;
    }

    public function model($modelName, $modelAlias = null) {

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

}
