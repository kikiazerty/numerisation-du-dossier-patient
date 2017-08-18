CREATE TABLE `CAT_MED` (
  `CODE` char(2) NOT NULL DEFAULT '',
  `LIBELLE` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CODE`)
); 

INSERT INTO CAT_MED VALUES ('AD','Acte Dentaire');
INSERT INTO CAT_MED VALUES ('AP','Anesthesie organe pair');
INSERT INTO CAT_MED VALUES ('AR','Acte de Reanimation');
INSERT INTO CAT_MED VALUES ('CP','Chirurgie organe pair');
INSERT INTO CAT_MED VALUES ('ID','Imagerie Dentaire');
INSERT INTO CAT_MED VALUES ('PD','Prothese dentaire');
INSERT INTO CAT_MED VALUES ('99','Valeur sans signification');
