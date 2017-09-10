<?php

function connectDb() {

	define('DSN','mysql:host=localhost;dbname=form_study');
	define('DB_USER','root');
	define('DB_PASSWORD','qSJNFXBqw9Z5542D');
	error_reporting(E_ALL & ~E_NOTICE);	
	$options = array(
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
	); 
    try {
        return new PDO(DSN, DB_USER, DB_PASSWORD, $options);
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
}
