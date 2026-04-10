<?php
session_start();

include 'conectar.php';
include 'mostrarAlojamientos.php';
include 'google_config.php';
include 'login.php';
include 'reservas.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

function checkIdReserva($id){
      $conn = conectarBD();
      $sql = "Select ID from reservas where id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$id]);
      $idEncontrado = $stmt->fetch(PDO::FETCH_ASSOC);
      return $idEncontrado;
  }

function generarId(){
  //Creacion de IDs de reserva aleatorio
  $letras = range('a', 'z');
  $letrasMayusculas = range('A', 'Z');
  $numeros = range(0, 9);
  $encontrado = false;

  do{
    $id = "";

    for ($i=0; $i < 8; $i++) { 
      if($i < 3){
        $id .= $letras[array_rand($letras)];
      }
      else if($i > 2 && $i < 6 ){
        $id .= $numeros[array_rand($numeros)];
      }
      else if($i == 6){
        $id .= $letrasMayusculas[array_rand($letrasMayusculas)];
      }
      else if($i == 7){
        $id .= $numeros[array_rand($numeros)];
      }
    }

    if(!checkIdReserva($id)){
      $encontrado = true;
    }
  }while(!$encontrado);
  
  return $id;
}


  if (!isset($_GET['id'], $_SESSION['fechaInicio'], $_SESSION['fechaFin'], $_SESSION['usuario'])) {
      header('Location: index.php');
      exit;
  }

  $fechaInicio = $_SESSION['fechaInicio'];
  $fechaFin    = $_SESSION['fechaFin'];
  $dniReserva  = $_SESSION['usuario'];
  $huespedes   = $_GET['huespedes'] ?? 0;
  $id_ver      = $_GET['id'];

  $id = generarId();

  $resultado = insertarReserva($id, $id_ver, $fechaInicio, $fechaFin, $huespedes, $dniReserva);
  
  if ($resultado) {
      unset($_SESSION['fechaInicio']);
      unset($_SESSION['fechaFin']);
  } else {
      header('Location: index.php?error=reserva_fallida');
      exit;
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reserva Completada</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa; 
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      text-align: center;
      padding: 20px;
    }

    h1 {
      color: green !important;
    }

    .spinner {
      animation: spin 1s infinite linear;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Media queries */
    @media (max-width: 768px) {
      body {
        height: auto;
        padding: 40px 20px;
      }

      h1 {
        font-size: 1.5rem;
      }

      .spinner-border {
        width: 3rem;
        height: 3rem;
      }
    }

    @media (max-width: 576px) {
      h1 {
        font-size: 1.25rem;
      }

      .spinner-border {
        width: 2.5rem;
        height: 2.5rem;
      }
    }
  </style>
</head>
<body>

  <div class="container text-center">
    <h1>Reserva Completada con Éxito</h1>&nbsp;&nbsp;
    <p>Redirigiendo...</p><br>
    <div class="spinner-border text-primary spinner" role="status">
      <span class="sr-only">Cargando...</span>
    </div>
  </div>

  <script>
    setTimeout(function() {
      window.location.href = 'index.php';
    }, 3000);
  </script>
</body>
</html>
