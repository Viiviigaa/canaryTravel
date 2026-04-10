<?php
    include 'conectar.php';
    include 'mostrarAlojamientos.php';
    session_start(); 
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canary Travel - Alquiler de Coches</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header>   
        <img src="static/img/lista.png" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" width="30" style="cursor: pointer;"> 
        
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">  
            <div class="offcanvas-header">    
                <h5 class="offcanvas-title" id="offcanvasExampleLabel">Menú Lateral</h5>    
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>  
            </div>  
            <div class="offcanvas-body d-flex flex-column">    
                <ul class="list-group">      
                    <li class="list-group-item"><a href="informacionCuenta.php">Mi cuenta</a></li> 
                    <li class="list-group-item"><a href="suscripciones.php">Suscripción</a></li>     
                    <li class="list-group-item"><a href="#">Recomendaciones</a></li>  
                    <li class="list-group-item"><a href="#">Contacto</a></li>
                </ul> 
                <ul class="list-group mt-auto">
                    <li class="list-group-item"><a href="logout.php" style='text-decoration: none; color:black'>Cerrar sesión</a></li>
                </ul> 
            </div> 
        </div>

        <a href="index.php" id="menuPrincipial" style="text-decoration: none; color: inherit; display: flex; align-items: center;">
            <img src="static/img/logo.png" alt="logo" id="logo" width="50">
            <h3 id="textoCabecera" style="margin-left: 10px;">Canary Travel</h3>
        </a>
        
        <div class="logs">
            <a href="empresas.php" class="btn btn-primary">Empresas</a>
            <a href="sesion.php" class="btn btn-primary">Iniciar sesión</a>
            <a href="registro.php" class="btn btn-primary">Registrarse</a>
        </div>
    </header>

    <main>
        <div id="buscador" class="container mt-4">
            <form action="" method="post"> <div class="row align-items-end justify-content-center">
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Origen</label>
                        <select name="origen" class="form-select">
                            <?php
                            try {
                                $conn = conectarBD();
                                $stmt = $conn->query("SELECT nombre FROM destinos");
                                $destinos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($destinos as $d) {
                                    $selected = (isset($_POST['origen']) && $_POST['origen'] == $d['nombre']) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($d['nombre']) . "' $selected>{$d['nombre']}</option>";
                                }
                            } catch (PDOException $e) {
                                echo "<option>Error de conexión</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Destino</label>
                        <select name="destinos" class="form-select">
                            <?php
                            try {
                                $conn = conectarBD();
                                $stmt = $conn->query("SELECT nombre FROM destinos");
                                $destinos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($destinos as $d) {
                                    $selected = (isset($_POST['destinos']) && $_POST['destinos'] == $d['nombre']) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($d['nombre']) . "' $selected>{$d['nombre']}</option>";
                                }
                            } catch (PDOException $e) {
                                echo "<option>Error de conexión</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Fecha de salida</label>
                        <input type="date" name="fechaIni" class="form-control" value="<?php echo $_POST['fechaIni'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Pasajeros</label>
                        <input type="number" name="pasajeros" class="form-control" value="<?php echo $_POST['fechaIni'] ?? ''; ?>">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label fw-bold">Vehículo: </label>
                        <input type="checkbox" name="vehiculo" class="form-checkbox">
                    </div>
                    <div class="col-md-2">
                        <input class="btn btn-primary w-100" type="submit" name="buscar" value="Buscar">
                    </div>
                </div>
            </form>
        </div>
        <?php
        // Procesamiento de datos de búsqueda
        $origenSeleccionado = $_POST['origen'] ?? null;
        $destinoSeleccionado = $_POST['destinos'] ?? null;
        $pasajeros = $_POST['pasajeros'] ?? null;
        $fechaIni = $_POST['fechaIni'] ?? null;
        $fechaFin = $_POST['fechaFin'] ?? null;
        
        // Cálculo de días
        $totalDias = 1;
        if ($fechaIni && $fechaFin) {
            $date1 = new DateTime($fechaIni);
            $date2 = new DateTime($fechaFin);
            $diff = $date1->diff($date2);
            $totalDias = $diff->days > 0 ? $diff->days : 1;
        }

        // Solo mostramos si hay un destino seleccionado o se ha pulsado buscar
        if ($origenSeleccionado && $destinoSeleccionado) {
            $ferrysEncontrados = mostrarFerrys();

            if (empty($ferrysEncontrados)) {
                echo "<h2 class='text-center mt-5'>No se encontraron ferrys disponibles desde " . htmlspecialchars($origenSeleccionado) . " hasta " . htmlspecialchars($destinoSeleccionado) .".</h2>";
            } else {
                echo "<div class='container mt-4'>";
                
                foreach ($ferrysEncontrados as $ferry) {
                    // Usamos el ID para crear un ID único para el carrusel de Bootstrap
                    $carouselId = "ferry_" . preg_replace('/[^A-Za-z0-9]/', '', $ferry['id']);
                    $precioTotalFerry = $ferry['precio'] * $totalDias;

                    echo "
                    <div class='card mb-4 shadow-sm' style='border-radius: 15px; overflow: hidden;'>
                        <div class='row g-0'>
                            <div class='col-md-4'>
                                <div id='{$carouselId}' class='carousel slide'>
                                    <div class='carousel-inner'>";
                                    
                                    $imagenes = explode(",", $ferry['fotos']);
                                    foreach($imagenes as $index => $img) {
                                        $activeClass = ($index === 0) ? 'active' : '';
                                        $rutaImg = "static/img/" . trim($img);
                                        echo "
                                        <div class='carousel-item {$activeClass}'>
                                            <img src='{$rutaImg}' class='img-fluid w-100' style='height: 250px; object-fit: cover;' alt='vehículo'>
                                        </div>";
                                    }

                                    echo "
                                    </div>
                                    <button class='carousel-control-prev' type='button' data-bs-target='#{$carouselId}' data-bs-slide='prev'>
                                        <span class='carousel-control-prev-icon' aria-hidden='true'></span>
                                    </button>
                                    <button class='carousel-control-next' type='button' data-bs-target='#{$carouselId}' data-bs-slide='next'>
                                        <span class='carousel-control-next-icon' aria-hidden='true'></span>
                                    </button>
                                </div>
                            </div>
                            
                            <div class='col-md-5 p-4'>
                                <h2 class='card-title'>Salida: {$ferry['origen']} Llegada: {$ferry['destino']}</h2>
                                <div class='mt-3'>
                                    <p class='mb-1'><strong>Fecha de salida:</strong> {$ferry['fechaSalida']}</p> 
                                    <p class='text-muted'>Precio por día: " . number_format($ferry['precio'], 2) . "€</p>
                                </div>
                            </div>

                            <div class='col-md-3 d-flex flex-column justify-content-center align-items-center bg-light border-start'>
                                <h2 class='text-success mb-0'>" . number_format($precioTotalFerry, 2) . "€</h2>
                                <p class='text-muted mb-3'>Total por {$totalDias} días</p>
                                <a href='procesarRenting.php?origen={$ferry['origen']}&destino={$ferry['destino']}&dias={$totalDias}' class='btn btn-primary btn-lg'>Reservar ahora</a>
                            </div>
                        </div>
                    </div>";
                }
                echo "</div>";
            }
        }
        ?>
    </main>
</body>
</html>