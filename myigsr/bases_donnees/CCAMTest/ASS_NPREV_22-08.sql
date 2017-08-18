CREATE TABLE `ASS_NPREV` (
  `CODE` char(1) NOT NULL DEFAULT '',
  `LIBELLE` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CODE`)
); 

INSERT INTO ASS_NPREV VALUES ('1','Acte de tarif le plus eleve, geste complementaire, supplement, toujours factures a taux plein');
INSERT INTO ASS_NPREV VALUES ('2','Acte associe facture a 50 % de sa valeur');
INSERT INTO ASS_NPREV VALUES ('3','Acte associe facture a 75 % de sa valeur');
INSERT INTO ASS_NPREV VALUES ('4','Acte specifique facture a 100 % de sa valeur');
INSERT INTO ASS_NPREV VALUES ('5','Acte facture a 100 % de sa valeur - cas particulier actes a des moments non continus de meme journee');
