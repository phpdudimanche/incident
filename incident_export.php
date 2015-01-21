<?php
session_start();
isset($_SESSION['query'])?$export=$_SESSION['query']:$export='';// recuperation de ce qu'il faut exporter

    require_once 'incident.php';
    $incident = new incident;

    $data=$incident->recherche_personnalisee_tout($con,$export);
    // peut aussi exporter la liste complète
    if($debug===1){
    echo "ce que nous devons exporter : ";
    echo $export."<br />";// recuperation de l'ajout a personnalisé
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    }

  function cleanData(&$str) //---- traitement export
  {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    $str= mb_convert_encoding($str, 'UCS-2LE','UTF-8');// INDISPENSABLE excel gère mal l'UTF-8
  }

  if($debug===1){
   echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit();// empeche sortie du fichier
  }
 
  $filename = "export_personnalise_" . date('Ymd') . ".xls";// nom de fichier
  //echo b"\xEF\xBB\xBF";// BOM s'écrit et gene
  $flag = false;
  foreach($data as $row) {// boucle de sortie
    if(!$flag) {
      // affiche entete ou pas
      $output.= implode("\t", array_keys($row)) . "\n";
      $flag = true;
    }
    array_walk($row, 'cleanData');//nettoyage
     $output.= implode("\t",array_values($row)) . "\n";
  }

  header("Content-Disposition: attachment; filename=\"$filename\"");// ne pas avoir de sortie avant
  header("Content-Type: application/vnd.ms-excel");
  header("Pragma: no-cache");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
  header("Expires: 0");
  
  echo $output;// sortie
  
  exit;
?>