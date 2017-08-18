CREATE TABLE `TYPENOTE` (
  `CODE` char(2) NOT NULL DEFAULT '',
  `LIBELLE` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CODE`)
); 

INSERT INTO TYPENOTE VALUES ('01','A l exclusion de');
INSERT INTO TYPENOTE VALUES ('02','Comprend');
INSERT INTO TYPENOTE VALUES ('03','Avec ou sans');
INSERT INTO TYPENOTE VALUES ('04','Par, ..., on entend');
INSERT INTO TYPENOTE VALUES ('05','Par exemple');
INSERT INTO TYPENOTE VALUES ('06','Coder eventuellement');
INSERT INTO TYPENOTE VALUES ('07','Modificateur implicite');
INSERT INTO TYPENOTE VALUES ('08','Inclut');
INSERT INTO TYPENOTE VALUES ('09','Non structure');
INSERT INTO TYPENOTE VALUES ('10','Premiere phase ou premiere activite');
INSERT INTO TYPENOTE VALUES ('11','Deuxieme phase ou deuxieme activite');
INSERT INTO TYPENOTE VALUES ('12','Troisieme phase ou troisieme activite');
INSERT INTO TYPENOTE VALUES ('13','Condition de prise en charge : Indication specifique');
INSERT INTO TYPENOTE VALUES ('14','Condition de prise en charge : Formation specifique');
INSERT INTO TYPENOTE VALUES ('15','Condition de prise en charge : Environnement specifique');
INSERT INTO TYPENOTE VALUES ('16','Condition de prise en charge : Recueil prospectif de donnees');
INSERT INTO TYPENOTE VALUES ('17','Condition de prise en charge : Facturation');
