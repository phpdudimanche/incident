<?php
/** rapport d'incident
 *  ne pas transformer cela en tchat ou échange de mail : capitaliser infos dans des champs dédiés
 */
 class incident{
     
     public $id_incident;
     public $resume;// résumé vaut titre
     
     public $id_statut_actuel;// dernier statut (déjà en historique ?) / pour date création, récupérer celle dupremier statut
     public $severite_test;// mineur, majeur, bloquant // le risque peut être récupéré de "condition de test/cas de test"
     public $severite_metier;// à détecter en création selon qui se logge
     public $urgence;
     
     public $severite_list=array(10=>'mineur',20=>'moyen',30=>'majeur',40=>'bloquant');
    private $urgence_list=array(10=>'non urgent',20=>'urgent',30=>'immédiat');
     
     public $tracabilite_projet;// projet/module... (possible import outil projet)
     public $tracabilite_test;// condition de test, cas de test
     public $tracabilite_exigence;// citation, id reference (possible gestionnaire exigence, outil documentaire)
     public $tracabilite_risque;// possible (gestionnaire de risque)
     public $tracabilite_registre_de_test;// jointure table de pièce jointe pour preuve
     
     public $version;// versionning (possible import gestion de configuration)
     public $procedure;// steps (possible import référentiel de test)
     public $donnees;// (possible import référentiel de test)
     public $attendu;// (possible import référentiel de test)
     
     public $description;//tout commentaire utile...
     /* possible template à charger par projet
     ATTENDU :    OBTENU :  
     ACTIONS : 
     DONNEES : 
     
     APPLICATION :  LIVRABLE :  VERSION : DATE : 
     */
     
     public $id_proprietaire;// suivre jusqu'au bout ?

    
     public $phase_detection;// LOV (pouvoir tester la maîtrise de phase)
     public $phase_introduction;// LOV
     public $activite_detection;//LOV (pouvoir rapprocher du type statique ou dynamique)
     public $activite_introduction;// LOV
     
     public $defaillance;
     public $defaut;
     public $erreur;
     public $type_defaut;// LOV
     public $type_erreur;// 
     
    /** maitrise de phase
     *  v0 O/N  v1 combien de niveaux d'écarts (d'après la lov)
     * 
     */
     function maitrise_de_phase($phase_detection,$phase_introduction){
      $return=($phase_detection==$phase_introduction)?1:0;
      return $return;
     }
     /** statique ou dynamique
      * + autre fonction : faire le ratio
      */
     function type_technique(){
         
     }
     /** formulaire de création // vocation à être différent d'un formulaire de modification ?!
      * 
      */
     function display_crea_incident(){
      
      $act="create";// à faire passer si formulaire mutualisé
  // si controle js des champs : onsubmit= fonction(); return false   
	 $return="
	 		<form name='instance_incident' id='instance_incident' action='incident_act.php' method='post'><input type='hidden' name='act' id='act' value='$act';
			<p><label for='resume'>Résumé : </label><input type='text' name='resume' id='resume' size='70' value='le titre vaut résumé' onFocus='javascript:this.value=\"\"'></p>";
			
			 $return.="<p><label>&nbsp;</label>";
			$name='severite';// v0 si nom du champ transmis / v1 réécriture du nom comme url
   $selected='';
			$return.=$this->choisir_severite($name,$selected);
			  $return.=" ";
			$name='urgence';
   $selected='';
   $return.=$this->choisir_urgence($name,$selected);
     $return.="</p>";
			
			$return.="<p><label for='description'>Description : </label><textarea rows='6' cols='71' name='description' id='description' onfocus='javascript:this.value = \"\"' onsubmit='javascript:this.value = \"\"'>ce qui ne serait pas rapporté d'un autre outil</textarea></p>
			<p><label>&nbsp;</label><input type='submit' value=\"rapporter l'incident\" /></p>
			</form>
	 ";
 return $return; 
 // value='ce qui ne serait pas rapporté d\'un autre outil' onFocus='javascript:this.value=\"\"'>
 }
 /** liste deroulante
  * 
  */
 function presente_select($array,$name,$selected){
   	$return= '<select name="'.$name.'" size="1">';
   		$return.= '<option value="">--- '.$name.'</option>';// null first
   	foreach($array as $key=>$value){
   			$return.= '<option value="'.$key.'"';//key avant
   		if ($selected==$value) {//key avant
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
 function choisir_urgence($name,$selected){
  $array=$this->urgence_list;
  $return=$this->presente_select($array,$name,$selected);
  return $return;
 }
 }
?>