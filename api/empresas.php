<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script> 
    <title>CanaryTravel</title>
</head>
<body>
    <header>
        <img src="static/img/lista.png"  data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" width="30"> 
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">  <div class="offcanvas-header">    
            <h5 class="offcanvas-title" id="offcanvasExampleLabel">Menú Lateral</h5>    
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>  
        </div>  
        <div class="offcanvas-body d-flex flex-column">    
            <ul class="list-group">      
                <li class="list-group-item"><a href="informacionCuenta.php">Mi cuenta</a></li> 
                <li class="list-group-item"><a href="#">Suscripción</a></li> 
                <li class="list-group-item"><a href="#">Alquiler de coches</a></li>      
                <li class="list-group-item"><a href="#">Vuelos</a></li>      
                <li class="list-group-item"><a href="#">Ferrys</a></li>      
                <li class="list-group-item"><a href="#">Recomendaciones</a></li>  
                <li class="list-group-item"><a href="#">Contacto</a></li>
            </ul> 
            <ul class="list-group mt-auto">
                <li class="list-group-item"><a href="logout.php" style='text-decoration: none; color:black'>Cerrar sesion</a></li>
            </ul> 
        </div> 
    </div> 
        <a href="index.php" id="menuPrincipial">
            <img src="static/img/logo.png" alt="logo" id="logo">
            <h3 id="textoCabecera">Canary Travel</h3>
        </a>
        <div class="logs">
            <button class="btn btn-primary"><a href="empresas.php" style='text-decoration: none; color:white; width:150px'>Empresas</a></button>
            <button class="btn btn-primary"><a href="sesion.php" style='text-decoration: none; color:white; width:150px'>Iniciar sesión</a></button>
            <button class="btn btn-primary"><a href="registro.php" style='text-decoration: none; color:white; width:150px'>Registrarse</a></button>
        </div>
    </header>
    <main>
        <div id="sesion-box">
        <h3>Formulario para afiliacion con empresas</h3>
        <form method="post" id="registerForm">
            
            <label for="correo">Correo:</label>
            <input type="email" name="correo" placeholder="Correo" id="correo" class="inputIni">
            <label for="asunto">Asunto del contacto:</label>
            <input type="text" name="asunto" placeholder="Asunto" id="asunto" class="inputIni">
            <label for="body">Cuerpo del comunicado:</label>
            <textarea name="cuerpo"  style="height:150px; width: 350px" placeholder="Cuerpo del comunicado" class="inputIni"></textarea>
            <input type="submit" value="Enviar Formulario" name="enviar" id="submitRegister">
        </div>
        </form>

    </main>
    <footer>

    </footer>
</body>
</html>