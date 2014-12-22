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

?>