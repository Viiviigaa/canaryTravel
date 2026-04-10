<?php
    function conectarBD(){
        $con = new PDO('mysql:host=localhost;dbname=canaryTravel;charset=utf8','victor','1234');
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $con;
    }
?>