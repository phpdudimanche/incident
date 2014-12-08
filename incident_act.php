<?php
/** aiguillage CRUD
 * 
 */
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

$title='actions de CRUD sur incident';
require_once '_haut.php';
 
require_once 'config.php';


isset($_REQUEST['act'])?$act=$_REQUEST['act']:$act='';
((isset($_POST['resume']))AND($_POST['resume']!='le titre vaut résumé'))?$resume=$_POST['resume']:$resume='';
$description=((isset($_POST['description']))AND($_POST['description']!="ce qui ne serait pas rapporté d'un autre outil"))?$_POST['description']:'';

$severite=((isset($_POST['severite']))AND($_POST['severite']!=''))?$_POST['severite']:'';
$urgence=((isset($_POST['urgence']))AND($_POST['urgence']!=''))?$_POST['urgence']:'';
// $erreur ou exception cumulée avec liste oubli

echo "<h1>$title</h1>";
if($debug==1){
echo '<p>résumé:'.$resume.'|act:'.$act.'|description:'.$description.'|sévérité:'.$severite.'|urgence:'.$urgence.'</p>';    
}
///exit();
//echo $erreur;



if ($act=='create'){
// redirection vers : les derniers nouveaux pour l'auteur, les derniers nouveaux pour le périmètre ciblé 
// redirection vers page de provenance avant formulaire, si logiciel interfacé
// twiterbootstrap de 

try{
        // 0/ appel de la connexion
        // 1/ requête
        $query = "INSERT INTO incident SET resume= ?, description = ?, severite = ?, urgence = ?";
        // 2/ étape préparation
        $stmt = $con->prepare($query);
        // 3/ binder, passer les paramètres
        $stmt->bindParam(1, $resume);
        $stmt->bindParam(2, $description);
        $stmt->bindParam(3, $severite);
        $stmt->bindParam(4, $urgence);
        // 4/ exécution, envoi de la requete
        if($stmt->execute()){
        $nombre=$stmt->rowCount();
            echo "OK ".$nombre." traitement";
        }else{
            die('KO');
        }
    }catch(PDOException $exception){ //capture d'erreur
        echo "Erreur: " . $exception->getMessage();
    }

echo "create !";
}
elseif($act=='read'){
    // Récupération des données
//
try{
  // On envois la requète NON PREPARE
  $select = $con->query("SELECT * FROM incident");
  // On indique que nous utiliserons les résultats en tant qu'objet
  $select->setFetchMode(PDO::FETCH_OBJ);
  $nombre=$select->rowCount();
  echo $nombre.' enregistrement|';
     if($nombre>0)
     {  
           // Nous traitons les résultats en boucle
          while( $enregistrement = $select->fetch() )
          {
            // Affichage des enregistrements
            echo '<h1>', $enregistrement->id, '|', $enregistrement->resume, '</h1>';
          }
     }
     else{
     echo 'aucun enregistrement|'; 
      }
            } 
            catch ( Exception $e ) {
              echo "Une erreur est survenue lors de la récupération";
            }
//
echo "read !|";
}
else{
    echo "rien demandé";
}





require_once '_bas.php';
?>