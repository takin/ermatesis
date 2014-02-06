<?php

/*
 * Core class untuk koneksi dengan database
 * konfigurasi user,pass dan host ada di core/config.php
 * created by Syamsul Muttaqin
 */

class Database {

    // atribut untuk standar pengiriman data ke view
    protected $data = array();
    private $connection;
    private static $connector;
    private $last_query;
    private $magic_quote_active;
    private $real_escape_string;
    //khusus untuk koneksi dengan konektor mysql
    private $dbName;

    function __construct() {

        //nilai DB_CONNECTOR sesuai dengan settingan yang ada di file system/config/config.php
        self::$connector = DB_CONNECTOR;
        $this->open();
        /*
         * cek apakah fungsi escape string aktif 
         * (fungsi khusus ada di PHP versi 4.2 ke atas)
         * jika ada, variabel $real_escape_string = true
         */
        $this->real_escape_string = function_exists("mysql_real_escape_string");
        /*
         * Cek apakah fungsi magic quote aktif atau tidak
         * jika aktif maka diisi ke variabel $magic_quote_active = true
         */
        $this->magic_quote_active = get_magic_quotes_gpc();
    }

    private function confirm($result) {
        $err = (self::$connector == 'mysqli') ? $this->connection->connect_errno : mysql_errno();
        if ($result === false) {
            $message = "Query Gagal -> " . $err . "<br/>";
            $message .= "Query: " . $this->last_query;
            exit($message);
        }
    }

    private function open() {

        /* konfigurasi DB_HOST, DB_USER, DB_PASS, DB_NAME ada di system/config/config.php  */

        //jika DB_CONNECTOR menggunakan mysqli
        if (self::$connector == 'mysqli') {

            //ciptakan objek koneksi dengan database
            $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            //cek apakah proses koneksi dengan database berhasil atau tidak
            $this->connection->connect_errno ? exit('koneksi gagal') : null;

            // jika DB_CONNECTOR menggunakan mysql biasa
        } else if (self::$connector == 'mysql') {

            // proses koneksi tanpa menggunakan model OOP
            $this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASS);

            //cek apakah koneksi dengan database berhasil atau tidak
            mysql_errno() ? exit('koneksi gagal') : null;

            // pilih database yang akan digunakan
            $this->dbName = mysql_selectdb(DB_NAME, $this->connection);
        } else {

            //jika setting DB_CONNECTOR di system/config/config.php tidak benar
            die('tipe konektor tidak didukung');
        }
    }

    public function get_last_query() {
        return $this->last_query;
    }

    public function cleaning($data) {

        // cek apakah $real_escape_string aktif ?
        if ($this->real_escape_string) {

            // jika fungsi magic quotes aktif, hapus semua slash yang dibuat oleh fungsi ini
            if ($this->magic_quote_active) {

                //buang semua bacslash yang dibuat manual oleh user
                if (is_array($data)) {
                    foreach ($data as $key => $value)
                        $clean_data[$key] = stripslashes($value);
                }
                else
                    $clean_data = stripslashes($data);

                // fungsi sanitasi diambil alih oleh sistem mysql_real_escape
                if (is_array($data)) {
                    foreach ($data as $key => $value)
                        $clean_data[$key] = mysql_real_escape_string($value);
                }
            } else {
                if (is_array($data)) {
                    foreach ($data as $key => $value)
                        $clean_data[$key] = mysql_real_escape_string($value);
                }
                else
                    $clean_data = mysql_real_escape_string($data);
            }
        } else {
            /*
             * PHP versi 4.1 ke bawah belum menyediakan funsgi mysql real_escape string
             * jika magic_quote tidak aktif maka proses sanitasi dilakukan secara manual
             */
            if (!$this->magic_quote_active)
                if (is_array($data))
                    foreach ($data as $key => $value)
                        $clean_data[$key] = addslashes($value);
            $clean_data = addslashes($data);
        }
        return $clean_data;
    }

    public function fetch($result) {

        $array_data = array();
        $nodata = "Tidah ada data";

        //jika konektor menggunakan mysqli
        if (self::$connector == "mysqli") {

            // ambil jumlah baris data dari database
            $num_rows = $result->num_rows;

            /*
             * jika data dalam dabase ada (jumlah baris minimal 1)
             * maka $array_data->rows diisi oleh data dari database
             * 
             */
            if ($num_rows > 0) {
                while ($res = $result->fetch_array(MYSQLI_ASSOC)) {
                    $array_data['rows'][] = $res;
                }
            }

            // jika tidak, maka data diisi oleh isi dari variabel $nodata
            else
                $array_data['rows'] = $nodata;

            // bersihkan memory dari hasil fetching
            $result->close();

            // jika konektor menggunakan mysql biasa
        } else {

            // ambil jumlah bari data dari database
            $num_rows = mysql_num_rows($result);

            /*
             * jika data dalam dabase ada (jumlah baris minimal 1)
             * maka $array_data->rows diisi oleh data dari database
             * jika tidak, maka data diisi oleh isi dari variabel $nodata
             */
            if ($num_rows > 0) {

                // fetching data ke dalam variabel $array_data->rows
                while ($res = mysql_fetch_assoc($result)) {
                    $array_data['rows'][] = $res;
                }

                // bershikan memory dari hasil fetching
                mysql_free_result($result);
            }

            // jika tidak ada data dalam database maka variabel $array_data->rows diisi dengan isi variabel $nodata
            else
                $array_data['rows'] = $nodata;
        }
        $array_data['num_rows'] = $num_rows;
        return $array_data;
    }

    public function query($sql, $fetch = true) {

        // simpan query terakhir ke dalam variabel $last_query
        $this->last_query = $sql;

        // buat objek $result sesuai dengan tipe konektor yang ditentukan di dalam system/config/config.php
        $result = (self::$connector == 'mysqli') ? $this->connection->query($sql) : mysql_query($sql, $this->connection);

        //cek untuk memastikan tidak ada error dalam eksekusi query
        $this->confirm($result);

        /* jika query select maka fetch hasil query
         * jika query update dan insert maka kembalikan true
         */
         $fetched_data = ($fetch)? $this->fetch($result) : true;

        return $fetched_data;
    }

    private function close() {
        self::$connector == 'mysqli' ? $this->connection->close() : mysql_close($this->connection);
    }

    function __destruct() {
        $this->close();
    }

}