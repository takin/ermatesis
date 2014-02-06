<?php

Class Application {

    private $_path;
    private $_controller;
    private $_method;
    private $_parameters = array();
    private $_core_location;
    private $_controller_location;

    function __construct() {

        // ambil parameter $_GET['url'] dan buang slash terakhir dengan menggunakan rtrim
        $rawurl = (isset($_GET['url'])) ? rtrim($_GET['url'], '/') : DEF_CONTROLLER;
        
        // pecah semua parameter dan masukkan menjadi array
        $url = explode('/', $rawurl);

        // masukkan lokasi core aplikasi ke dalam variabel $_core_location
        $this->_core_location = ROOT . DS . 'system' . DS . 'core' . DS;
        
        // masukkan lokasi Controller ke dalam variabel $_controller_location
        $this->_controller_location = ROOT . DS . 'app' . DS . 'controller' . DS;
        
        // masukkan nama controller ke dalam $_controller
        $this->_controller = array_shift($url);
        
        // masukkan nama method dari controller yang dipanggil ke dalam $_method
        $this->_method = array_shift($url);
        
        /*
         * part URL ke 3 dan seterusnya adalah merupakan parameter dari method
         * parameter ini dimasukkan ke dalam array $_parameter
         */
        $this->_parameters = array_merge($url);


        // cek apakah url yang dimasukkan ber-korelasi dengan sebuah Controller atau tidak
        if (file_exists($this->_controller_location . $this->_controller . '.php')) {

            //load semua kelas dalam folder system/core
            $this->loadCoreSystem($this->_controller);

            //instansiasi kontroller
            $this->_path = new $this->_controller;
            
            //instansiasi method
            if (isset($this->_method)) {

                // cek apakah terdapat nama method yang sama dengan yang dikirikan di URL
                // dengan yang ada di dalam kelas controller yang bersangkutan
                if(method_exists($this->_path, $this->_method)) {

                    // cek apakah ada parameter yang dilewatkan
                    (count($this->_parameters) > 0) ?
                    
                    // jika ada, maka panggil method yang bersangkutan dan lewatkan parameter yang diberikan
                    $this->_path->{$this->_method}($this->_parameters) :

                    // jika tidak, maka panggil method 
                    $this->_path->{$this->_method}();
                }
                
                /*
                 * jika method tidak diberikan di dalam URL, 
                 * maka secara default akan langsung memanggil method index dari DEF_CONTROLLER
                 */
            } else {
                $this->_path->index();
            }
        } 
    }

    private function loadCoreSystem($controller) {

        require_once $this->_core_location . 'Loader.php';

        // load base controller /system/core/Controller.php
        require_once $this->_core_location . 'Controller.php';
        
        // load controller yang dilewatkan di URL
        require_once $this->_controller_location . $controller . '.php';
        
        //require_once $this->_core_location . 'Loader.php'; -- tidak terpakai --
        
        /* 
         * load kelas template. Kelas ini dibutuhkan oleh kelas controller 
         * untuk melakukan instansiasi template.
         * setiap kali controller dipanggil, maka otomatis akan melakukan instansiasi Template
         */
        require_once $this->_core_location . 'Template.php';
    }

    public function stage($environment = 'live') {

        if ($environment == 'development') {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 'off');
        }
    }

}

//fungsi-fungsi yang dibutuhkan langsung tanpa harus melakukan instansiasi kelas
function redirect($location = '') {
    header("Location: " . basepath . $location);
}