CREATE TABLE `ACCES1` (
  `CODE` char(1) NOT NULL DEFAULT '',
  `ACCES` varchar(80) DEFAULT NULL,
  `LIBELLE` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`CODE`),
  KEY `ACCES` (`ACCES`)
); 

INSERT INTO ACCES1 VALUES ('A','ABORD OUVERT','acc�s exposant le site op�ratoire, par incision des t�guments et de tout autre tissu sousjacent, sans introduction d''un instrument d''optique.Par extension, concerne tout acc�s � travers la peau par une ouverture cutan�e d''origine.');
INSERT INTO ACCES1 VALUES ('B','ACC�S TRANSPARI�TAL','acc�s au site op�ratoire par ponction ou incision minime des t�guments et de tout autre tissu sousjacent, sans introduction d''un instrument d''optique');
INSERT INTO ACCES1 VALUES ('C','ACC�S ENDOSCOPIQUE TRANSPARI�TAL','acc�s au site op�ratoire par ponction ou incision minime des t�guments et de tout autre tissu sousjacent, avec introduction d''un instrument d''optique');
INSERT INTO ACCES1 VALUES ('D','ACC�S TRANSORIFICIEL','acc�s au site op�ratoire en passant par un orifice externe naturel ou artificiel (stomie cutan�e), sans introduction d''un instrument d''optique');
INSERT INTO ACCES1 VALUES ('E','ACC�S ENDOSCOPIQUE TRANSORIFICIEL','acc�s au site op�ratoire en passant par un orifice externe naturel ou artificiel (stomie cutan�e), avec introduction d''un instrument d''optique');
INSERT INTO ACCES1 VALUES ('F','ACC�S INTRALUMINAL TRANSPARI�TAL','acc�s au site op�ratoire par la lumi�re d''un conduit anatomique apr�s ponction ou incision minime des t�guments, sans introduction d''un instrument d''optique');
INSERT INTO ACCES1 VALUES ('G','ACC�S ENDOSCOPIQUE INTRALUMINAL TRANSPARI�TAL','acc�s au site op�ratoire par la lumi�re d''un conduit anatomique apr�s ponction ou incision minime des t�guments, avec introduction d''un instrument d''optique');
INSERT INTO ACCES1 VALUES ('H','ACTE PAR RAYONS X, AVEC ACC�S AUTRE QU''ABORD OUVERT','r�alisation d''un acte utilisant des rayons X, avec acc�s autre qu''un abord ouvert');
INSERT INTO ACCES1 VALUES ('J','ACTE PAR ULTRASONS OU REMNOGRAPHIEAVEC ACC�S AUTRE QU''ABORD OUVERT','r�alisation d''un acte utilisant des ultrasons ou la r�sonance magn�tique, avec acc�s autre qu''un abord ouvert');
INSERT INTO ACCES1 VALUES ('K','ACTE PAR RAYONS X, SANS ACC�S','r�alisation d''un acte utilisant des rayons X, sans acc�s');
INSERT INTO ACCES1 VALUES ('L','ACTE PAR AGENT IONISANT','r�alisation d''un acte utilisant des agents ionisants autres que les rayons X (isotopes radioactifs, particules �)');
INSERT INTO ACCES1 VALUES ('M','ACTE PAR ULTRASONS, SANS ACC�S','r�alisation d''un acte utilisant des ultrasons, sans acc�s');
INSERT INTO ACCES1 VALUES ('N','ACTE PAR REMNOGRAPHIE SANS ACC�S','r�alisation d''un acte utilisant la r�sonance magn�tique, sans acc�s');
INSERT INTO ACCES1 VALUES ('P','ACTE PAR AUTRE MOYEN, SANS ACC�S, OU NON PR�CIS�','r�alisation d''un acte utilisant d''autre moyen, sans acc�s, ou sans pr�cision sur ses modalit�s');
