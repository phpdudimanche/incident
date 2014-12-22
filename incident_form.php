<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

isset($_REQUEST['act'])?$act=$_REQUEST['act']:$act='';
isset($_REQUEST['id'])?$id=$_REQUEST['id']:$id='';
$title='Rapport d\'incident';

require_once '_haut.php';

require_once 'incident.php';
$incident = new incident;

print("
<h1>$title</h1>
");


if($act=="create"){
$form_creation=$incident->display_crea_incident();
echo $form_creation;
}
elseif($act=="update"){
 // query select fetch  
$result=$incident->retrieve_id_incident($con,$id);
        if($debug===1){//DEBUG non genant
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        }
$id=$result[0]['id'];
$resume=$result[0]['resume'];
$statut=$result[0]['statut'];
$severite=$result[0]['severite'];
$urgence=$result[0]['urgence'];
$description=$result[0]['description'];
$form_modification=$incident->display_modif_incident($id,$resume,$statut,$severite,$urgence,$description);
echo $form_modification;
}
else{
    echo "que faire ?";//@todo par défaut proposer la création ?
}

require_once '_bas.php';
?>