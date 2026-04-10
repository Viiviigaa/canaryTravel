<?php

    include_once 'conectar.php';

    function loginUser($nombre, $contrasena, $location){
        $errores = [];
        $correcta = false;

        if (empty($errores)) {
            try {
                $conn = conectarBD();
                $stmt = $conn->query("SELECT nombreUsuario, Contrasena from usuarios");
                $validar = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($validar as $u) {
                    if ($u['nombreUsuario'] == $nombre && $u['Contrasena']==$contrasena){
                        $correcta =  true;
                        break;
                    }
                }
                if($correcta){
                    $_SESSION['usuario'] = $nombre;
                    header("Location: $location");
                    exit();
                }else{
                    return "Usuario o contraseña incorrectos";
                }
            } catch (PDOException $e) {
                return "Error en la base de datos";
            }
        }
    }

?>