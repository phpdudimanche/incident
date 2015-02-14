<?php
$debug=0;//0 rien, 1 affichage, 2 logs
require_once 'lib.php';// fonctions utiles
require_once 'param.php';// paramètres de configuration fine : règles métiers

//--- paramètres de connexion
    $host = 'localhost'; //'localhost'; getHostByName(getHostName())
    $port=3306;
    $username='root';
    $password='root';
    $db_name='incident';
//--- connexion
    try {
        $con = new PDO("mysql:host={$host};dbname={$db_name};port={$port};",$username, $password);

        //echo "OK";
    }
    // to handle connection error
    catch(PDOException $exception){
        echo "Erreur de connexion: " . $exception->getMessage();
    }
?>