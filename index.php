<?php
include 'conectar.php';
include 'mostrarAlojamientos.php';
include 'google_config.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/styles.css">

    <title>CanaryTravel</title>
</head>
<style>
    /* 1. Contenedor principal horizontal */
    .cardAlojamientos {
        display: flex !important;
        width: 100%;
        max-width: 900px;
        height: 200px;
        /* Altura fija igual a la imagen */
        margin: 20px auto;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
        /* Corta la imagen para que respete el radio del borde de la card */
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 0 !important;
        /* Asegura que la card no tenga espacio interno */
    }

    /* 2. El contenedor de la imagen (Reset total de espacios) */
    .galeria {
        width: 300px !important;
        min-width: 300px !important;
        max-width: 300px !important;
        height: 200px !important;
        position: relative;
        overflow: hidden;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* 3. Ajuste de los elementos internos del carrusel */
    .carousel,
    .carousel-inner,
    .carousel-item {
        width: 300px !important;
        height: 200px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Fix para evitar el bug de parpadeo/salto en la animación */
    .carousel-item {
        float: left !important;
        margin-right: -100% !important;
        backface-visibility: hidden;
    }

    /* 4. La imagen ajustada a TODOS los bordes (Top, Left, Bottom) */
    .tamImg {
        width: 300px !important;
        height: 200px !important;
        object-fit: cover;
        display: block;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
    }

    /* 5. Contenido de la derecha (Texto) */
    .itemAlojamientos {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        overflow: hidden;
    }

    .itemAlojamientos h2 {
        font-size: 1.3rem;
        margin-top: 0;
        margin-bottom: 8px;
        color: #003580;
    }

    .itemAlojamientos p {
        font-size: 0.95rem;
        color: #333;
        margin: 0;
    }

    /* Flechas de control */
    .carousel-control-prev,
    .carousel-control-next {
        width: 40px;
    }
</style>

<body>
    <header>
        <img src="static/img/lista.png" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" width="30">
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasExampleLabel">Menú Lateral</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column">
                <ul class="list-group">
                    <li class="list-group-item"><a href="informacionCuenta.php">Mi cuenta</a></li>
                    <li class="list-group-item"><a href="#">Suscripción</a></li>
                    <li class="list-group-item"><a href="#">Recomendaciones</a></li>
                    <li class="list-group-item"><a href="#">Contacto</a></li>
                </ul>
                <ul class="list-group mt-auto">
                    <li class="list-group-item"><a href="logout.php" style='text-decoration: none; color:black'>Cerrar sesion</a></li>
                </ul>
            </div>
        </div>
        <a href="" id="menuPrincipial">
            <img src="static/img/logo.png" alt="logo" id="logo">
            <h3 id="textoCabecera">Canary Travel</h3>
        </a>
        <?php
        if (!isset($_SESSION['usuario'])) {
            echo "<div class='logs'>
                <a href='empresas.php' class='btn btn-primary'>Empresas</a>
                <a href='sesion.php' class='btn btn-primary'>Iniciar sesión</a>
                <a href='registro.php' class='btn btn-primary'>Registrarse</a>
            </div>";
        }
        ?>
    </header>
    <main>
        <nav>
            <ul class="navList">
                <li><a style='text-decoration: none; color:black;' href="busquedaVuelos.php">Vuelos</a></li>
                <li><a style='text-decoration: none; color:black;' href="renting.php">Alquiler de coches</a></li>
                <li><a style='text-decoration: none; color:black;' href="ferrys.php">Ferrys</a></li>
            </ul>
        </nav>
        <h3 id="slogan">A que estás esperando para viajar al paraíso español</h3>
        <div id="buscador" class="container mt-4">
            <form action="index.php" method="post">
                <div class="row align-items-end justify-content-center">
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Destinos</label>
                        <select name="destinos" class="form-select">
                            <?php
                            try {
                                $conn = conectarBD();
                                $stmt = $conn->query("SELECT nombre from destinos");
                                $destinos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($destinos as $d) {
                                    echo "<option value='" . $d['nombre'] . "'>{$d['nombre']}</option>";
                                }
                            } catch (PDOException $e) {
                                echo "<option>Error de conexión</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Ida</label>
                        <input type="date" name="fechaIni" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Vuelta</label>
                        <input type="date" name="fechaFin" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Huéspedes</label>
                        <input type="number" name="huespedes" value="1" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input class="btn btn-primary w-100" type="submit" value="Buscar">
                    </div>
                </div>
            </form>
        </div>

        <?php
        $fechaErronea = false;

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo "<div class='GaleriaDestinos'>
            <div class='foto-item'><img src='static/img/tenerife.jpg'><div class='pie-foto'>Tenerife</div></div>
            <div class='foto-item'><img src='static/img/lanzarote.webp'><div class='pie-foto'>Lanzarote</div></div>
            <div class='foto-item'><img src='static/img/fuerteventura.jpg'><div class='pie-foto'>Fuerteventura</div></div>
            <div class='foto-item'><img src='static/img/granCanaria.webp'><div class='pie-foto'>Gran Canaria</div></div>
            <div class='foto-item'><img src='static/img/laPalma.png'><div class='pie-foto'>La Palma</div></div>
            <div class='foto-item'><img src='static/img/laGomera.avif'><div class='pie-foto'>La Gomera</div></div>
            <div class='foto-item'><img src='static/img/elHierro.jpg'><div class='pie-foto'>El Hierro</div></div>
            <div class='foto-item'><img src='static/img/laGraciosa.avif'><div class='pie-foto'>La Graciosa</div></div>
            </div>";
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $fechaInicio = $_POST['fechaIni'];
            $fechaFin = $_POST['fechaFin'];
            $hoy = date("Y-m-d");

            $errorMsg = "";

            if (empty($fechaInicio) || empty($fechaFin)) {
                $errorMsg = "Por favor, selecciona ambas fechas.";
            }elseif ($fechaInicio < $hoy) {
                $errorMsg = "La fecha de ida no puede ser anterior a hoy.";
            }
            elseif ($fechaInicio > $fechaFin) {
                $errorMsg = "La fecha de vuelta debe ser posterior a la fecha de ida.";
            }

            if ($errorMsg !== "") {
                echo "<h2 style='color:red; text-align:center; margin-top:20px;'>$errorMsg</h2>";
            } else {
                //Calculamos la diferencia de la fecha para saber el total de días y poder obtener el precio total
                $fechaIda = new DateTime($fechaInicio);
                $fechaVuelta = new DateTime($fechaFin);

                $intervalo = $fechaIda->diff($fechaVuelta);

                $totalDias = $intervalo->days;

                if($totalDias == 0){
                    $totalDias = 1; //La reserva no pueden tener un precio inferior al de 1 día
                }

                $_SESSION['fechaInicio'] = $fechaIda->format('Y-m-d');
                $_SESSION['fechaFin'] = $fechaVuelta->format('Y-m-d');

                $destinoSeleccionado = $_POST['destinos'];
                $numeroHuespedes = $_POST['huespedes'];
                $numeroHuespedes = (int) $numeroHuespedes;

                $alojamientosEncontrados = mostrarAlojamientos($destinoSeleccionado, $numeroHuespedes);
                if (empty($alojamientosEncontrados)) {
                    echo "<h2 style='text-align:center; margin-top:20px;'>No se encontraron alojamientos en este destino.</h2>";
                } else {
                    echo "<div class='contenedorAlojamientos' style='padding: 20px;'>";
                    
                    foreach ($alojamientosEncontrados as $alojamiento) {
                        $carouselId = "carousel_" . $alojamiento['ID']; 

                        $precioTotal = $alojamiento['precio'] * $totalDias * $numeroHuespedes;
                        $_SESSION['precioTotal'] = $precioTotal;
                        $url_mapa = "https://www.google.com/maps/search/?api=1&query=" . urlencode($alojamiento['direccion']);

                        echo "
                        <div class='cardAlojamientos'>
                            <div class='galeria'>
                                <div id='{$carouselId}' class='carousel slide' data-bs-interval='false'>
                                    <div class='carousel-inner'>";
                                    
                                    $imagenes = explode(",", $alojamiento['fotos']);
                                    foreach($imagenes as $index => $img) {
                                        $activeClass = ($index === 0) ? 'active' : '';
                                        echo "
                                        <div class='carousel-item {$activeClass}'>
                                            <img src='static/img/" . trim($img) . "' class='tamImg' alt='alojamiento'>
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
                            
                            <div class='itemAlojamientos'>
                                <h2>{$alojamiento['nombreAlojamiento']}</h2>
                                <p><strong>Tiempo total de la estancia: {$totalDias}</strong> dia/s</p>
                                <p class='text-primary small'><i class='bi bi-geo-alt'></i> {$alojamiento['isla']}, Canarias - <a href='{$url_mapa}'>Ubicación excelente</a></p>
                                <p>{$alojamiento['descripcion']}</p>                               
                            </div>
                            <div style='padding: 20px; display: flex; flex-direction: column; justify-content: center; align-items: center; border-left: 1px solid #eee;'>
                                <h2 style='color: #28a745;'>{$precioTotal}€</h2>
                                <a href='detallesAlojamiento.php?id=" . $alojamiento['ID'] . "&estancia=" . $totalDias . "&precio=" . $alojamiento['precio'] . "&huespedes=" . $numeroHuespedes . "' class='btn btn-primary'>Ver disponibilidad</a>
                            </div>
                        </div>";
                    }
                    echo "</div>";
                }
            }
        }
        ?>
    </main>
    <footer></footer>
</body>

</html>