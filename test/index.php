<?php
print("<h1>Lecture de rapport Junit</h1>");

if(file_exists('rapport\rapport-selenium.xml')){
	$recup=simplexml_load_file('rapport\rapport-selenium.xml'); 
	$failure=$recup->xpath('/testsuites/testsuite[1]/@failures');
	$error=$recup->xpath('/testsuites/testsuite[1]/@errors');
	$echec=$failure[0]['failures'];//print_r($failure);
	$erreur=$error[0]['errors'];//print_r($error);
	echo "Rapport pour Selenium : ";
	if(($echec!=0)OR($erreur!=0)){
	echo "KO : echec=".$echec." erreur=".$erreur;
	}
	else{
	echo "OK";
	}
}
?>