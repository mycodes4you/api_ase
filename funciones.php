<?php

function limpiar_cadena($string) {
	$patterns = "/[<*°\'\\\[\{\]\}\!#\=$%&+\">]/";
	$replace = '';
	if (is_string($string)) {
		return preg_replace($patterns, $replace, $string);
	} elseif (is_array($string)) {
		reset($string);
		while (list($key, $value) = each($string)) {
			$string[$key] = limpiar_cadena($value);
		}
		return $string;
	}
}

function ejecutar_db($table, $data, $action = 'insertar', $parameters = '') {
	reset($data);
	$link = Conectarse();
	if ($action == 'insertar') {
		$preg0 = "SHOW INDEXES FROM " . $table . " WHERE Key_name = 'PRIMARY'";
		$matr0 = mysqli_query($link, $preg0);
		$fila0 = mysqli_fetch_assoc($matr0);
		$tablaid = $fila0['Column_name'];
		$query = 'INSERT INTO ' . $table . ' (';
		while (list($columns, ) = each($data)) {
			$query .= $columns . ', ';
		}
		$query = substr($query, 0, -2) . ') values (';
		reset($data);
		while (list(, $value) = each($data)) {
			switch ((string)$value) {
				case 'now()':
					$query .= 'now(), ';
					break;
				case 'null':
					$query .= 'null, ';
					break;
				default:
					$query .= '\'' . addslashes($value) . '\', ';
					break;
			}
		}
		$query = substr($query, 0, -2) . ')';
	} elseif ($action == 'actualizar') {
		$query = 'UPDATE ' . $table . ' SET ';
		while (list($columns, $value) = each($data)) {
			switch ((string)$value) {
				case 'now()':
					$query .= $columns . ' = now(), ';
					break;
				case 'null':
					$query .= $columns .= ' = null, ';
					break;
				default:
					$query .= $columns . ' = \'' . addslashes($value) . '\', ';
					break;
			}
		}
		$query = substr($query, 0, -2) . ' WHERE ' . $parameters;
	} elseif ($action == 'eliminar') {
		$query = 'DELETE FROM ' . $table . ' WHERE ' . $parameters;
	}
	$result = mysqli_query($link, $query) or die('Fallo el query: ' . $query);
	$nid = mysqli_insert_id($link);
	if ($action == 'insertar') {
		if($nid < 1) {
			$preg1 = "SELECT * FROM " . $table . " ORDER BY $tablaid DESC LIMIT 1";
		} else {
			$preg1 = "SELECT * FROM " . $table . " WHERE $tablaid = '" . $nid . "'";
		}
		$error = 'Error al ejecutar consulta';
		$matr1 = mysqli_query($link, $preg1) or die($error);
		$datos = mysqli_fetch_assoc($matr1);
		$query = "INSERT INTO " . $table . " VALUES (";
		while (list(, $value) = each($datos)) {
			switch ((string)$value) {
				case 'now()':
					$query .= 'now(), ';
					break;
				case 'null':
					$query .= 'null, ';
					break;
				default:
					$query .= '\'' . addslashes($value) . '\', ';
					break;
			}
		}
		$query = substr($query, 0, -2) . ')';
	}
	return $nid;
	return $error;
	}

function encripta($password){
	return hash('sha256' , $password);
}

function base64UrlEncode($data)
{
  // First of all you should encode $data to Base64 string
  $b64 = base64_encode($data);

  // Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
  if ($b64 === false) {
    return false;
  }

  // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
  $url = strtr($b64, '+/', '-_');

  // Remove padding character from the end of line and return the Base64URL result
  return rtrim($url, '=');
}

function base64UrlDecode($data, $strict = false)
{
  // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
  $b64 = strtr($data, '-_', '+/');

  // Decode Base64 string and return the original data
  return base64_decode($b64, $strict);
}