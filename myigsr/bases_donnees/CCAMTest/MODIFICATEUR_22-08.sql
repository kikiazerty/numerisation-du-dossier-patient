CREATE TABLE `MODIFICATEUR` (
  `CODE` char(1) NOT NULL DEFAULT '',
  `LIBELLE` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CODE`)
); 

INSERT INTO MODIFICATEUR VALUES ('A','Anesthesie patient < 4 ans ou > 80 ans');
INSERT INTO MODIFICATEUR VALUES ('B','Radio. au bloc operatoire, unite de rea. ou lit du patient intransportable');
INSERT INTO MODIFICATEUR VALUES ('C','Radio. comparative des membres');
INSERT INTO MODIFICATEUR VALUES ('D','Controle radio. segment squelette immobilis� par contention rigide');
INSERT INTO MODIFICATEUR VALUES ('E','Radiologie conventionnelle ou scanographie patient < 5 ans');
INSERT INTO MODIFICATEUR VALUES ('F','Actes en urgence dimanche ou jour ferie');
INSERT INTO MODIFICATEUR VALUES ('G','Glaucome... patient < 1 an ; Extraction oeso ou bronch. patient < 3 ans ; Med. nuc. patient < 3 ans');
INSERT INTO MODIFICATEUR VALUES ('H','Radiotherapie niveau 1');
INSERT INTO MODIFICATEUR VALUES ('I','Modificateur transitoire de convergence vers la cible, valeur 3');
INSERT INTO MODIFICATEUR VALUES ('J','Majoration transitoire de chirurgie');
INSERT INTO MODIFICATEUR VALUES ('K','Majoration forfaits modulables accouchements gyneco. et chir sect. 1 ou 2 adherant,pour actes avec J');
INSERT INTO MODIFICATEUR VALUES ('L','Traitement fracture ou luxation ouverte');
INSERT INTO MODIFICATEUR VALUES ('M','Urgence cabinet m�decin g�n�raliste ou du p�diatre, apr�s examen en urgence d un patient');
INSERT INTO MODIFICATEUR VALUES ('N','Majoration acte de restauration tissus durs et/ou endodontie dent permanente enfant < 13 ans');
INSERT INTO MODIFICATEUR VALUES ('O','Modificateur transitoire de convergence vers la cible, valeur 1');
INSERT INTO MODIFICATEUR VALUES ('P','Acte r�alis� en urgence par les p�diatres et m�decins g�n�ralistes de 20h � 00h');
INSERT INTO MODIFICATEUR VALUES ('Q','Radiotherapie niveau 2');
INSERT INTO MODIFICATEUR VALUES ('R','Chirurgie plastique t�guments face, cou, main et doigts');
INSERT INTO MODIFICATEUR VALUES ('S','Urgence nuit 00h-08h pediatres et med. gen. ou autres med. pr acte th�rapeutique sous anesth�sie');
INSERT INTO MODIFICATEUR VALUES ('T','Bilateral, hors radiologie conventionnelle et chirurgie sur les membres');
INSERT INTO MODIFICATEUR VALUES ('U','Urgence hors pediatres et omnipraticiens nuit 20 h - 8 h');
INSERT INTO MODIFICATEUR VALUES ('V','Radiotherapie niveau 3');
INSERT INTO MODIFICATEUR VALUES ('W','Radiotherapie niveau 4');
INSERT INTO MODIFICATEUR VALUES ('X','Modificateur transitoire de convergence vers la cible, valeur 4');
INSERT INTO MODIFICATEUR VALUES ('Y','Radiographie realisee par pneumologue ou rhumatologue');
INSERT INTO MODIFICATEUR VALUES ('Z','Radiographie realisee par radiologue');
INSERT INTO MODIFICATEUR VALUES ('6','Intervention iterative glaucome..., voies biliaires ou urinaires');
INSERT INTO MODIFICATEUR VALUES ('7','Presence permanente anesthesiste durant intervention');
INSERT INTO MODIFICATEUR VALUES ('8','Anesthesie intervention iterative glaucome..., voies biliaires ou urinaires');
INSERT INTO MODIFICATEUR VALUES ('9','Modificateur transitoire de convergence vers la cible, valeur 2');
