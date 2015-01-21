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
    
    /** recuperation de where
     */
    function preparation_where($severite,$urgence,$statut){    //---- preparation : WHERE
        //@todo methode attendant un nombre inconnu d'argument
        // si une valeur est vide, retirer OPTIMISATION
        $where=array();// $where=array('severite'=>$severite,'urgence'=>$urgence,'statut'=>$statut);
        if($severite!=''){
            $where['severite']=$severite;
        }
        if($urgence!=''){
            $where['urgence']=$urgence;
        }
        if($statut!=''){
            $where['statut']=$statut;
        }
     return $where;   
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
    /** recuperation de orderby
     */
    function preparation_orderby($tri_severite,$tri_urgence,$tri_statut){
        //---- preparation : ORDERBY
        $orderby=array();// $orderby=array('tri_severite'=>$tri_severite,'tri_urgence'=>$tri_urgence);// ne pas s'embeter avec enlever le tri_
        if($tri_severite!=''){
            $orderby['severite']=$tri_severite;
        }
        if($tri_urgence!=''){
            $orderby['urgence']=$tri_urgence;
        }
        if($tri_statut!=''){
            $orderby['statut']=$tri_statut;
        }
        return $orderby;
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
     
    /** filtrer un résultat et préparer la pagination
     *  @param[in] tableau à traiter, page demandée, 
     *  @global lignes par page
     *  @param[out] taille du tableau, nombre de pages, page demandée, tableau filtré
     *  @return array
     */
    function infos_pagination($display_array, $page) {
            global $lignes_par_page;
    $return=array();
    			//echo "par page :".$lignes_par_page."<br />";
    							if(is_array($display_array)){
    		$total = sizeof($display_array);// nombre de valeurs en comptant le 0
    							}
    							else{
    							$total=$display_array;
    							}
    $return['nombre de resultats']=$total;//echo "total : ".$total."<br />";
    		$nbre_pages=ceil($total/$lignes_par_page);// nombre de pages arrondi au supérieur POSITIF 1,1=2 MAIS -3,2=-3 
    $return['nombre de pages']=$nbre_pages;//echo "nombre de pages : ".$nbre_pages."<br />";		
            $page = ($page < 1) ? 1 : $page;// si erreur avec 0 de mis, sinon demarrage à- lignes_par_page --------- TRANSMIS A REQUETE !
    $return['page demandee']=$page;//echo "page ".$page."<br />";
            $debut = ($page - 1) * ($lignes_par_page);// $show_per_page + 1 (3-1=2)*(3)
    			//echo "indice de demarrage dans tableau original : ".$start."<br />";
            $taille = $lignes_par_page;// car compte déjà avec +1
    if(is_array($display_array)){// ATTENTION
    $return['resultats'] = array_slice($display_array,$debut,$taille); // si pagination sur ARRAY et non SGBD
    }
    			return($return);
        }
    /** éléments de la pagination
     *  @param[in] nombre de pages, page demandée
     *  @param[out] tableau du numéro des pages et de 'la page en cours'
     *  @test : nominal page 5/3; inférieur page 3/1; supérieur 2/2 ;erreur  2/3
     */
    function menu_pagination($nbre_pages,$page_demandee){
        		global $max_liens_pages;		
        				$return=array();//$return='';// présenter dans l'ordre de sortie, du - vers +
        	if($page_demandee<=$nbre_pages){// KO $nbre_pages>$page_demandee
        		$mini=(($page_demandee-$max_liens_pages)<=0)?1:($page_demandee-$max_liens_pages);
        			if($mini!=1){
        				$return[1]=1;//$return.='1|';//debut
        			}		
        		for($i=$mini;$i<$page_demandee;$i++){// // N incréments avant
        		// gérer les impossibilités
        				$return[$i]=$i;//$return.=$i.'|';// capable de faire -1,0
        		}
        				if($nbre_pages>1){// evite de mettre une pagination pour rien
        				$return[$page_demandee]="'".$page_demandee."'";//$return.='<b>'.$page_demandee.'</b>|';
        				}
        		$maxi=(($page_demandee+$max_liens_pages)>=$nbre_pages)?	$nbre_pages:($page_demandee+$max_liens_pages);				
        		for($i=$page_demandee+1;$i<=$maxi;$i++){// // N incréments après $i<=$page_demandee+$max_liens_pages
        		// gérer les impossibilités
        				$return[$i]=$i;//$return.=$i.'|';
        		}	
        			if($maxi<$nbre_pages){
        				$return[$nbre_pages]=$nbre_pages;//$return.=$nbre_pages;//debut
        			}
        				
        	}
        	else{
        			    //$return.="ATENTION, la page demandee ne peut etre superieure au nombre de pages total.";
        	}	
        				return $return;	
        }
    /** affichage du lien de pagination
     *  @param[in] la liste des numéros de page, le tableau complet à véhiculer par champ caché
     *  @return display
     */
    function display_pagination($pages,$display_array,$action,$requete){
        // $pages:menu_pagination $display_array:donnees completes si requete sans limit, recherche_avancee si action
        print("<form id='pagination' name='pagination' action='".$_SERVER['PHP_SELF']."' method='post'>
        <input type='hidden' name='donnees' id='donnees' value='".serialize($display_array)."'>
        <input type='hidden' name='requete' id='requete' value='".serialize($requete)."'>
        <input type='hidden' name='act' value='".$action."'>
        ");// passer array en serialize
        foreach($pages as $key=>$value){
        	if(strstr($value,"'")){// page en cours avec '...'
        	//echo "valeur:".$value;
        	$value=strtr($value, "'", " ");// 
        	printf("<input class='page encours' type='text' value='%d' readonly='true'></input>",$value);// 'value' sort 0 (si pas nettoye)
        	}
        	else{
        	printf("<input name='page' id='page' class='page' type='submit' value='%d' readonly='true' />",$value);// mettre un submit sur des pages
        	//@todo : faire des fonds d'image
        	}
        }
        print("</form>
        ");
    }
    
    /* act='recherche_avancee' 103 lignes, c'est trop
    //function recherche_avancee_requete($severite,$urgence,$statut,$tri_severite,$tri_urgence,$tri_statut,$requete)
    $where=preparation_where($severite,$urgence,$statut);// 01:where recupere
    $orderby=preparation_orderby($tri_severite,$tri_urgence,$tri_statut);// 02:orderby recupere
    $result1=requete_personnalisee_where($where);// 03:where requete
    $result2=requete_personnalisee_orderby($orderby);// 04:orderby requete
    ($requete!='')?$result=$requete:$result=requete_where_order($result1,$result2);// 05:where et orderby requete commune / OU requete depuis pagination
    // 08:session charger la session requete concatenee pour export
    
$display_array=$incident->count_n_incident_avancee($con,$result1,$result2,$requete);//06:total @todo requete si pagination, compte total
    $data=infos_pagination($display_array, $page);// 07:infos pagination (nombre de pages,page demandée) OK compatible array-sgbd
    $pages=menu_pagination($nbre_pages,$page_demandee);//  10:liste des pages OK compatible array-sgbd
    
$requete=$incident->recherche_personnalisee($con,$result1,$result2,$page_demandee,$requete);// 09:resultats
    
    // 11:affichage des liens avant resultat (display dans incident.php)
    display_pagination($pages,$display_array,'recherche_avancee',$result);// 12:affichage des pages
$incident->display_admin_n_incident($requete);// 13:affichage des resultats
    */
    
    /** nettoyage pour export excel
     * excel ne supporte pas utf-8
     */
    function cleanData(&$str) {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    $str= mb_convert_encoding($str, 'UCS-2LE','UTF-8');// INDISPENSABLE excel gère mal l'UTF-8
    }
    /** export vers excel
     *
     */
    function exporter_vers_excel($data){
      $filename = "export_personnalise_" . date('Ymd') . ".xls";// nom de fichier
      $flag = false;
      foreach($data as $row) {// boucle de sortie
        if(!$flag) {
          // affiche entete ou pas
          $output.= implode("\t", array_keys($row)) . "\n";
          $flag = true;
        }
        array_walk($row, 'cleanData');//nettoyage DEPENDANCE
         $output.= implode("\t",array_values($row)) . "\n";
      }
    
      header("Content-Disposition: attachment; filename=\"$filename\"");// ne pas avoir de sortie avant
      header("Content-Type: application/vnd.ms-excel");
      header("Pragma: no-cache");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
      header("Expires: 0");
      
      echo $output;// sortie
    }
?>