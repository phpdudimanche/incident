<?php
$debug=1;//0 rien, 1 affichage, 2 logs

//--- paramètres de connexion
    $host = getHostByName(getHostName()); 'localhost';
    $port=3306;
    $username='';
    $password='';
    $db_name='';
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