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
/*    // WHERE ... AND ...
    echo "severite avant:";
    print_r($severite);// OR si plusieurs a l'interieur
    echo "<br />urgence avant:";
    print_r($urgence);
    // ORDER BY
    echo "<br />tri severite avant:";
    print($tri_severite);
    echo "<br />tri urgence avant:";
    print($tri_urgence);
    echo "<br /><br />";
    // transmission : array(where,order_by);
*/    
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
//@todo offrir la possibilité de trimballer les optionspour :remplir le formulaire de recherche (avec possibilité d'effacer)
}   
    
     /** seconde partie de la requete apres les champs, le filtre
      * 
      */
     function requete_personnalisee_where($where){//@test les deux, le 1er, le dernier, et variatioon en multiple = 6 tests
         //echo "<br />";
         //print_r($where);
         //--- WHERE
         $return="";
         $taille_1=sizeof($where);
         $a=0;
         //echo "taille ".$taille_1."<br />";
         foreach($where as $key=>$value){// tous les éléments du tableau N1
             $a++;
             //$return.=" a est ".$a."/ ";
             if($value!=''){// tant que tableau N2 n'est pas vide
                  $return.= "(";
                  //echo "tableau concernant ".$key."<br />"; 
                  $taille_2=sizeof($value);// compteur commence à 2
                  //echo 'taille du tableau '.$taille.'<br />';
                  $i=0;
                  foreach($value as $key_2=>$value_2){ //($i=0,$i<$taille,$i++){
                      $i++;
                      $return.= $key.'='.$key_2;
                      if($i<$taille_2){
                          $return.= " OR ";
                      }
                  }
                  $return.= ")";//fin du tableau N2
                    $suivant=next($where);// anticipation de l'élément suivant
                        if($suivant!=''){//@bug fixé en nettoyant l'envoi, pas de tableau vide
                        $return.= " AND ";// il faut qu'il y ait aussi eu un élément non vide avant
                        }
                        else{
                            $return.="";
                        }
             }
         }
         return $return;
     }
     $result1=requete_personnalisee_where($where);
if($debug===1){
echo "<br />Requete WHERE";
print($result1);
}
     function requete_personnalisee_orderby($orderby){
         $taille=sizeof($orderby);
         $i=0;
         $return ="";
         foreach($orderby as $cle=>$valeur){
             $i++;
            if($i==1 AND $i==$taille){// si une seule valeur, la valeur est la première et la dernière
               //$return.="(" ;
               $return.=$cle." ".$valeur; 
               //$return.=")";
            }
            elseif($i==1){// premier
               //$return.="(" ;
               $return.=$cle." ".$valeur;
            }
            elseif($i==$taille){// fin
                $return.=",".$cle." ".$valeur;
                //$return.=")";
            }
         }
         return $return;
     }
     $result2=requete_personnalisee_orderby($orderby);
        if($debug===1){
        echo "<br />Requete ORDERBY ";
        print($result2);
        echo "<br />";
        }
     /*
     pouvoir l'enregistrer, cad :
     V-00 la récupérer en array (au premier jeu)
     lui donner un nom sans accent ni apostrophe,virgule, espace
     la mettre en fichier de config
     la recherche est accessible par URL portant le nom
     - le propriétaire ou niveau d'accès sera à voir plus tard !
     */
     //concatenation des 2 requetes
     function requete_where_order($result1,$result2){
            $return="";
     if ($result1!="" AND $result2!=""){// optimal
            $return.=" AND ";// WHERE avant
            $return.="$result1";
            $return.=" ORDER BY ";
            $return.="$result2";           
     }
     elseif($result1!=""){
            $return.=" AND ";// WHERE avant
            $return.="$result1";
     }
     elseif($result2!=""){
            $return.=" ORDER BY ";
            $return.="$result2";
     }      
            return $return;
     }
     $result=requete_where_order($result1,$result2);
        if($debug===1){//DEBUG non genant
        echo 'requete concatenee :';
        print($result);
        echo '<br />';
        }
//echo "<br />Requete presque complete : ".$result;
     /** requete non preparee pour aller plus vite ?
      * @todo requete préparée
      */
     function recherche_personnalisee($con,$where,$orderby){
         try{
           // 0/ connexion : con
           // 1/ requete
           //$query="SELECT * FROM incident ";// initialement
$query="SELECT i.id, i.resume, i.description, i.severite, i.urgence";
$query.=", s.statut, s.date";//@todo avoir des id plus long :  , s.id as evenement s.id l'emportait sur i.id
$query.=" FROM incident i";
$query.=" JOIN statut s";
$query.=" ON s.id_incident=i.id";
$query.=" WHERE s.id=(SELECT MAX(s.id) FROM statut s WHERE s.id_incident=i.id)";// le dernier des statuts de l'incident
//$query.=" AND i.id!=''";
           $personnalisation=requete_where_order($where,$orderby);
           $query.=$personnalisation;
           // 4/ envoi
           //$appel=$con->query($query);
                $appel=$con->prepare($query);
                    if($appel->execute()){
                    $nombre=$appel->rowCount();//@bug FIXE pas pour SELECT, ou alors en PREPARE par execute
                    //$return=$nombre;
                    }else{
                        die('KO');
                    }
                if($nombre>0){
                    //$return='OK '.$nombre;
                    $return=$appel->fetchall();
                }
                else{
                    $return=$nombre.' : aucun enregistrement';
                }
           return $return;
         }
         catch (Exception $e){
             echo "erreur au select";
         }
     }
    
    $requete=recherche_personnalisee($con,$result1,$result2);
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
else{
   // echo "rien de prévu ?";
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