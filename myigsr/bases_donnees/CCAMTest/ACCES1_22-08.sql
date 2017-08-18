CREATE TABLE `ACCES1` (
  `CODE` char(1) NOT NULL DEFAULT '',
  `ACCES` varchar(80) DEFAULT NULL,
  `LIBELLE` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`CODE`),
  KEY `ACCES` (`ACCES`)
); 

INSERT INTO ACCES1 VALUES ('A','ABORD OUVERT','accès exposant le site opératoire, par incision des téguments et de tout autre tissu sousjacent, sans introduction d''un instrument d''optique.Par extension, concerne tout accès à travers la peau par une ouverture cutanée d''origine.');
INSERT INTO ACCES1 VALUES ('B','ACCÈS TRANSPARIÉTAL','accès au site opératoire par ponction ou incision minime des téguments et de tout autre tissu sousjacent, sans introduction d''un instrument d''optique');
INSERT INTO ACCES1 VALUES ('C','ACCÈS ENDOSCOPIQUE TRANSPARIÉTAL','accès au site opératoire par ponction ou incision minime des téguments et de tout autre tissu sousjacent, avec introduction d''un instrument d''optique');
INSERT INTO ACCES1 VALUES ('D','ACCÈS TRANSORIFICIEL','accès au site opératoire en passant par un orifice externe naturel ou artificiel (stomie cutanée), sans introduction d''un instrument d''optique');
INSERT INTO ACCES1 VALUES ('E','ACCÈS ENDOSCOPIQUE TRANSORIFICIEL','accès au site opératoire en passant par un orifice externe naturel ou artificiel (stomie cutanée), avec introduction d''un instrument d''optique');
INSERT INTO ACCES1 VALUES ('F','ACCÈS INTRALUMINAL TRANSPARIÉTAL','accès au site opératoire par la lumière d''un conduit anatomique après ponction ou incision minime des téguments, sans introduction d''un instrument d''optique');
INSERT INTO ACCES1 VALUES ('G','ACCÈS ENDOSCOPIQUE INTRALUMINAL TRANSPARIÉTAL','accès au site opératoire par la lumière d''un conduit anatomique après ponction ou incision minime des téguments, avec introduction d''un instrument d''optique');
INSERT INTO ACCES1 VALUES ('H','ACTE PAR RAYONS X, AVEC ACCÈS AUTRE QU''ABORD OUVERT','réalisation d''un acte utilisant des rayons X, avec accès autre qu''un abord ouvert');
INSERT INTO ACCES1 VALUES ('J','ACTE PAR ULTRASONS OU REMNOGRAPHIEAVEC ACCÈS AUTRE QU''ABORD OUVERT','réalisation d''un acte utilisant des ultrasons ou la résonance magnétique, avec accès autre qu''un abord ouvert');
INSERT INTO ACCES1 VALUES ('K','ACTE PAR RAYONS X, SANS ACCÈS','réalisation d''un acte utilisant des rayons X, sans accès');
INSERT INTO ACCES1 VALUES ('L','ACTE PAR AGENT IONISANT','réalisation d''un acte utilisant des agents ionisants autres que les rayons X (isotopes radioactifs, particules …)');
INSERT INTO ACCES1 VALUES ('M','ACTE PAR ULTRASONS, SANS ACCÈS','réalisation d''un acte utilisant des ultrasons, sans accès');
INSERT INTO ACCES1 VALUES ('N','ACTE PAR REMNOGRAPHIE SANS ACCÈS','réalisation d''un acte utilisant la résonance magnétique, sans accès');
INSERT INTO ACCES1 VALUES ('P','ACTE PAR AUTRE MOYEN, SANS ACCÈS, OU NON PRÉCISÉ','réalisation d''un acte utilisant d''autre moyen, sans accès, ou sans précision sur ses modalités');
