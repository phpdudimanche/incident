<?php
//ini_set('display_errors', 1); 
//error_reporting(E_ALL); 
isset($_REQUEST['act'])?$act=$_REQUEST['act']:$act='';
isset($_REQUEST['id'])?$id=$_REQUEST['id']:$id='';
isset($_REQUEST['severite'])?$severite=$_REQUEST['severite']:$severite='';
isset($_REQUEST['urgence'])?$urgence=$_REQUEST['urgence']:$urgence='';
isset($_REQUEST['tri_severite'])?$tri_severite=$_REQUEST['tri_severite']:$tri_severite='';
isset($_REQUEST['tri_urgence'])?$tri_urgence=$_REQUEST['tri_urgence']:$tri_urgence='';

$title='Affichage d\'incident';

    require_once '_haut.php';
    require_once 'incident.php';
    $incident = new incident;

print("
<h1>$title</h1>
");

if ($act=='list'){// liste de N incidents
$result=$incident->retrieve_n_incident($con);
        if($debug===1){
            echo '<br/>debug:'.$debug;
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
    //print_r($result);
    $incident->display_vue_incident($result);
    }
}
elseif($act=='recherche_avancee'){
    // WHERE ... AND ...
    echo "severite avant:";
    print_r($severite);// OR si plusieurs a l'interieur
    echo "<br />";
    print_r($urgence);
    // ORDER BY
    print($tri_severite);
    print($tri_urgence);
    // transmission : array(where,order_by);
    
    // preparation
    // $where=array('severite'=>$severite,'urgence'=>$urgence);
    // si une valeur est vide, retirer OPTIMISATION
    $where=array();
    
    if($severite!=''){
        //array_push($where,'severite'=>$severite);
        $where['severite']=$severite;
    }
    if($urgence!=''){// pas bonne solution elseif : non cumulable
        //array_push($where,'urgence'=>$urgence;
        $where['urgence']=$urgence;
    }
    echo "<br />retravail:";
    print_r($where);
    
    
    
    
    $orderby=array('tri_severite'=>$tri_severite,'tri_urgence'=>$tri_urgence);
        /*if($severite!='' OR $urgence!=''){
            echo "severite urgence <br />";
        }
        else{
            echo "ni severite ni urgence : pas de where specifique<br />";
        }
         if($tri_severite!='' OR $tri_urgence!=''){
            echo "triseverite triurgence <br />";
        }
        else{
            echo "ni triseverite ni triurgence : pas de order_by specifique<br />";
        }*/
     
     print_r($orderby);
     function requete_personnalisee($where,$orderby){
         //echo "<br />";
         //print_r($where);
         //--- WHERE
         $return="";
         $taille_1=sizeof($where);
         $a=0;
         //$z=0;// controle au moins un element non vide
         //echo "taille ".$taille_1."<br />";
         foreach($where as $key=>$value){// tous les éléments du tableau N1
             $a++;
             //$return.=" a est ".$a."/ ";
             if($value!=''){// tant que tableau N2 n'est pas vide
             //$z++;
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
                  $return.= ")";
                    //--- AND ici
                    //$avant=prev($where);
                    $suivant=next($where);// anticipation de l'élément suivant
                    //$return.='suivant '.printr($suivant).' // ';
                    // mais il faut que l'élément suivant existe !
                        /*if(!array_key_exists($suivant)){
                            $return.="";
                        }*/
                        if($suivant!=''){//@bug fixé en nettoyant l'envoi, pas de tableau vide
                        $return.= " AND ";// il faut qu'il y ait aussi eu un élément non vide avant
                        }
                        //elseif()
                        else{
                            $return.="";
                        }
                    
             }
                        
         }
         
         /*
         //--- ORDERBY
         $taille_3=sizeof($orderby);
         $b=0;
         $c=0;// pour orderby
         //return.='taille3 '.$taille_3;
         foreach($orderby as $cle=>$valeur){
             if($valeur!=''){
                 $c++;
                 $prochain=next($orderby);
                        if($c==1){
                          $return.=' ORDER BY ';// 1 seul ORDER BY au premier non vide 
                        }
                        elseif($c>0 AND $prochain!=''){
                            $return.=', ';// 1 seule virgule tant que la suivant existe et est non vide
                        }
                 $return.=$cle.' '.$valeur;
             }
         }
        */

         return $return;
     }
     $result=requete_personnalisee($where,$orderby);
     echo "<br />";
     print($result);
     /*
     pouvoir l'enregistrer, cad :
     V-00 la récupérer en array (au premier jeu)
     lui donner un nom sans accent ni apostrophe,virgule, espace
     la mettre en fichier de config
     la recherche est accessible par URL portant le nom
     - le propriétaire ou niveau d'accès sera à voir plus tard !
     */
     
    
}
else{
   // echo "rien de prévu ?";
$result=$incident->retrieve_n_incident($con);
$incident->display_admin_n_incident($result);
}
    require_once '_bas.php';
?>