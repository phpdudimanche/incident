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
<a href='incident_list.php'>Liste des incidents (par défaut)</a>
</form>
";// fonction de lien libelles
echo $links;
?>

