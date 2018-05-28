<?php
  session_start();


 if (isset($_COOKIE['id'])){
   $_SESSION['id'] = $_COOKIE['id'];
  }

  function crearUsuario($datos, $imagen){
    $foto = '';
    if($_FILES[$imagen]['name']){
      $foto = 'img/' . $datos['email'] . '.' . pathinfo($_FILES[$imagen]['name'], PATHINFO_EXTENSION);
    }
    $usuario = [
      'id' => traerUltimoID(),
      'nombre' => $datos['nombre'],
      'apellido' => $datos['apellido'],
      'telefono' => $datos['telefono'],
      'email' => $datos['email'],
      'edad' => $datos['edad'],
      'barrio' => $datos ['barrio'],
      'pass' => password_hash($datos['pass'],PASSWORD_DEFAULT),
      'foto' => $foto
    ];
    return $usuario;
   }

     function validar($datos){
        $errores = [];
        $nombre = trim($datos['nombre']);
        $apellido =trim($datos['apellido']);
        $telefono =trim($datos['telefono']);
        $email = trim($datos['email']);
        $edad = $datos['edad'];
        $barrio = $datos['barrio'];
        $pass = trim($datos['pass']);
        $rpass = trim($datos['rpass']);


    if ($nombre == '') {
      $errores ['nombre'] = "Completa tu nombre";
      }

    if ($apellido == '') {
      $errores ['apellido'] = "Completa tu apellido";
      }

    if ($telefono == ''){
      $errores ['telefono'] = "Ingresa un Teléfono";
    } elseif (!filter_var($telefono, FILTER_VALIDATE_INT)){
      $errores ['telefono'] = "Teléfono invalido";
    }
    if ($edad == '*Elegi tu rango de edad'){
      $errores ['edad'] = "Selecciona tu rango de edad";
    }
    if ($barrio == '*Elegi tu barrio'){
      $errores ['barrio'] = "Selecciona tu barrio";
    }
    if ($email == ''){
      $errores ['email'] = "Completa tu email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
      $errores ['email'] = "Email invalido";
    } elseif (existeEmail($email)) {
      $errores ['email'] = "Este email ya está registrado";
    }

    if ($pass == '' || $rpass == ''){
      $errores ['pass'] = "Completa tus contraseñas";
    } elseif ($pass != $rpass){
      $errores ['pass'] = "Tus contraseñas deben coincidir";
    }

    if($_FILES['foto']['name']){
        $archivo = $_FILES['foto']['name'];
        $ext = pathinfo($archivo, PATHINFO_EXTENSION);
        $archivoFisico = $_FILES['foto']['tmp_name'];
        if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png'){
        $errores['foto'] = "Tu foto de perfil debe ser JPG, PNG o JPEG.";
         }
        }
    return $errores;
   }

   function existeEmail($email){
 		$todos = traerRegistros();
 		foreach ($todos as $todosEmail) {
 			if ($todosEmail['email'] == $email) {
 				return $todosEmail;
 			}
 		}
 		return false;
 	}

   function guardarUsuario ($datos){


     $usuarioJSON = json_encode($datos);
     file_put_contents('usuarios.json', $usuarioJSON . PHP_EOL, FILE_APPEND);
     return $datos;
   }

  function traerRegistros() {

     $registrosJson = file_get_contents('usuarios.json', true);
     $usuariosDatos = explode(PHP_EOL, $registrosJson);
      array_pop($usuariosDatos);
      $todosDatos = [];

      foreach ($usuariosDatos as $usuario){
      $todosDatos[] = json_decode($usuario, true);
      }
      return $todosDatos;
    }

    function traerUltimoID(){
    $usuarios = traerRegistros();

      if (count($usuarios) == 0){
       return 1;
      }

      $elUltimo = array_pop($usuarios);

      $id = $elUltimo['id'];
      return $id + 1;
    }

    function guardarImagen($imagen){
      $errores = '';

      if (isset($_FILES[$imagen])){
        $archivo = $_FILES[$imagen]['name'];
        $ext = pathinfo($archivo, PATHINFO_EXTENSION);
        $archivoFisico = $_FILES[$imagen]['tmp_name'];
        if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png'){
          $dondeEstoyParado = dirname(__FILE__);
  				$rutaFinalConNombre = $dondeEstoyParado . '/img/' . $_POST['email'] . '.' . $ext;
          move_uploaded_file($archivoFisico, $rutaFinalConNombre);
        } else {
          $errores = "El formato de tu foto de perfil tiene que ser JPG, PNG o JPEG.";

        }
      }
      return $errores;
    }

    function validarLogin($datos){
  		$traerDatos = [];
  		$email = trim($datos['email']);
  		$pass = trim($datos['pass']);

  		if ($email == '') {
  			$traerDatos['email'] = 'Completá tu email';
  		} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  			$traerDatos['email'] = 'Formato de email inválido';
  		} elseif (!$usuario = existeEmail($email)) {
  			$traerDatos['email'] = 'Este email no está registrado';
  		} else {
        if ($pass == '') {
         $traerDatos['pass'] = 'Ingresa tu Password';
       }elseif(!password_verify($pass, $usuario['pass'])) {
            $traerDatos['pass'] = "Datos Inválidos";
          }
      }

      return $traerDatos;
    }

    function loguear($usuario){
  	  $_SESSION['id'] = $usuario['id'];
      header('location: index.php');
  		exit;
  	}

      function estaLogueado(){
        return isset($_SESSION['id']);
      }

      function traerPorId($id){
        $todos = traerRegistros();
        foreach ($todos as $usuario) {
          if ($id == $usuario['id']) {
            return $usuario;
          }
        }
        return false;
      }



   ?>
