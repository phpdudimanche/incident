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
isset($_REQUEST['page'])?$page=$_REQUEST['page']:$page='';
isset($_POST['requete'])?$requete=$_POST['requete']:$requete='';//unserialize($_POST['requete'])


/** NEW : session
 * 1/ pour : incident_list.php?act=export
 * @param[in] ce qu'il faut exporter : liste complète, recherche_avancee, query OU array
 * deja mis à disposition par la page
 * -> autre maniere : traiter tous les param en page _haut.php
 */
session_start();
isset($_SESSION['query'])?$export=$_SESSION['query']:$export='';// recuperation de ce qu'il faut exporter
//isset($_SESSION['query'])?$export=$_SESSION['query']:$export='';// recuperation de toute requete paginee

$title='Affichage d\'incident';

    require_once 'incident.php';
    $incident = new incident;
   
if(($requete!='') AND ($act='recherche_personnalisee')){//differencier recherche_avancee et recherche_personnalisee
    $requete=$recherche_personnalisee[$requete]['recherche'];
    // allouer la requete de recherche personnalisee du form
}
    
        if($act=='export'){
          // pas de sortie  
        }
        else{
        require_once '_haut.php';
        print("
        <h1>$title</h1>$export
        ");//$requete
        }
        
if ($act=='list'){// liste de N incidents ATTENTION, n'est plus utilisé
$result=$incident->retrieve_n_incident($con);
        if($debug===1){//DEBUG non genant
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        }
$incident->display_admin_n_incident($result);
}//@todo à enlever ? est en fait le "par défaut"
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
elseif(($act=='recherche_avancee') OR ($act=='recherche_personnalisee')){

    if($debug===1){//--- RECHERCHE AVANCEEE
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
$result=recherche_avancee_requete($severite,$urgence,$statut,$tri_severite,$tri_urgence,$tri_statut,$requete);
//avant requete, provenance RECHERCHE AVANCEE
//l.32 requete peut être RECHERCHE PERSONNALISEE
//sinon peut etre en form en cas de pagination [ENLEVER]


        $display_array=$incident->count_n_incident_avancee($con,$result,$requete);//06:total @todo requete si pagination, compte total
        $data=infos_pagination($display_array, $page);// 07:infos pagination (nombre de pages,page demandée) OK compatible array-sgbd
            if($debug===1){
            print_r($display_array);
            print_r($data);
            }
        $nbre_pages=$data['nombre de pages'];
        $page_demandee=$data['page demandee'];
    $requete=$incident->recherche_personnalisee($con,$result,$page_demandee,$requete);// 09:resultats
        if($debug===1){//DEBUG non genant
        echo "<br />requete finie";
        echo '<pre>';
        print_r($requete);
        echo '</pre>';
        }

    if(is_array($requete)){// pour éviter de traiter du vide !
    //-- <input type="hidden" name="act" value="recherche_avancee">// PAGINATION-avancee a passer en formulaire
    $pages=menu_pagination($nbre_pages,$page_demandee);//10:liste des pages OK compatible array-sgbd
        if($debug===1){
        echo "<br />pagination : ";
        print_r($pages);
        }
//--- affichage DEBUT
    $incident->display_lien_admin($display_array);// 11:affichage liens avant resultat
          display_pagination($pages,$display_array,$act,$result);// 12:affichage des pages
    $incident->display_admin_n_incident($requete);// 13:affichage des resultats
          display_pagination($pages,$display_array,$act,$result);
//--- affichage FIN
    }
    else{
        echo "Aucun résultat : <a href='incident_form.php?act=create'>en consigner un</a>";//@bug fixed
    }
}
elseif($act=='export'){
    if($debug===1){
    echo "ce que nous devons exporter : ";
    echo $export."<br />";// recuperation de l'ajout a personnalisé
    }
    $data=$incident->recherche_personnalisee_tout($con,$export);// sans avancee, fonctionne aussi ! (mais tri ID)
    if($debug===1){
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    }
    exporter_vers_excel($data);
    exit;
}
else{// par défaut
$_SESSION['query']='';// permet de dire que ce n'est pas une recherche avancée: traduit en : export

$display_array=$incident->count_n_incident($con);// si pagination, compte total
        $data=infos_pagination($display_array, $page);// OK compatible array-sgbd
        if($debug===1){
        print_r($display_array);
        print_r($data);
        }
        $nbre_pages=$data['nombre de pages'];
        $page_demandee=$data['page demandee'];
$result=$incident->retrieve_n_incident($con,$page_demandee);// retrieve_all_incident($con)
        if($debug===1){//DEBUG non genant
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        }
    if(is_array($result)){// si au moins un resultat
$pages=menu_pagination($nbre_pages,$page_demandee);// OK compatible array-sgbd
if($debug===1){
echo "<br />pagination : ";
print_r($pages);
}
    $incident->display_lien_admin($display_array);
display_pagination($pages,$display_array,'','');
     $incident->display_admin_n_incident($result);
display_pagination($pages,$display_array,'','');
    }
    else{
        echo "Aucun résultat : <a href='incident_form.php?act=create'>en consigner un</a>";//@bug fixed
    }
}

    require_once '_bas.php';
?>