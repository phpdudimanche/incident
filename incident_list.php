<?php
//ini_set('display_errors', 1); 
//error_reporting(E_ALL); 
isset($_REQUEST['act'])?$act=$_REQUEST['act']:$act='';
isset($_REQUEST['id'])?$id=$_REQUEST['id']:$id='';
isset($_REQUEST['severite'])?$severite=$_REQUEST['severite']:$severite='';
isset($_REQUEST['urgence'])?$urgence=$_REQUEST['urgence']:$urgence='';
isset($_REQUEST['statut'])?$statut=$_REQUEST['statut']:$statut='';
isset($_REQUEST['tri_severite'])?$tri_severite=$_REQUEST['tri_severite']:$tri_severite='';
isset($_REQUEST['tri_urgence'])?$tri_urgence=$_REQUEST['tri_urgence']:$tri_urgence='';
isset($_REQUEST['tri_statut'])?$tri_statut=$_REQUEST['tri_statut']:$tri_statut='';

$title='Affichage d\'incident';

    require_once '_haut.php';
    require_once 'incident.php';
    $incident = new incident;

print("
<h1>$title</h1>
");

if ($act=='list'){// liste de N incidents
$result=$incident->retrieve_n_incident($con);
        if($debug===1){//DEBUG non genant
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        }
$incident->display_admin_n_incident($result);
}
elseif($act=='view'){// vue non modifiable sur 1 incident imprimable
    $result=$incident->retrieve_id_incident($con,$id);
    //echo $result;
    if ($result=='rien'){
    echo 'Aucun incident avec cet identifiant.';
    }
    else{
        if($debug===1){//DEBUG non genant
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        }
    $incident->display_vue_incident($result);
    }
}
elseif($act=='recherche_avancee'){//@todo mettre dans incident.php dès que tout est intégré
if($debug===1){
   // WHERE ... AND ...
    echo "severite avant:";
    print_r($severite);// OR si plusieurs a l'interieur
    echo "<br />urgence avant:";
    print_r($urgence);
    echo "<br />statut avant:";
    print_r($statut);
    // ORDER BY
    echo "<br />tri severite avant:";
    print($tri_severite);
    echo "<br />tri urgence avant:";
    print($tri_urgence);
    echo "<br />tri statut avant:";
    print($tri_statut);
    echo "<br />";
    // transmission : array(where,order_by);
}
    //---- preparation : WHERE
    // $where=array('severite'=>$severite,'urgence'=>$urgence,'statut'=>$statut);
    // si une valeur est vide, retirer OPTIMISATION
    $where=array();
    if($severite!=''){
        $where['severite']=$severite;
    }
    if($urgence!=''){
        $where['urgence']=$urgence;
    }
    if($statut!=''){
        $where['statut']=$statut;
    }
if ($debug===1){
echo "<br />retravail WHERE:";
print_r($where);
}
    //---- preparation : ORDERBY
   // $orderby=array('tri_severite'=>$tri_severite,'tri_urgence'=>$tri_urgence);// ne pas s'embeter avec enlever le tri_
    $orderby=array();
    if($tri_severite!=''){
        $orderby['severite']=$tri_severite;
    }
    if($tri_urgence!=''){
        $orderby['urgence']=$tri_urgence;
    }
    if($tri_statut!=''){
        $orderby['statut']=$tri_statut;
    }
if ($debug===1){
echo '<br />retravail ORDERBY:';
print_r($orderby);
//@todo offrir la possibilité de trimballer les options pour : remplir le formulaire de recherche (avec possibilité d'effacer)
}   
    
     /** seconde partie de la requete apres les champs, le filtre
      * 
      */
     $result1=requete_personnalisee_where($where);// lib.php
if($debug===1){
echo "<br />Requete WHERE";
print($result1);
}
     $result2=requete_personnalisee_orderby($orderby);// lib.php
        if($debug===1){
        echo "<br />Requete ORDERBY ";
        print($result2);
        echo "<br />";
        }
     /** @todo  pouvoir l'enregistrer, cad :
     * V-00 la récupérer en array (au premier jeu)
     * lui donner un nom sans accent ni apostrophe,virgule, espace
     * la mettre en fichier de config
     * la recherche est accessible par URL portant le nom
     * le propriétaire ou niveau d'accès sera à voir plus tard !
     */
     //concatenation des 2 requetes
     $result=requete_where_order($result1,$result2);// lib.php
        if($debug===1){//DEBUG non genant
        echo 'requete concatenee :';
        print($result);
        echo '<br />';
        }
     /** requete non preparee pour aller plus vite ?
      * @todo requete préparée
      */
    $requete=$incident->recherche_personnalisee($con,$result1,$result2);// incident.php
//echo "<br />requete finie: ";
//print_r($requete);// tolere d'afficher du texte
        if($debug===1){//DEBUG non genant
        echo '<pre>';
        print_r($requete);
        echo '</pre>';
        }

    if(is_array($requete)){// pour éviter de traiter du vide !
    $incident->display_admin_n_incident($requete);
    }
    else{
        echo "Aucun résultat : <a href='incident_form.php?act=create'>en consigner un</a>";//@bug fixed
    }
}
else{// par défaut
$result=$incident->retrieve_n_incident($con);
        if($debug===1){//DEBUG non genant
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        }
    if(is_array($result)){
     $incident->display_admin_n_incident($result);
    }
    else{
        echo "Aucun résultat : <a href='incident_form.php?act=create'>en consigner un</a>";//@bug fixed
    }
}

    require_once '_bas.php';
?>