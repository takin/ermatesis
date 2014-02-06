<?php 
require_once 'system/config/config.php';
require_once 'system/config/application.php';

/* 
 * instansiasi kelas application
 * kelas ini berisi setting dasar dari aplikasi
 */
$app = new Application();

/* 
 * panggil method stage()
 * method ini berisi setting error_reporting PHP
 * sesuai dengan setting ENVIRONMENT di config.php
 */
$app->stage(); 