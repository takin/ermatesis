<?php

class Model {

    protected $db;

    function __construct() {
        $this->db = new Database();
        $this->session = new Session();
        //(array_key_exists('logged_in', $_SESSION)) ? null : $this->session = new Session();
    }

    protected function message($action = null, $ack = null) {
        switch ($action) {
            case "edit" : $message = "Edit data ";
                break;
            case "add" : $message = "Tambah data ";
                break;
            case "delete" : $message = "Hapus data ";
                break;
            case "publish" : $message = "Publish Artikel ";
                break;
            case "unpubllish" : $message = "Unpublish Artikel ";
                break;
            default : $message = "Proses ";
                break;
        }
        switch ($ack) {
            case 0 : $result = "Gagal!";
                break;
            case 1 : $result = "Sukses!";
                break;
            default: "";
                break;
        }
        return $message . $result;
    }

}