<?php
error_reporting(E_ALL ^ E_NOTICE);
$host = 'localhost';
$usuario = 'insta45_sd4';
$contrasena = 'Sw0~c2s2';
$baseDeDatos = 'instancias_ASE';
//$tabla = 'personas';
 
function Conectarse()
{
	global $host, $usuario, $contrasena, $baseDeDatos, $tabla;
	if (!($link = mysqli_connect($host, $usuario, $contrasena))) { 
		echo 'Error conectando a la base de datos!!!.<br>'; 
		exit(); 
	}
	else {
		//echo 'Listo, estamos conectados.<br>';
	}
	if (!mysqli_select_db($link, $baseDeDatos)) { 
		echo 'Error seleccionando la base de datos!!!.<br>'; 
		exit(); 
	}
	else {
		//echo 'Obtuvimos la base de datos ' .$baseDeDatos. ' sin problema.<br>';
	}
	return $link; 
} 
//$link = Conectarse(); 
//mysqli_close($link);


?>