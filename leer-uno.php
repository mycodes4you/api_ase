<?php
include_once('conexion.php');
include_once('funciones.php');
foreach($_POST as $k => $v) {$$k = limpiar_cadena($v);} // echo $k.' -> '.$v.' | ';    
foreach($_GET as $k => $v){$$k = limpiar_cadena($v);} // echo $k.' -> '.$v.' | ';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$mensaje = [];

$link = Conectarse();
$tabla = 'b64_usuarios';

$datos = json_decode(file_get_contents("php://input"));

foreach ($datos as $usuario => $valor) {
	$email = $datos->usuario->email;
	$password = encripta($datos->usuario->password);
	$instancia = $datos->instancia->numero;
	$token = $datos->archivo;
}

$q_permiso = "SELECT * FROM $tabla WHERE email = '".$email."' AND password = '".$password."'";
	$consulta_u = mysqli_query($link, $q_permiso);
	$existe = mysqli_num_rows($consulta_u);

	if($existe != '' || $existe != '0' || $existe != FALSE || $existe != NULL){
		http_response_code(200);
		$mensaje_acceso = array('Mensaje Acceso' => "Acceso obtenido correctamente!.");
		unset($tabla);
		$instancia = $datos->instancia->numero;

		if ($instancia != ''){
			$tabla = 'b64_instancias';
			$q_instancia = "SELECT * FROM $tabla WHERE id_instancia = " . $instancia ."";
			$p_instancia = mysqli_query($link, $q_instancia);

			$datos_array = [];

			if ($datos_i = mysqli_fetch_array($p_instancia)) {	

				$datos_array = array ('id_instancia' => $datos_i['id_instancia'],
															'nombre_instancia' => $datos_i['nombre_instancia'],
															'subdominio_instancia' => $datos_i['subdominio_instancia'],
															'activa_instancia' => $datos_i['activa_instancia'],
															'servidor_instancia' => $datos_i['servidor_instancia']
														);
				http_response_code(200);
				$mensaje_consulta = array('Mensaje Consulta' => "Datos de la Instancia obtenidos con Exito!!!");
			}
			else {
				http_response_code(503);
				$mensaje_consulta = array('Mensaje Consulta' => "No se encontro ninguna instancia con el ID ingresado.");
			}

		}
		else{
			http_response_code(400);
			$mensaje_consulta = array('Mensaje Consulta' => "Error al realizar la consulta, no se ingreso un ID");
		}
	}
	else{
		http_response_code(401);
		$mensaje_acceso = array('Mensaje Acceso' => "Usuario no existe o las credenciales no son validas.");
		echo json_encode($mensaje_acceso);
	}


	$token_recibido = $token;

	//llave secreta
	$key_secreta = 'Myx9ln.2s3';

	//explode a el token recibido
	$jwt_valores = explode('.', $token_recibido);

	///header recibido
	$header_recibido = $jwt_valores[0];

	///payload recibido
	$payload_recibido = $jwt_valores[1];


	$payload_recibido_unir = str_replace('\r', '', str_replace( "????", "", $payload_recibido));

	$nombre_archivo = date('Ymd-H.m.s').'.xml';

	$payload_r_bas64_1 = file_put_contents($nombre_archivo, base64UrlDecode($payload_recibido_unir));

	// firma recibida
	$key_secreta_recibida = $jwt_valores[2];

	//concatenamos header y payload recibidos
	$header_payload_recibidos = $header_recibido . '.' . $payload_recibido;

	$key_generado = base64UrlEncode(hash_hmac('sha256', $header_payload_recibidos, 'Myx9ln.2s3', true));

	if($key_generado == $key_secreta_recibida) {
	    $verifica_token = array('Mensaje Archivo' => "El archivo se recibio de manera Correcta");
	    $url_archivo = array('Nombre Archivo' => $nombre_archivo);
	} else {
	    $verifica_token = array('Mensaje Archivo' => "El archivo no se recibio");
	}


//$encabezados = getallheaders();
$respuesta = json_encode($datos_array + $mensaje_consulta + $mensaje_acceso + $verifica_token + $url_archivo);

echo $respuesta;