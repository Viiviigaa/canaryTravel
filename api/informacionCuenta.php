<?php
// 1. Iniciar sesión SIEMPRE lo primero
session_start();

// 2. Incluir conexión
include 'conectar.php';

try {
    $conn = conectarBD('canaryTravel', 'root', 'root');
    
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit; 
    }

    $user = $_SESSION['usuario'];

    function obtenerInformacionUsuario($nombreUsuario, $db){
        $query = "SELECT * FROM usuarios WHERE nombreUsuario = ?";   
        $stmt = $db->prepare($query);
        $stmt->execute([$nombreUsuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    $informacionUsuario = obtenerInformacionUsuario($user, $conn);
    $date = new DateTime($informacionUsuario['FechaNac']);
    $fechaFormateada = $date->format('d/m/Y');

    function cambiarPassword($conn, $vieja, $nueva1, $nueva2, $informacionUsuario) {
        if ($nueva1 !== $nueva2) {
            echo "Las nuevas contraseñas no coinciden entre sí.";
            return false;
        }

        if ($informacionUsuario['Contrasena'] === $vieja) {
            $query = "UPDATE usuarios SET Contrasena = ? WHERE nombreUsuario = ?";       
            try {
                $stmt = $conn->prepare($query);
                $stmt->execute([$nueva1, $informacionUsuario['nombreUsuario']]);        
                echo "Contraseña actualizada con éxito.";
                return true;
            } catch (PDOException $e) {
                echo "Error al actualizar: " . $e->getMessage();
                return false;
            }
        } else {
            echo "La contraseña actual no es correcta.";
            return false;
        }
    }

    if (!$informacionUsuario) {
        session_destroy();
        header('Location: index.php');
        exit;
    }

} catch (Exception $e) {
    die("Error crítico: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
        .lenguajes {
            margin: 5px;
        }

        .contenido {
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            height: 100%;
            color: #333;
        }

        .texto-azul {
            color: #0d6efd !important;
        }

        body {
            background-color: #ffffffff;
        }

        .inputPadd{
            padding: 5px;
            border-radius: 5px;
        }

        /* La tarjeta base */
        .hooverCard {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        /* Cuando pasas el ratón */
        .hooverCard:hover {
            transform: translateY(-10px); /* Se eleva 10 píxeles */
            box-shadow: 0 10px 20px rgba(0,0,0,0.2) !important; /* Sombra más fuerte */
        }
    </style>
<body>
       <main class="container" style="margin-top: 100px !important;">
        <!-- Título en morado ubicado (mb - margin bottom) -->
        <h3 class="display-3 fw-bold texto-azul mb-4" id="datosPersonales">
            Usuario:
            <?php 
                if(isset($_SESSION['usuario'])){
                    $userLogged = $_SESSION['usuario'];
                    echo $userLogged;
                } 
            ?>
        </h3>
        <h5 class="display-3 fw-bold texto-azul mb-4">
            <?php
                echo $informacionUsuario['rol'];
            ?>
        </h5>
        <div class="row g-4">
            <!-- En pantallas grandes ocupamos 4 espacios y en pantallas medianas ocupamos 5 espacios-->
            <div class="col-lg-4 col-md-5">
                <!-- Añadimos sombreado-->
                <div class="contenido border shadow-sm text-dark">
                    <img src="static/img/usuario.png" alt="foto de perfil" class="img-fluid rounded mb-4 shadow-sm">
                    <button class="btn btn-primary">Cambiar foto de perfil</button>
                </div>
            </div>
            <!-- Hacemos que ocupe 8 espacios en pantallas grandes y 7 en espacios medianos (8+4)= 12 y (7+5)= 12 -->
            <div class="col-lg-8 col-md-7">
                <div class="contenido border shadow-sm text-dark">
                    <h3 class="texto-azul">Datos personales</h3>
                    <div class="row">
                        <div class="col-md-8">
                            <p class="mb-1"><strong>Nombre: </strong><?php echo $informacionUsuario['nombre']?></p>
                            <p class="mb-1"><strong>Apellidos: </strong><?php echo $informacionUsuario['apellidos']?></p>
                            <p class="mb-1"><strong>DNI: </strong><?php echo $informacionUsuario['dni']?></p>
                            <p class="mb-1"><strong>Teléfono: </strong><?php echo $informacionUsuario['Telefono']?></p>
                            <p class="mb-1"><strong>Correo electrónico: </strong><?php echo $informacionUsuario['Correo']?></p>
                            <p class="mb-1"><strong>Fecha de nacimiento: </strong><?php  echo $fechaFormateada ?></p>
                        </div>
                    </div>
                    <hr>
                    <h3 class="texto-azul">Cambiar contraseña</h3>
                        <form action="" method='post'>
                            <input type="password" name="actual" placeholder="Contraseña actual" class="inputPadd">
                            <input type="password" name="nueva" placeholder="Contraseña nueva" class="inputPadd">
                            <input type="password" name="repiteNueva" placeholder="Repite la nueva contraseña" class="inputPadd">
                            <button type="submit" class="btn btn-primary">Cambiar la contraseña</button>
                        </form>
                        <?php
                            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                                if(isset($_POST['actual']) && isset($_POST['nueva']) && isset($_POST['repiteNueva'])){
                                    $actual = $_POST['actual'];
                                    $nueva = $_POST['nueva'];   
                                    $repiteNueva = $_POST['repiteNueva'];  

                                    cambiarPassword($conn,$actual, $nueva, $repiteNueva,$informacionUsuario);
                                }
                            }
                        ?>
                    <hr>
                    <h3 class="texto-azul">Suscripciones</h3>
                    <div class="container mt-5">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm border-primary hooverCard">
                                <a href="suscripciones.php">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary">Free Plan</h5>
                                    </div>
                                </a>
                            </div>
                            </div>

                            <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm hooverCard">
                                <a href="suscripciones.php">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary">Plan Pro</h5>
                                        <p class="card-text"></p>
                                    </div>
                                </a>
                            </div>
                            </div>

                            <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm hooverCard">
                                <a href='suscripciones.php'>
                                    <div class="card-body">
                                        <h5 class="card-title text-primary">Plan Bussiness</h5>
                                        <p class="card-text"></p>
                                    </div>
                                </a>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
            </div>
    </main>
    <br><br>
</body>
</html>
