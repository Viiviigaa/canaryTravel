<?php
session_start();

include 'conectar.php';
include 'mostrarAlojamientos.php';
include 'google_config.php';
include 'login.php';
include 'reservas.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
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
  <?php
  if (!isset($_GET['id'], $_SESSION['fechaInicio'], $_SESSION['fechaFin'], $_SESSION['usuario'])) {
      header('Location: index.php');
      exit;
  }

  $fechaInicio = $_SESSION['fechaInicio'];
  $fechaFin    = $_SESSION['fechaFin'];
  $dniReserva  = $_SESSION['usuario'];
  $huespedes   = $_GET['huespedes'] ?? 0;
  $id_ver      = $_GET['id'];

  $resultado = insertarReserva($id_ver, $fechaInicio, $fechaFin, $huespedes, $dniReserva);
  if (!$resultado) {
      header('Location: index.php?error=reserva_fallida');
      exit;
  }
?>

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
