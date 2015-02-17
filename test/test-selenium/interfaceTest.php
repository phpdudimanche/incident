<?php
// test selenium

class interfaceTest extends PHPUnit_Extensions_SeleniumTestCase
{
protected $captureScreenshotOnFailure = TRUE;
protected $screenshotPath = "D:\schemas";
protected $screenshotUrl = "http://localhost:8080/dev-01/";
// $_REQUEST['domain'];
// obligé de déclarer et de noter en dur sans getenv('DOMAIN'), préférer nom de méthode + ligne;	
	
	//--- INITIALISATION BASE
	//public static function setUpBeforeClass(){
	protected function setUp(){ // boucle BROWSER
		//--- paramètres de connexion
		$host = 'localhost';//getHostByName(getHostName()); 'localhost';
		$port=3306;
		$username='root';
		$password='root';
		$db_name='incident';
		//--- connexion
			try {
				$con = new PDO("mysql:host={$host};dbname={$db_name};port={$port};",$username, $password);
				//echo "OK";
			}
			catch(PDOException $exception){
				echo "Erreur de connexion: " . $exception->getMessage();
			}	
	//--- action
	$req='';
		$racine=getenv('RACINE');
	$req=file_get_contents ($racine."sql\sql-donnees.sql");
	$req=str_replace("\n","",$req);
	$req=str_replace("\r","",$req);
		$ligne = explode(";",$req);// multiples requetes
		$taille=sizeof($ligne);// taille
		$query="";// requete complete
	for($i=0;$i<$taille-1;$i++){
		$query.="\n\r".$ligne[$i].";";// saut de ligne et ;
	}
	$con->exec($query);    
	  $this->setBrowserUrl(getenv('DOMAIN'));// l'avoir lancé avant
	}
	// nettoyage base
	/*public static function tearDownAfterClass(){
		$this->open("http://localhost:4444/selenium-server/driver?cmd=shutDownSeleniumServer");
	}*/
	protected function tearDown(){// boucle BROWSER
	//--- paramètres de connexion
    $host = 'localhost';//getHostByName(getHostName()); 'localhost';
    $port=3306;
    $username='root';
    $password='root';
    $db_name='incident';
	//--- connexion
    try {
        $con = new PDO("mysql:host={$host};dbname={$db_name};port={$port};",$username, $password);
        //echo "OK";
    }
    catch(PDOException $exception){
        echo "Erreur de connexion: " . $exception->getMessage();
    }  
	$query="
	TRUNCATE table incident;
	TRUNCATE table statut;
	";
	$con->exec($query); 
   }

  public function testEtatDesLieux(){
    $this->open("incident_list.php");
	$this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("Affichage d'incident"));    
  }/*
  //----- CREATE --------------------------------------------
  // filtrer moyen et urgent, ne rien trouver, créer, filtrer,trouver
  public function testCreer(){
	$this->open("incident_list.php");
	
	//$this->click("link=Recherche avancée");// SANS accents
	$this->click("//a[contains(text(),'Recherche avanc')]");	
    $this->click("name=severite[20]");
    $this->click("name=urgence[20]");
    $this->click("css=p.action > input[type=\"submit\"]");
    $this->waitForPageToLoad("30000");
	
	$this->assertContains("Aucun", $this->getText("css=body"));
	$this->click("link=en consigner un");
	$this->waitForPageToLoad("30000");
	
    $this->type("id=resume", "nouveau"); 
	$this->select("name=severite", "label=moyen");
    $this->select("name=urgence", "label=urgent");
    $this->type("id=description", "desc");
    $this->click("//input[@value=\"rapporter l'incident\"]");
    $this->waitForPageToLoad("30000");
	
	//$this->click("link=Recherche avancée");// SANS accents
	$this->click("//a[contains(text(),'Recherche avanc')]");	
    $this->click("name=severite[20]");
    $this->click("name=urgence[20]");
    $this->click("css=p.action > input[type=\"submit\"]");
    $this->waitForPageToLoad("30000");
	
    $this->assertEquals("nouveau", $this->getText("css=td.resume"));	
  } 
  //----- RETRIEVE ---------------------------------------
  // filtrer / classer / filtrer ET classer / paginer
  // rechercher par id, trouver ID-1
  public function testRechercher_par_id(){// obligé de préfixer test
	  $this->open("incident_list.php");// page (peu importe)
	  $this->type("name=id",1);// en champ id taper 1
	  $this->click("css=input[type=\"submit\"]");// cliquer sur -> prochain submit
	  $this->waitForPageToLoad("30000");
	  $this->assertTrue($this->isTextPresent("le titre de la page de listing n'est pas conforme"));
  }
  // recherche sauvegardee première, un résultat ID-5
  public function testRecherchercher_sauvegardee(){
	$this->open("incident_list.php");	
	//$this->click("link=Recherches personnalisees");// SANS accents
	$this->click("//a[contains(text(),'Recherches')]");	
    $this->click("id=0");// 1ere requete
    $this->click("css=#sauvees > p.action > input[type=\"submit\"]");
    $this->waitForPageToLoad("30000");
	// $this->assertEquals("a tester desormais", $this->getText("css=td.resume"));// SANS accents
	$this->assertContains("tester", $this->getText("css=td.statuts"));   
	$this->assertEquals("1 incident | en consigner un autre | exporter tout", $this->getText("css=div > p"));  	
  }  
  //----- UPDATE -------------------------------------------
  // lister, modifier premier lien, voir modification ID-2
  public function testModifier_par_liste(){
	 $this->open("incident_list.php");
	 $this->click("link=modifier");// 1er lien 
	 $this->waitForPageToLoad("30000");
	 $this->type("id=resume", "resume change");
	 $this->click("//input[@value=\"rapporter l'incident\"]");// submit
	 $this->waitForPageToLoad("30000");
     $this->assertEquals("resume change", $this->getText("css=td.resume"));
  }
  //----- DELETE -------------------------------------------------------
  // filtrer bloquant-immédiat-à préciser, trouver,supprimer et confirmer, filtrer, ne plus trouver ID-4
  // CONTRAINTES non utilise par d'autres
  public function testSupprimer(){
	 $this->open("incident_list.php");
	 $this->click("//a[contains(text(),'Recherche avanc')]");
	 $this->click("name=severite[40]");
	 $this->click("name=urgence[30]");
	 $this->click("name=statut[35]");
	 $this->click("css=p.action > input[type=\"submit\"]");
	 $this->waitForPageToLoad("30000");
	 
     $this->assertEquals("1 incident | en consigner un autre | exporter tout", $this->getText("css=div > p"));
     $this->assertEquals("4", $this->getText("css=td.id"));
     $this->click("link=supprimer");
     $this->waitForPageToLoad("30000");
     $this->click("link=supprimer");
     $this->waitForPageToLoad("30000");		 

	 $this->click("//a[contains(text(),'Recherche avanc')]");
	 $this->click("name=severite[40]");
	 $this->click("name=urgence[30]");
	 $this->click("name=statut[35]");
	 $this->click("css=p.action > input[type=\"submit\"]");
	 $this->waitForPageToLoad("30000");

	 $this->assertContains("Aucun", $this->getText("css=body"));	 
  }*/
  
 
}
?>