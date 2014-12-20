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
//$incident->display_form_recherche_avancee();

echo '<form id="listing" action="incident_list.php" method="post"><input type="hidden" name="act" value="recherche_avancee">';
$severite_avancee=$incident->choisir_avancee('severite','sévérité');
print($severite_avancee);
$urgence_avancee=$incident->choisir_avancee('urgence','urgence');
print($urgence_avancee);
echo '<p class="action"><a href="#fermer">fermer</a> <input type="submit" value="go" /></p></form>';

/*
print('
<form id="listing">
<p class="severite"><label class="utile">
<input type="radio" name="tri_severite" id="tri_severite" value="asc" title="asc" checked><span></span><input type="radio" name="tri_severite" value="desc" title="desc" >
sévérité : <a href="#fermer">fermer</a></label>
<label><input type="checkbox" name="items[]" /> Best Practices</label><br />
<label><input type="checkbox" name="items[]" /> Client Relationships</label><br />
<label><input type="checkbox" name="items[]" checked="checked" /> Communications</label><br />
<label><input type="checkbox" name="items[]" /> Compensation</label><br />
<label><input type="checkbox" name="items[]" /> Contracts and Negotiations</label>
</p>
<p class="urgence"><label class="utile">urgence</label>
<label><input type="checkbox" name="items[]" /> Best Practices</label><br />
<label><input type="checkbox" name="items[]" /> Client Relationships</label><br />
<label><input type="checkbox" name="items[]" checked="checked" /> Communications</label><br />
<label><input type="checkbox" name="items[]" /> Compensation</label><br />
<label><input type="checkbox" name="items[]" /> Contracts and Negotiations</label>
</p>
</form>
');*/

?>


