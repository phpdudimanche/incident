<?php
/** rapport d'incident
 *  ne pas transformer cela en tchat ou échange de mail : capitaliser infos dans des champs dédiés
 */
 require_once 'config.php';
 class incident{
     
     public $id;
     public $resume;// résumé vaut titre
     public $description;//tout commentaire utile...
     
     public $severite;
     public $urgence;
     
     private $severite_list=array(10=>'mineur',20=>'moyen',30=>'majeur',40=>'bloquant');//@todo mettre en _config.php
     private $urgence_list=array(10=>'non urgent',20=>'urgent',30=>'immédiat');//@todo mettre en _config.php

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
      function retrieve_n_incident($con){//@todo remanier cette requete par défaut
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
        //$query.=" ORDER BY severite DESC, urgence DESC";// initialement
    //@todo recuperer le dernier statut (tous,celamultiplierait les lignes)
        // 2/ étape préparation
        $stmt =$con->prepare($query);
        // precision : passer en objet ? nom de colonne ! = FETCH_ASSOC (par défaut sinon =both : nom+ordre)
        $stmt->setFetchMode(PDO::FETCH_ASSOC);// FETCH_OBJ FETCH_CLASS,'incident' sort tous les param de la classe, et pas mieux !
        // 3/ binder, passer les paramètres
        // 4/ exécution, envoi de la requete
        if($stmt->execute()){
        $nombre=$stmt->rowCount();
            //echo "OK ".$nombre." enregistrement trouvé";
        }else{
            die('KO');// fait un return ?
        }  
         if($nombre>0)
         {  
             $result = $stmt->fetchAll();
            /*echo '<pre>';
            print_r($result);
            echo '</pre>';*/
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
//----------------------- méthodes d'affichage --------------------------
 /** présentation du listing + header et footer
       * $type (admin avec liens modif / visiteur sans action)
       */
 function display_admin_n_incident($array){
     global $statut_list,$severite_list,$urgence_list;// param.php
          $nombre=sizeof($array);
          $label=($nombre>1)?"incidents":"incident";// gérer le pluriel
          echo "<div id=''><p>".$nombre." ".$label." | ";// mise en page
          echo "<a href='incident_form.php?act=create'>en consigner un autre</a></p>";
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
      
      $act="create";// à faire passer si formulaire mutualisé
  // si controle js des champs : onsubmit= fonction(); return false   
	 $return="
	 		<form name='instance_incident' id='instance_incident' action='incident_act.php' method='post'><input type='hidden' name='act' id='act' value='$act'>
			<p><label class='vide' for='resume'>Résumé : </label><input type='text' name='resume' id='resume' size='70' value='le titre vaut résumé' onFocus='javascript:this.value=\"\"'></p>";
			
			 $return.="<p><label class='vide'>&nbsp;</label>";
			$name='severite';// v0 si nom du champ transmis / v1 réécriture du nom comme url
   $selected='';
			$return.=$this->choisir_severite($name,$selected);
			  $return.=" ";
			$name='urgence';
   $selected='';
   $return.=$this->choisir_urgence($name,$selected);
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
     global $statut_list;
     
    $act="update";// à faire passer si formulaire mutualisé
    // difference : value remplie sans js
  // si controle js des champs : onsubmit= fonction(); return false   
	 $return="
	 		<form name='instance_incident' id='instance_incident' action='incident_act.php' method='post'><input type='hidden' name='act' id='act' value='$act'><input type='hidden' name='id' id='id' value='$id'>
			<p><label class='vide' for='resume'>Résumé : </label><input type='text' name='resume' id='resume' size='70' value='".$resume."'></p>";
			
	$return.="<p><label class='vide'>&nbsp;</label>";
			$name='severite';// v0 si nom du champ transmis / v1 réécriture du nom comme url
            $selected=$severite;// recup
	$return.=$this->choisir_severite($name,$selected);
	$return.=" ";
			$name='urgence';
            $selected=$urgence;// recup
   $return.=$this->choisir_urgence($name,$selected);
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
 /** en formulaire de création et modification
  * @todo mettre en fonctions utile
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
 function choisir_severite($name,$selected){
  $array=$this->severite_list;
  $return=$this->presente_select($array,$name,$selected);
  return $return;
 }
 /** en formulaire de recherche avancee
  * @todo mettre en fonctions utile
  */
 function choisir_avancee($type,$name){
  $la_liste=$type.'_list';// impossible à utiliser de suite : provoque erreur
  $array=$this->$la_liste;
  
  $return='
  <p class="severite"><label class="utile">
  '.$name.' : ';
  //$return.='<a href="#fermer" class="mini">fermer</a>';// haut
 // $return.='</label><label class="utile">';
  $return.='<input type="radio" name="tri_'.$type.'" id="tri_'.$type.'" value="asc" title="asc">
  <input type="radio" name="tri_'.$type.'" id="tri_'.$type.'" value="desc" title="desc" ></label>';// problème des labels for
      foreach($array as $key => $value){
      $return.='<label><input type="checkbox" name="'.$type.'['.$key.']" />'.$value.'</label><br />';
      }
  $return.='</p>'; // bas
  return $return;
 }
 function choisir_urgence($name,$selected){
  $array=$this->urgence_list;
  $return=$this->presente_select($array,$name,$selected);
  return $return;
 }
 /** utilisé en page de visualisation
  *
  */
 function  annoncer_severite($key){
     $array=$this->severite_list;
     $return=(array_key_exists($key,$array))?$array[$key]:'';// sans cela, avec clé inexistante : message Undefined offset: 0
     return $return;
 }
 function  annoncer_urgence($key){
     $array=$this->urgence_list;
     $return=(array_key_exists($key,$array))?$array[$key]:'';
     return $return;
 }

 }
?>