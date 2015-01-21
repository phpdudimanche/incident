<?php
/** namespace pour éviter les conflits ?
 * 
 */
//--------------------- statuts ----------------------
    $severite_list=array(10=>'mineur',20=>'moyen',30=>'majeur',40=>'bloquant');
    $urgence_list=array(10=>'non urgent',20=>'urgent',30=>'immédiat');
    $statut_list=array(10=>'à traiter',20=>'à arbitrer',30=>'à analyser',35=>'à préciser',40=>'à corriger',50=>'à livrer',60=>'à installer',70=>'à tester',100=>'non validé',500=>'rejeté',800=>'différé',1000=>'validé');// statuts d'incident, contrainte nommage _list pour selection en recherche avancee
//------------------ divers --------------------------
$lignes_par_page=2;
$max_liens_pages=2;


?>