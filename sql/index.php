<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
		<link rel='stylesheet' href='../_style.css' type='text/css'/>
<h1>Actions en base :</h1>
    </head>
<body>
<?php
isset($_POST['act'])?$act=$_POST['act']:$act='';
isset($_POST['script'])?$script=$_POST['script']:$script='';
// index.php : form à recharger avec radio button : installer la structure / installer les données / vider les données
echo '<form id="scripts" action="index.php" method="post"><input type="hidden" name="act" value="agir"><p class="severite">';
echo '<label for="script1">Structure <input type="radio" name="script" id="script1" value="structure"></label><br />';
echo '<label for="script2">Données <input type="radio" name="script" id="script2" value="donnees"></label><br />';
echo '<label for="script3">Nettoyage <input type="radio" name="script" id="script3" value="nettoyage"></label><br />';
echo '<input type="submit" value="lancer" style="color:red;margin-left:6em;"/></p></form>';

if($act!=''){
  echo 'Exécution du script : '.$script; 
//--- paramètres de connexion
    $host = 'localhost';//getHostByName(getHostName()); 'localhost';
    $port=3306;
    $username='root';
    $password='root';
    $db_name='incident';
//--- connexion
    try {
        $con = new PDO("mysql:host={$host};dbname={$db_name};port={$port};",$username, $password);
    }
    catch(PDOException $exception){
        echo "Erreur de connexion: " . $exception->getMessage();
    }
//--- action
$req='';
$req=file_get_contents ("sql-".$script.".sql");
$req=str_replace("\n","",$req);
$req=str_replace("\r","",$req);
//--- multiples requetes
	$lines = explode(";",$req);
	$taille=sizeof($lines);
	$query="";
		//echo "max ".$taille."<br />";
	for($i=0;$i<$taille-1;$i++){
		//echo "<br />".$i;
		//print_r($lines[$i]);
		$query.="\n\r".$lines[$i].";";	
	}
		//echo "<br />".$query;
 $con->exec($query);
}
else{	
	//echo 'rien';
}
?>
</body></html>