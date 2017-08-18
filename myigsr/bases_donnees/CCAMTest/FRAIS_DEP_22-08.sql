CREATE TABLE `FRAIS_DEP` (
  `CODE` char(1) NOT NULL DEFAULT '',
  `LIBELLE` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`CODE`)
); 

INSERT INTO FRAIS_DEP VALUES ('A','Autorise les frais de deplacement');
INSERT INTO FRAIS_DEP VALUES ('B','Autorise les indemnites de deplacement');
INSERT INTO FRAIS_DEP VALUES ('C','Autorise les indemnites kilometriques');
INSERT INTO FRAIS_DEP VALUES ('N','Interdit les frais de deplacement');
