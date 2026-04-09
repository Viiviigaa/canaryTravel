<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pasarela de Pago</title>
  <link rel="shortcut icon" href="https://i.postimg.cc/CKgwQCZM/prtuebackxw.png">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .payment-form {
      max-width: 400px;
      margin: 50px auto;
      padding: 30px;
      background-color: #fff;
      border-radius: 5px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    .form-group label {
      font-weight: 500;
    }
    .form-control {
      border-radius: 3px;
    }
    .btn-pay {
      background-color: #007bff;
      border-color: #007bff;
    }
    .circle-icon {
      position: absolute;
      top: 20px;
      left: 20px;
    }
    @media (max-width: 768px) {
      .payment-form {
        margin: 30px auto;
        padding: 20px;
      }
      .circle-icon {
        top: 10px;
        left: 10px;
      }
    }
    @media (max-width: 576px) {
      .payment-form {
        margin: 20px auto;
        padding: 15px;
      }
      h2 {
        font-size: 1.5rem;
      }
      .circle-icon {
        top: 5px;
        left: 5px;
      }
    }
  </style>
</head>
<?php

    session_start();

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

        $errores = [];
        $correcta = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $numeroTarjeta = trim($_POST['numeroTarjeta'] ?? '');
            $fechaTarjeta  = trim($_POST['fechaTarjeta'] ?? '');
            $cvv = trim($_POST['cvv'] ?? '');
            $nombre  = trim($_POST['nombre'] ?? '');

            if (empty($numeroTarjeta)) {
                $errores['numeroTarjeta'] = "El número de tarjeta es obligatorio.";
            }else if((strlen($numeroTarjeta)<13) || (strlen($numeroTarjeta)>19)){
                $errores['numeroTarjeta']="La longitud del numero de la tarjeta debe estar entre 13 y 19 caracteres";
            }

            if (empty($fechaTarjeta)) {
            $errores['fechaTarjeta'] = "La fecha es obligatoria.";
            } else {
                $fechaActual = new DateTime('first day of this month');
                $fechaExpedicion = DateTime::createFromFormat('Y-m', $fechaTarjeta);
                
                $fechaExpedicion->modify('first day of this month');

                if ($fechaExpedicion < $fechaActual) {
                    $errores['fechaTarjeta'] = "La tarjeta está caducada.";
                }
            }

            if (empty($cvv)) {
                $errores['cvv'] = "El numero CVV es obligatorio";
            }else if((strlen($cvv)<3) || (strlen($cvv)>4)){
                $errores['cvv']="La longitud del numero CVV de la tarjeta debe estar entre 3 y 4 caracteres";
            }

            if (empty($nombre)) {
                $errores['nombre'] = "El nombre es obligatorio.";
            }




            if (empty($errores)) {
                 header("Location: correcto.php?id=" . urlencode($_GET['id']) . "&huespedes=" . urlencode($_GET['huespedes']));
                exit();
            }
            
        }

?>
<body>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <form class="payment-form" action="" method="post">
        <h2 class="text-center mb-4">Realizar pago</h2>
        <div class="form-group">
          <label for="numeroTarjeta">Número de tarjeta</label>
          <input type="text" class="form-control" name="numeroTarjeta" id="numeroTarjeta" placeholder="Número de tarjeta">
          <?php if (isset($errores['numeroTarjeta'])){
                 echo "<br><span style='color:red'>{$errores['numeroTarjeta']}</span>";
            } 
            ?>
        </div>
        <div class="form-group">
          <label for="expiry">Fecha de expiración</label>
          <input type="month" class="form-control" name="fechaTarjeta" id="fechaTarjeta">
          <?php if (isset($errores['fechaTarjeta'])){
                 echo "<br><span style='color:red'>{$errores['fechaTarjeta']}</span>";
            } 
            ?>
        </div>
        <div class="form-group">
          <label for="cvv">CVV</label>
          <input type="text" class="form-control" name="cvv" id="cvv" placeholder="CVV">
          <?php if (isset($errores['cvv'])){
                 echo "<br><span style='color:red'>{$errores['cvv']}</span>";
            } 
            ?>
        </div>
        <div class="form-group">
          <label for="name">Nombre en la tarjeta</label>
          <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre en la tarjeta">
          <?php if (isset($errores['nombre'])){
                 echo "<br><span style='color:red'>{$errores['nombre']}</span>";
            } 
            ?>
        </div>
        <button type="submit" class="btn btn-primary btn-pay btn-block">Reservar</button>
      </form>
    </div>
    <div class="circle-icon">
      <a href="index.php">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left">
          <path d="M15 18l-6-6 6-6"></path>
        </svg>
        <span>Volver</span>
      </a>
    </div>
  </div>
</div>

</body>
</html>
