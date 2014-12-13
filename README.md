# Gestionnaire d'incident optimisé.
## Optimisé veut dire : 
- Ne pas utiliser de grosse librairie, type jquery, twiterbootstrap sauf obligé.
- Ne pas être tributaire de framework lourd ou léger (symfony2 ou phpmvc).
- Ne pas multiplier les intermédiaires (template twig).
- Etre rustique pour être indépendant mais être propre pour être maintenable.
- Aller droit à l'essentiel dans une approche relativement LEAN.
- Se recentrer sur le coeur de métier (dans une optique ISTQB).
- Chercher la valeur ajoutée différenciatrice des autres outils du même type.
- Pour le reste, prévoir plus tard des interfaces xmlrpc pour les gestionnaires de :
    - lotissement projet,
    - version de code source
    - et traçabilité documentaire,
    - risque,
    - exigences,
    - référentiel de test.

## Optimisé pour le code :
- Les pages ont vocation à être en nombre limité : pas de répertoire afin de vérifier ce principe.
- Le code est en POO PDO.
- La répartition logique des pages par bloc métier est :
    - ClassMetier.php         fonctions display incluse
    - PageMetier_form.php     formulaire de création et modification
    - PageMetier_act.php      script de traitement : Create Update Delete, et affichage d'alertes
    - PageMetier_list.php     script de traitement at affichage : Retrieve avec options pour listing, détails. 
- Les pages type header, footer, side, à vocation de template pour inclusion son nommées _PAGE.php.

## Optimisé pour le métier :
- Inspiré de Mantis en retenant l'essentiel.
- Version 0 sans multiprojet : une installation par projet.
- Version 0 sans paramétrage par IHM ni adaptation aux catégories.

## Installation :
- Les scripts sql sont fournis.
- Le fichier de configuration "_config.php" est à renommer "config.php".