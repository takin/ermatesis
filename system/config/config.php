<?php

/* konfigurasi global deparator riketori
 * untuk memudahkan aplikasi di deploy di lintas platform
 * karena UNIX dan Windows menggunakan sistem spearator yanng berbeda,
 * dimana windows menggunakan backslash (\) sedangkan UNIX menggunakan forward slash (/)
 */
define('DS', DIRECTORY_SEPARATOR);

//lokasi root aplikasi
define('ROOT', dirname(dirname(dirname(__FILE__))));

//host database
define("DB_HOST", "localhost");

//username database
define("DB_USER", "root");

//password datbase
define("DB_PASS", "root");

//nama database
define("DB_NAME", "erma");

//tipe konektor
define("DB_CONNECTOR", "mysqli");

//hompage
define('basepath', 'http://localhost/erma/mvc/');

//kontroller standar
define('DEF_CONTROLLER', 'dashboard');
