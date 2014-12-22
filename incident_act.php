<?php
/** aiguillage CRUD
 * 
 */
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

$title='actions de CRUD sur incident';

require_once 'config.php';
require_once 'incident.php';//FETCH_CLASS ou methodes
$incident=new incident;

isset($_REQUEST['id'])?$id=$_REQUEST['id']:$id='';
isset($_REQUEST['act'])?$act=$_REQUEST['act']:$act='';
((isset($_POST['resume']))AND($_POST['resume']!='le titre vaut résumé'))?$resume=$_POST['resume']:$resume='';
$description=((isset($_POST['description']))AND($_POST['description']!="ce qui ne serait pas rapporté d'un autre outil"))?$_POST['description']:'';
$severite=((isset($_POST['severite']))AND($_POST['severite']!=''))?$_POST['severite']:'';
$urgence=((isset($_POST['urgence']))AND($_POST['urgence']!=''))?$_POST['urgence']:'';
$statut=((isset($_POST['statut']))AND($_POST['statut']!=''))?$_POST['statut']:'';
// $erreur ou exception cumulée avec liste oubli

/** @todo n'eut pas été nécessaire avec un contrôleur : pour la redirection, pas de sortie écran avant : 
 * 
 */
if(($act=='create')||($act=='delete_confirm')||($act=='update')||($act=='search_id')){// marche dans ce sens, pas dans l'autre !==
}
else{
    //echo 'act:'.$act;
require_once '_haut.php';// empeche deja le redirect ?
echo "<h1>$title</h1>";// empeche le redirect tout le temps
}


if($debug==1){// empeche le redirect en mode debug
echo '<p>résumé:'.$resume.'|act:'.$act.'|description:'.$description.'|sévérité:'.$severite.'|urgence:'.$urgence.'</p>';    
}
///exit();
//echo $erreur;


//--------------------------------- contrôleur -----------------------
if ($act=='create'){
// redirection vers : les derniers nouveaux pour l'auteur, les derniers nouveaux pour le périmètre ciblé,page de provenance avant formulaire, si logiciel interfacé
$incident->create_incident($con,$resume,$description,$severite,$urgence);
header('location:incident_list.php');
}
elseif($act=='delete'){
    echo "<label>&nbsp;</label>Vous confirmez la suppression ? : ";
    echo "<a href='incident_act.php?act=delete_confirm&id=".$id."'>supprimer</a>";
}
elseif($act=='delete_confirm'){
     echo "<p>Supprimer id : ".$id."</p>";
     $incident->delete_incident($con,$id);
     header('location:incident_list.php');
}
elseif($act=='update'){
  //script mise à jour
  $incident->update_incident($con,$id,$resume,$description,$statut,$severite,$urgence);
  // détecter si retourne un message (return) ou rien (void) pour voir s'il y a lieu de faire une redirection
  header('location:incident_list.php');// fera de toute façon une erreur !
  
}
elseif($act=='read'){
    // Récupération des données : page _list
    $incident->retrieve_n_incident($con);
}
elseif($act=='search_id'){
    header('location:incident_list.php?act=view&id='.$id.'');
}
else{
    echo "rien demandé";
}





require_once '_bas.php';
?>