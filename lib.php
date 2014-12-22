<?php
/** librairie embarquée, le reste a vocation d'être géré par le dossier vendor de composer
 * tout ce qui est répété et n'est pas strictement propriétaire de la classe quil'utilisé
 * incluse dans : config.php
 */

    /** select des statuts : statut,urgence,gravité
     * utilisé par : formulaire de modification ou création
     * @param [in] array=tableau de valeurs / name=libelle de la liste / selected=valeur cochee
     */
    function presente_select($array,$name,$selected){
       	$return= '<select name="'.$name.'" size="1">';
       	$return.= '<option value="">--- '.$name.'</option>';// null first
       	    foreach($array as $key=>$value){
       	$return.= '<option value="'.$key.'"';//key avant
       		    if ($selected==$key) {//key comparee au resultat de requete : ATTENTION
       	$return.='selected="selected"';
       		    }
       	$return.= '>';
       	$return.= $value; 
       	$return.= '</option>';
       	}
       	$return.= '</select>';
       	return $return;
       //	print_r($array);
    }
    /** affichage et choix multiple des statuts : statut,urgence,gravité
     * utilisé par : recherche avancée
     */
    function choisir_avancee($type,$name){
        $la_liste=$type.'_list';// impossible à utiliser de suite : provoque erreur
        global $$la_liste;// variable dynamique
        $array=$$la_liste;
  
    $return='
            <p class="severite"><label class="utile">
            '.$name.' : ';
    $return.='<input type="radio" name="tri_'.$type.'" id="tri_'.$type.'" value="asc" title="asc">
              <input type="radio" name="tri_'.$type.'" id="tri_'.$type.'" value="desc" title="desc" ></label>';// problème des labels for
          foreach($array as $key => $value){
    $return.='<label><input type="checkbox" name="'.$type.'['.$key.']" />'.$value.'</label><br />';
          }
    $return.='</p>'; // bas
    return $return;
 }
    /** afficher le statut
     * 
     */
    function  afficher_statut($key,$array){
    // $array=$this->severite_list; directement passé
     if($array!=''){// parfois non rempli, passé alors en ''
     $return=(array_key_exists($key,$array))?$array[$key]:'';// sans cela, avec clé inexistante : message Undefined offset: 0
     return $return;
     }
    }
    /** partie where de la requete depuis checkbox: (... OR ...) AND (... OR ...)
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
     /** partie orderby de la requete depuis radio
      * 
      */
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
     /** partie réordonnée d'après les deux précédentes
      * utilisé par : incident.php/recherche_personnalisee
      */
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

?>