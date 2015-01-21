<?php
//@todo mutualiser traitement des entrants ?
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
<?php
//ini_set('display_errors', 1); 
//error_reporting(E_ALL); 
$style='_style.css';
print("        
        <title>$title</title>
        <link rel='stylesheet' href='$style' type='text/css'/>
");
?>
    </head>
<body>
<?php
// @todo de l'intérêt d'avoir un controleur, un seul si affichage modif _form ou visu _list : acces visu ou modif ?
$return="<form name='rechercher_id_incident' id='rechercher_id_incident' action='incident_act.php' method='post'>
         <input type='hidden' name='act' id='act'value='search_id'>
         <input type='text' size='3' value='id' id='id' name='id' onFocus='javascript:this.value=\"\"'>
         <input type='submit' value=\"chercher l'incident\" />
         ";
echo $return;
$links="
<a href='#listing' class='plus'>Recherche avancée</a>
 | <a href='incident_list.php'>Liste des incidents</a> 
 | <a href='#sauvees' class='plus'>Recherches personnalisées</a>
</form>
";// fonction de lien libelles
echo $links;

require_once 'incident.php';
$incident=new incident;

//@todo : droits URL+ visiteurs=-visu, acteurs=-modif, admin==-admin
echo '<form id="listing" action="incident_list.php" method="post"><input type="hidden" name="act" value="recherche_avancee">';
$severite_avancee=choisir_avancee('severite','sévérité');// chacune des colonnes
print($severite_avancee);
$urgence_avancee=choisir_avancee('urgence','urgence');
print($urgence_avancee);
$statut_avancee=choisir_avancee('statut','statut');
print($statut_avancee);
echo '<p class="action"><a href="#fermer">fermer</a> <input type="submit" value="go" /></p></form>';

echo '<form id="sauvees" action="incident_list.php" method="post"><input type="hidden" name="act" value="recherche_avancee">';
//echo 'liste des requetes';
$taille=sizeof($recherche_personnalisee);
//echo $taille;
echo '<p class="important">';
for($i=0; $i<$taille; $i++){
   echo '<label for="'.$i.'" class="important">'.$recherche_personnalisee[$i]['titre'].'';
   echo "<input type='radio' name='requete' id='".$i."' value='".serialize($recherche_personnalisee[$i]['recherche'])."'></label>";//serialize impose " et non '
}
echo '</p>';
echo '<p class="action"><a href="#fermer">fermer</a> <input type="submit" value="go" /></p></form>';
?>


