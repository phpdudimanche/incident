<?php
/** statut du rapport d'incident et historique du statut
 * 
 */
 class statut{
     
   public id_statut;// clef 
   public id_incident;// clef étrangère de l'incident
   public statut;// correspondance chiffre et libellé
   public id_date;//date la plus complète : nécessaire pour contrôle de date (stagnation)
   
   // parametrage
   private liste_statut=array(10=>'à traiter',20=>'à arbitrer',30=>'à analyser',35=>'à préciser',40=>'à corriger',50=>'à livrer',60=>'à installer',70=>'à tester',100=>'non validé',500=>'rejeté',800=>'différé',1000=>'validé');// cf mantis- V1 internationalisation
   private_plage_ferme=array('debut'=>500,'fin'=>1000);// à exclure de la surveillance (stagnation)
   
   // SGBD : historique traduisible en ci dessous
   private historique_statut_incident=array(125=>array(25=>array('statut'=>10,'date'=>15897),50=>array('statut'=>20,'date'=>19797)));// 125=id incident, 25=id événement
   
   /* optionnel
   public id_membre;// clef étrangère des acteurs */
   
   
   /** surveiller la répétition inconsidérée d'un statut
    * modalités : V0 seuil de répétition à partir duquel déclencher une alerte (tout statut confondu) / V1 seuil par statut
    * usage : pour un incident "livré" N fois, problème de qualité 
    * usage : N fois en statut "à compléter", problème de qualité dans la création du rapport
    */
   function controler_recurrence_statut(){
       
   }
   /** ne pas rester indéfiniment dans le même statut
    * modalités : distinguer 2 (ou 3) types : le ouvert, (en cours), fermé : parce que le fermé, normal qu'il le reste
    * modalités : seuil de durée acceptable / V1 durée variable par statut ou autre facteur
    * usage : "à analyser" depuis plus de 15 jours / V1 "à analyser" depuis 3 semaines, mais "urgence faible" : RAS / "à corriger" depuis une seamine or "bloquant" et "urgent"
    */
    function controler_stagnation_statut(){
        
    }
    /** lister l'historique des changements de statut pour un même incident
     * 
     */
    function display_historique_statut(){
        
    }
     
 }

?>