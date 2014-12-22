<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL); 
//phpinfo();//trouver le php.ini : non accessible : /etc/php5/apache2/php.ini
$style='_style.css';
print("        
        <title>$title</title>
        <link rel='stylesheet' href='$style' type='text/css'/>
");
?>
    </head>
<body>
<?php
// @todo de l'intérêt d'avoir un controleur, un seul si affichage modif _form ou visu _list
$return="<form name='rechercher_id_incident' id='rechercher_id_incident' action='incident_act.php' method='post'>
         <input type='hidden' name='act' id='act'value='search_id'>
         <input type='text' size='3' value='id' id='id' name='id' onFocus='javascript:this.value=\"\"'>
         <input type='submit' value=\"chercher l'incident\" />
         ";
echo $return;
$links="
<a href='#listing' class='plus'>Recherche avancée</a>
 | <a href='incident_list.php'>Liste des incidents (par défaut)</a>
</form>
";// fonction de lien libelles
echo $links;

require_once 'incident.php';
$incident=new incident;


echo '<form id="listing" action="incident_list.php" method="post"><input type="hidden" name="act" value="recherche_avancee">';
$statut_avancee=choisir_avancee('statut','statut');
print($statut_avancee);
$severite_avancee=$incident->choisir_avancee('severite','sévérité');// chacune des colonnes
print($severite_avancee);
$urgence_avancee=$incident->choisir_avancee('urgence','urgence');
print($urgence_avancee);
echo '<p class="action"><a href="#fermer">fermer</a> <input type="submit" value="go" /></p></form>';


?>


