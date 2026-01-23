<?php
error_reporting(E_ERROR);

function conexion()
{

	// datos para la coneccion a mysql
	// define('DB_SERVER','localhost');
	// define('DB_NAME','confluen_portal');
	// define('DB_USER','confluen_user');
	// define('DB_PASS','uNPu&~h1cXt3');

	// $mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	$mysqli = new mysqli('localhost', 'gestiontictaccom_usercron', ']s+tER.&k{ew(!?^', 'gestiontictaccom_admin');
	$tildes = $mysqli->query("SET NAMES 'utf8'"); //Para que se muestren las tildes correctamente

	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	return $mysqli;
}

?>


