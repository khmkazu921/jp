<?php

class Staff
{
    public $name = "";
	public $deps = "";
	public $staffid = "";
	public $mail = "";

	//userinfo is aouth v3
	//array
	function __construct(array $staff) {
		$this->name = $staff["name"];
		$this->staffid = $staff["sub"];
		$this->mail = $staff["mail"]; 
	}
}

function login_confirmation() {
	if(!isset($_SESSION['staff'])) {
		header('Location: http://'.$_SERVER['HTTP_HOST'].'/statement/google.php/' ,true, 301);
		exit;
	}
}

function getGoogleUserInfo($accessToken) {
    if (empty($accessToken)) return null;
	$q = 'https://www.googleapis.com/oauth2/v3/userinfo?access_token='.$accessToken;
	$json = file_get_contents($q);
	$userInfo = json_decode($json,true);
    if (empty($userInfo)) {
        return null;
    }
    return $userInfo;
}

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
