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
isset($_POST['requete'])?$requete=unserialize($_POST['requete']):$requete='';

/** NEW : session
 * 1/ pour : incident_list.php?act=export
 * @param[in] ce qu'il faut exporter : liste complète, recherche_avancee, query OU array
 * deja mis à disposition par la page
 * -> autre maniere : traiter tous les param en page _haut.php
 */
session_start();
isset($_SESSION['query'])?$export=$_SESSION['query']:$export='';// recuperation de ce qu'il faut exporter

$title='Affichage d\'incident';

    require_once 'incident.php';
    $incident = new incident;
    
        if($act=='export'){
          // pas de sortie  
        }
        else{
        require_once '_haut.php';
        print("
        <h1>$title</h1>
        ");
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
$where=preparation_where($severite,$urgence,$statut);// 01:where recupere
if ($debug===1){
echo "<br />retravail WHERE:";
print_r($where);
}
$orderby=preparation_orderby($tri_severite,$tri_urgence,$tri_statut);// 02:orderby recupere
if ($debug===1){
echo '<br />retravail ORDERBY:';
print_r($orderby);
//@todo offrir la possibilité de trimballer les options pour : remplir le formulaire de recherche (avec possibilité d'effacer)
}   
     /** seconde partie de la requete apres les champs, le filtre
      * 
      */
     $result1=requete_personnalisee_where($where);// 03:where requete
if($debug===1){
echo "<br />Requete WHERE";
print($result1);
}
     $result2=requete_personnalisee_orderby($orderby);// 04:orderby requete
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
     ($requete!='')?$result=$requete:$result=requete_where_order($result1,$result2);// 05:where et orderby requete commune / OU requete depuis pagination
            if($debug===1){//DEBUG non genant
            echo 'requete concatenee :';
            print($result);// PAGINATION-avancee a passer en formulaire
            echo '<br />';
            }
        $display_array=$incident->count_n_incident_avancee($con,$result1,$result2,$requete);//06:total @todo requete si pagination, compte total
        $data=infos_pagination($display_array, $page);// 07:infos pagination (nombre de pages,page demandée) OK compatible array-sgbd
            if($debug===1){
            print_r($display_array);
            print_r($data);
            }
        $nbre_pages=$data['nombre de pages'];
        $page_demandee=$data['page demandee'];
        
 // 08:session charger la session pour export DEBUT -------------------------------------------------
 if($requete!=''){
$_SESSION['query']=$requete;// si c'est une recherche avancee apres pagination ou liste personnalisee
 }
 if(($result1!='')OR($result12='')){
 $_SESSION['query']=$result1.''.$result2;// si c'est une recherche avancee après formulaire
 }
 //--- charger la session pour export FIN -------------------------------------------------
    
    $requete=$incident->recherche_personnalisee($con,$result1,$result2,$page_demandee,$requete);// 09:resultats
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
        // 11:affichage des liens avant resultat
          $label=($display_array>1)?"incidents":"incident";// gérer le pluriel
          echo "<div id=''><p>".$display_array." ".$label." | ";// mise en page 
          echo "<a href='incident_form.php?act=create'>en consigner un autre</a> | "; 
          echo "<a href='incident_list.php?act=export'>exporter tout</a></p>";//EXPORT passer tableau ou requete incident_export.php
          display_pagination($pages,$display_array,'recherche_avancee',$result);// 12:affichage des pages
    $incident->display_admin_n_incident($requete);// 13:affichage des resultats
          display_pagination($pages,$display_array,'recherche_avancee',$result);
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
          $label=($display_array>1)?"incidents":"incident";// gérer le pluriel
          echo "<div id=''><p>".$display_array." ".$label." | ";// mise en page 
          echo "<a href='incident_form.php?act=create'>en consigner un autre</a> | ";
                    echo "<a href='incident_list.php?act=export'>exporter tout</a></p>";//EXPORT passer tableau ou requete
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