<?php

class Template {

    private $_template;

    function __construct() {

    }

    public function set($template)
    {
        $arr = explode('.', $template);
        $this->_template = $arr[0] . '.php';
    }

    public function view($view, $data = NULL, $return = TRUE)
    {
        $view_file = explode('.', $view);
        $_view = $view_file[0] . '.php';
        ob_start();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $$key = $value;
            }
        }
        
        include_once(ROOT . DS . 'app' . DS . 'views' . DS . $_view);
        
        if( $return === TRUE ) {
            $rendered_view = ob_get_contents();
            ob_get_clean();
            return $rendered_view;
        }
        return TRUE;
    }

    public function render($data = NULL) {

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $$key = $value;
            }
        }

        if (file_exists(ROOT . DS . 'app' . DS . 'views' . DS . $this->_template)) {
            require_once ROOT . DS . 'app' . DS . 'views' . DS . $this->_template;
        } 
        else {
            echo "file template tidak ditemukan!";
        }
    }

}