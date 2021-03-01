<?php
$db = 'to477gpz_scuolaguida';
$user = 'to477gpz';
$pasword = 'RosiGay1';

try {
	$pdo = new PDO("mysql:host=81.88.52.143;dbname=$db;charset=utf8", $user, $pasword);
	$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo $e->getMessage() . ' in ' . $e->getFile() . ': ' . $e->getLine();
}
