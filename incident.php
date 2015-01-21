<?php
/** rapport d'incident
 *  ne pas transformer cela en tchat ou échange de mail : capitaliser infos dans des champs dédiés
 */
 require_once 'config.php';
 require_once 'lib.php';
 class incident{
     
     public $id;
     public $resume;// résumé vaut titre
     public $description;//tout commentaire utile...
     public $severite;
     public $urgence;// $statut est embarqué par param.php

//----------------------- méthodes CRUD -------------------------------
      /** scripts CRUD
      * @todo la connexion vient polluer la méthode
      */
      function create_incident($con,$resume,$description,$severite,$urgence){
       
       try{
        // 0/ appel de la connexion
$con->beginTransaction();

        // 1/ requête
        $query = "INSERT INTO incident SET resume= :resume, description = :description, severite = :severite, urgence = :urgence";
        // 2/ étape préparation
$stmt = $con->prepare($query);
        // 3/ binder, passer les paramètres
        $stmt->bindParam(':resume', $resume);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':severite', $severite);
        $stmt->bindParam(':urgence', $urgence);
         if($stmt->execute()){
        $nombre=$stmt->rowCount();
            //echo "OK ".$nombre." traitement";
        }else{
            die('KO 1er');
        }  
        
        // requete
        $query="INSERT INTO statut SET id_incident=:id_incident, statut=:statut, date=:date";
        // preparation
$stmt = $con->prepare($query);
        // assignation
        $id_incident=$con->lastInsertId();//@todo à surveiller !
        $statut=10;//@todo mettre le statut à nouveau 10
        $date = date("YmdHis");//$date=20141006000000;
        $stmt->bindParam(':id_incident', $id_incident);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':date',$date);
        // remplacement des variables
        if($stmt->execute()){
        $nombre=$stmt->rowCount();
            //echo "OK ".$nombre." traitement";
        }else{
            die('KO 2nd');
        }
        
        
$con->commit();
    }catch(PDOException $exception){ //capture d'erreur
$con->rollback();
        echo "Erreur: " . $exception->getMessage();
    }
   // echo "create !";
      }
      function update_incident($con,$id,$resume,$description,$statut,$severite,$urgence){
      try{// seul $query change comparé à create ! + $id
        // 0/ appel de la connexion
$con->beginTransaction();        
        // 1/ requête
        $query = "UPDATE incident SET resume= ?, description = ?, severite = ?, urgence = ? WHERE id = ?";
        // 2/ étape préparation
        $stmt = $con->prepare($query);
        // 3/ binder, passer les paramètres
        $stmt->bindParam(1, $resume);
        $stmt->bindParam(2, $description);
        $stmt->bindParam(3, $severite);
        $stmt->bindParam(4, $urgence);
        $stmt->bindParam(5, $id);
        // 4/ exécution, envoi de la requete
        if($stmt->execute()){// execute(array($resume,$description,$severite,$urgence,$id))
        $nombre=$stmt->rowCount();
            //echo "OK ".$nombre." traitement";
        }else{
            die('KO execution');
        }
        
        // si le statut change on crée un événment en statut
        if($statut!=''){
            $query="INSERT INTO statut SET id_incident=:id_incident, statut=:statut, date=:date";
            $statut=$statut;
            $id_incident=$id;
            $date = date("YmdHis");
            $stmt = $con->prepare($query);
            $stmt->bindParam(':id_incident',$id);
            $stmt->bindParam(':statut',$statut);
            $stmt->bindParam(':date',$date);
            if($stmt->execute()){// execute(array($resume,$description,$severite,$urgence,$id))
                                $nombre=$stmt->rowCount();
                                    //echo "OK ".$nombre." traitement";
                                }else{
                                    die('KO execution');
                                }
        }
        else{
            
        }
$con->commit();        
    }catch(PDOException $exception){ //capture d'erreur
            echo "Erreur: " . $exception->getMessage();
        } 
      }
      /** peut etre interdit a l'usage (ou autorise qu'a l'admin) : juste statut supprime
       *
       */
      function delete_incident($con,$id){
          try{
        // 1/ requete
        $sql = "DELETE FROM incident WHERE id =:id";
        // 2/ preparation
        $stmt = $con->prepare($sql);
        // 3/ passage des parametres
        $stmt->bindParam(':id', $id);//, PDO::PARAM_INT
        // 4/ exécution, envoi de la requete
        if($stmt->execute()){
        $nombre=$stmt->rowCount();
            echo "OK ".$nombre." suppression effective ";
           
        }else{
            echo "pb";
               die('KO');
            }
    }catch(PDOException $exception){ //capture d'erreur
        echo "Erreur: " . $exception->getMessage();
    }   
      }
      /** total pour requete suivante : PAGINATION (sans filtre)
       * 
       */
      function count_n_incident($connexion){
          $query="SELECT COUNT(*) FROM incident";
            $flux= $connexion->prepare($query);
            $flux->setFetchMode(PDO::FETCH_ASSOC);	
                    if($flux->execute()){
            			$total=$flux->fetchColumn();
            			$return= $total;//.' resultats ';
            			//	$nbre_pages=ceil($total/$lignes_par_page);// voir fonction qui sort le nombre de pages
            			//	echo $nbre_pages.' pages :';
            			return($return);
                    }else{
                        die('KO execution');
                    }
      }
      function retrieve_n_incident($con,$page_demandee){
                        //nouveau : $page_demandee, LIMIT
                        global $lignes_par_page;
                        $depart=($page_demandee*$lignes_par_page)-$lignes_par_page;
      try{
  
        // 0/ connexion
        // 1/ requete SELECT * FROM incident ORDER BY statut ASC, severite DESC, urgence DESC
        //$query="SELECT * FROM incident";// initialement
$query="SELECT i.id, i.resume, i.description, i.severite, i.urgence";
$query.=", s.statut, s.date";//@todo avoir des id plus long :  , s.id as evenement s.id l'emportait sur i.id
$query.=" FROM incident i";
$query.=" JOIN statut s";
$query.=" ON s.id_incident=i.id";
$query.=" WHERE s.id=(SELECT MAX(s.id) FROM statut s WHERE s.id_incident=i.id)";// le dernier des statuts de l'incident
$query.=" AND i.id!=''";
$query.=" ORDER BY statut ASC, severite DESC, urgence DESC";
                        $query.=" LIMIT :offset,:length";// pagination
        //$query.=" ORDER BY severite DESC, urgence DESC";// initialement
        // 2/ étape préparation
        $stmt =$con->prepare($query);
        // precision : passer en objet ? nom de colonne ! = FETCH_ASSOC (par défaut sinon =both : nom+ordre)
        $stmt->setFetchMode(PDO::FETCH_ASSOC);// FETCH_OBJ FETCH_CLASS,'incident' sort tous les param de la classe, et pas mieux !
        // 3/ binder, passer les paramètres
                       $stmt->bindParam(':offset', $depart,PDO::PARAM_INT);// INDISPENSABLE forcer le type pour lim
                       $stmt->bindParam(':length', $lignes_par_page,PDO::PARAM_INT);// INDISPENSABLE forcer le type pour limit
        // 4/ exécution, envoi de la requete
        if($stmt->execute()){
        $nombre=$stmt->rowCount();
            //echo "OK ".$nombre." enregistrement trouvé";
        }else{
            die('KO');
        }  
         if($nombre>0)
         {  
             $result = $stmt->fetchAll();
            return $result;
         }
         else{
        // echo 'aucun enregistrement|'; 
          }
            } 
            catch ( Exception $e ) {
              echo "Une erreur est survenue lors de la récupération";
            }
      }
      function retrieve_id_incident($con,$id){
          try{
  
        // 0/ connexion
        // 1/ requete
        //$query="SELECT * FROM incident WHERE id= :id";// initialement
$query="SELECT i.id, i.resume, i.description, i.severite, i.urgence";
$query.=", s.statut, s.date";//, s.id as evenement sinon conflit entre id
$query.=" FROM incident i";
$query.=" JOIN statut s";
$query.=" ON s.id_incident=i.id";
$query.=" WHERE s.id=(SELECT MAX(s.id) FROM statut s WHERE s.id_incident=i.id)";// le dernier des statuts de l'incident
// ne gere pas les erreurs, si aucun statut existant
$query.=" AND i.id=:id";        
        // 2/ étape préparation
        $stmt =$con->prepare($query);
                // precision : passer en objet ? nom de colonne ! = FETCH_ASSOC (par défaut sinon =both : nom+ordre)
        $stmt->setFetchMode(PDO::FETCH_ASSOC);// FETCH_OBJ FETCH_CLASS,'incident' sort tous les param de la classe, et pas mieux !
        // 3/ binder, passer les paramètres
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);// balise, sinon N° avec ?
        // 4/ exécution, envoi de la requete
        if($stmt->execute()){
        $nombre=$stmt->rowCount();
            //echo "OK ".$nombre." enregistrement trouvé";
        }else{
            die('KO');// fait un return ?
        }  
  
     if($nombre>0)
     {  
        $result = $stmt->fetchAll();// fetch si une seule ligne
        /*echo '<pre>';
        print_r($result);
        echo '</pre>';*/
        return $result;
     }
     else{
    // echo 'aucun enregistrement|'; 
        $result='rien';
        return $result;
      }
            } 
            catch ( Exception $e ) {
              echo "Une erreur est survenue lors de la récupération";
            }
      }
      function count_n_incident_avancee($connexion,$result,$requete){// $where,$orderby,
          //$query="SELECT COUNT(*) FROM incident";
         $query="SELECT COUNT(i.id), i.resume, i.description, i.severite, i.urgence";
        $query.=", s.statut, s.date";//@todo avoir des id plus long :  , s.id as evenement s.id l'emportait sur i.id
        $query.=" FROM incident i";
        $query.=" JOIN statut s";
        $query.=" ON s.id_incident=i.id";
        $query.=" WHERE s.id=(SELECT MAX(s.id) FROM statut s WHERE s.id_incident=i.id)";
                    //$query.=" WHERE id!='' ";
                    //$personnalisation=requete_where_order($where,$orderby);
                    ($requete!='')?$personnalisation=$requete:$personnalisation=$result;// REQUETE fin=requete_where_order($where,$orderby)
                    $query.=$personnalisation;
            $flux= $connexion->prepare($query);
            $flux->setFetchMode(PDO::FETCH_ASSOC);	
                    if($flux->execute()){
            			$total=$flux->fetchColumn();
            			$return= $total;//.' resultats ';
            			//	$nbre_pages=ceil($total/$lignes_par_page);// voir fonction qui sort le nombre de pages
            			//	echo $nbre_pages.' pages :';
            			return($return);
                    }else{
                        die('KO execution comptage avancee');
                    }
      }
      function recherche_personnalisee($con,$result,$page_demandee,$requete){//$where,$orderby
                        //LIMIT : $page_demandee
                        global $lignes_par_page;
                        $depart=($page_demandee*$lignes_par_page)-$lignes_par_page;
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
           //$personnalisation=requete_where_order($where,$orderby);
           ($requete!='')?$personnalisation=$requete:$personnalisation=$result;// REQUETE requete_where_order($where,$orderby)
           //--- gérer la mutualisation de requete avec tout rechercher : ajouter order =" ORDER BY statut ASC, severite DESC, urgence DESC";
           $query.=$personnalisation;
                        $query.=" LIMIT :offset,:length";// pagination
           // 4/ envoi
           //$appel=$con->query($query);
                $appel=$con->prepare($query);
                        $appel->bindParam(':offset', $depart,PDO::PARAM_INT);// INDISPENSABLE type pour limit
                        $appel->bindParam(':length', $lignes_par_page,PDO::PARAM_INT);// INDISPENSABLE type pour limit
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
 /** pour l'export : pas de limit
 * 
 */
 function recherche_personnalisee_tout($con,$requete){
                        //LIMIT : $page_demandee
                        //global $lignes_par_page;
                        //$depart=($page_demandee*$lignes_par_page)-$lignes_par_page;
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
           //$personnalisation=requete_where_order($where,$orderby);
           ($requete!='')?$personnalisation=' AND '.$requete:$personnalisation=requete_where_order($where,$orderby);// REQUETE
           $query.=$personnalisation;
                        //$query.=" LIMIT :offset,:length";// pagination
    //return($query);
    //exit();
           // 4/ envoi
           //$appel=$con->query($query);
                $appel=$con->prepare($query);
                $appel->setFetchMode(PDO::FETCH_ASSOC);// PDO::FETCH_FUNC, "fruit"
                        //$appel->bindParam(':offset', $depart,PDO::PARAM_INT);// INDISPENSABLE type pour limit
                       // $appel->bindParam(':length', $lignes_par_page,PDO::PARAM_INT);// INDISPENSABLE type pour limit
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
//----------------------- méthodes d'affichage --------------------------
 /** liens avant affichage admin
  *
  */
 function display_lien_admin($display_array){
     // 11:affichage des liens avant resultat
          $label=($display_array>1)?"incidents":"incident";// gérer le pluriel
          echo "<div id=''><p>".$display_array." ".$label." | ";// mise en page 
          echo "<a href='incident_form.php?act=create'>en consigner un autre</a> | "; 
          echo "<a href='incident_list.php?act=export'>exporter tout</a></p>";//EXPORT passer tableau ou requete incident_export.php
 }
 /** présentation du listing + header et footer
       * $type (admin avec liens modif / visiteur sans action)
       */
 function display_admin_n_incident($array){         // modif parce que sans arrayni pagination
     global $statut_list,$severite_list,$urgence_list;// param.php
          $nombre=sizeof($array);// si array complet sans PAGINATION
                                                    //$label=($nombre>1)?"incidents":"incident";// gérer le pluriel
                                                     //echo "<div id=''><p>".$nombre." ".$label." | ";// mise en page 
                                                      //echo "<a href='incident_form.php?act=create'>en consigner un autre</a></p>";
    echo "<table>";
          for($i=0;$i<$nombre;$i++){
          $label_severite=afficher_statut($array[$i]['severite'],$severite_list);//$this->annoncer_severite($array[$i]['severite']);
          $label_urgence=afficher_statut($array[$i]['urgence'],$urgence_list);//$this->annoncer_urgence($array[$i]['urgence']);
          $label_statut=afficher_statut($array[$i]['statut'],$statut_list);
              //echo "<p><label class='vide'>&nbsp;</label><span class=''>";// mise en page
    echo "<tr><td class='id'>";
                  echo $array[$i]['id'].""; 
    echo "</td><td class='resume'>";
                  echo $array[$i]['resume'];
    echo "</td><td class='statuts'>";
                  echo $label_severite." - ".$label_urgence." - ".$label_statut;
    echo "</td><td class='crud'>";
                  echo "<a href='incident_list.php?act=view&id=".$array[$i]['id']."'>voir</a>";
             // echo "<a href='incident_list.php?act=view&id=".$array[$i]['id']."'>".$array[$i]['resume']."</a>";
              // si droit de modification : 
              echo " | <a href='incident_form.php?act=update&id=".$array[$i]['id']."'>modifier</a>";
              echo " | <a href='incident_act.php?act=delete&id=".$array[$i]['id']."'>supprimer</a>";
              //echo "</span></p>";
    echo "</td></tr>";
          }
          //echo '</div>';
    echo "</table>";
      }
 /** formulaire de création // vocation à être différent d'un formulaire de modification ?!
      * n'a pas l'option statut (n'existe pas encore)
      */
 function display_crea_incident(){
      global $severite_list,$urgence_list;
      $act="create";// à faire passer si formulaire mutualisé
  // si controle js des champs : onsubmit= fonction(); return false   
	 $return="
	 		<form name='instance_incident' id='instance_incident' action='incident_act.php' method='post'><input type='hidden' name='act' id='act' value='$act'>
			<p><label class='vide' for='resume'>Résumé : </label><input type='text' name='resume' id='resume' size='70' value='le titre vaut résumé' onFocus='javascript:this.value=\"\"'></p>";
			
			 $return.="<p><label class='vide'>&nbsp;</label>";
			$name='severite';// v0 si nom du champ transmis / v1 réécriture du nom comme url
   $selected='';
   $array=$severite_list;
			$return.=presente_select($array,$name,$selected);
			  $return.=" ";
			$name='urgence';
   $selected='';
   $array=$urgence_list;
   $return.=presente_select($array,$name,$selected);
     $return.="</p>";
			
			$return.="<p><label class='vide' for='description'>Description : </label><textarea rows='6' cols='71' name='description' id='description' onfocus='javascript:this.value = \"\"' onsubmit='javascript:this.value = \"\"'>ce qui ne serait pas rapporté d'un autre outil</textarea></p>
			<p><label class='vide'>&nbsp;</label><input type='submit' value=\"rapporter l'incident\" /></p>
			</form>
	 ";
 return $return; 
 // value='ce qui ne serait pas rapporté d\'un autre outil' onFocus='javascript:this.value=\"\"'>
 }
 /** form de modif potentiellement différent de la creation : car visualisation des imports effectues, plus complet...
  * 
  */
 function display_modif_incident($id,$resume,$statut,$severite,$urgence,$description){
     //----- dépendances -----------
     global $statut_list,$severite_list,$urgence_list;
     
    $act="update";// à faire passer si formulaire mutualisé
    // difference : value remplie sans js
  // si controle js des champs : onsubmit= fonction(); return false   
	 $return="
	 		<form name='instance_incident' id='instance_incident' action='incident_act.php' method='post'><input type='hidden' name='act' id='act' value='$act'><input type='hidden' name='id' id='id' value='$id'>
			<p><label class='vide' for='resume'>Résumé : </label><input type='text' name='resume' id='resume' size='70' value='".$resume."'></p>";
			
	$return.="<p><label class='vide'>&nbsp;</label>";
			$name='severite';// v0 si nom du champ transmis / v1 réécriture du nom comme url
            $selected=$severite;// recup
            $array=$severite_list;
	$return.=presente_select($array,$name,$selected);
	$return.=" ";
			$name='urgence';
            $selected=$urgence;// recup
            $array=$urgence_list;
   $return.=presente_select($array,$name,$selected);//$this->choisir_urgence($name,$selected);
   $return.=" ";
       $name='statut';
       $selected=$statut;//@todo implémenter la table de donnée
       $array=$statut_list;//param.php
   $return.=presente_select($array,$name,$selected);
     $return.="</p>";
			
			$return.="<p><label class='vide' for='description'>Description : </label><textarea rows='6' cols='71' name='description' id='description'>".$description."</textarea></p>
			<p><label class='vide'>&nbsp;</label><input type='submit' value=\"rapporter l'incident\" /></p>
			</form>
	 ";
 return $return;      
 }
 function display_vue_incident($result){
     global $statut_list,$urgence_list,$severite_list;
    //$severite_label=$this->annoncer_severite($result[0]['severite']);//@todo harmoniser : array-result urgence_label, label_urgence
    $severite_label=afficher_statut($result[0]['severite'],$severite_list);
    $urgence_label=afficher_statut($result[0]['urgence'],$urgence_list);//$this->annoncer_urgence($result[0]['urgence']);
    $statut_label=afficher_statut($result[0]['statut'],$statut_list);
    print("<dl>
        <p><dt>Résumé :</dt><dd>".$result[0]['resume']."</dd></p>
        <p><dt>Statuts :</dt><dd>".$severite_label." - ".$urgence_label." - ".$statut_label."</dd></p>
        <p><dt>Description :</dt><dd>".$result[0]['description']."</dd></p>
        </dl><br />");}

 }
?>