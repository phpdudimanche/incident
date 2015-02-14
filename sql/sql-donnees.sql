TRUNCATE table incident;
INSERT INTO incident (resume, description, severite, urgence) VALUES 
("le titre de la page de listing n'est pas conforme", "ATTENDU : listes des éléments / OBTENU : tous les éléments", 10, 10),
("la page des CGV n'est pas accessible","En cliquant sur le lien CGV en bas de page, erreur 404",40,30),
("le membre ne peut ajouter son icone","Comme dans un forum, un membre peut avoir une petite image",10,10),
("le paiement peut être négatif en cumulant des promotions et avoirs","ATTENDU : rien de précisé sur le sujet / OBTENU si les avoirs dépassent la somme à payer, la banque reçoit une transaction négative et crédite le client",40,30),
("a tester desormais","est corrigé, à retester",40,30);
TRUNCATE table statut;
INSERT INTO statut (id_incident, statut, date) VALUES 
(1,10,19941018020202),
(2,10,19951018020202),
(3,10,19961018020202),(3,20,20101018020202),(3,40,20101019020202),
(4,10,19971018020202),(4,35,19971020020202),
(5,10,20150211035625),(5,20,20150211045625),
(5,30,20150212055625),(5,40,20150212059625),
(5,50,20150213035625),(5,60,20150214035625),
(5,70,20150215035625);