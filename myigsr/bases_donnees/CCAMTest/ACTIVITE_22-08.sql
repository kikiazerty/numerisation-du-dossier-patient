CREATE TABLE `ACTIVITE` (
  `CODE` char(1) NOT NULL DEFAULT '',
  `LIBELLE` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CODE`)
); 

INSERT INTO ACTIVITE VALUES ('1','1° activité chir/med');
INSERT INTO ACTIVITE VALUES ('2','2° activité chir/med');
INSERT INTO ACTIVITE VALUES ('3','3° activité chir/med');
INSERT INTO ACTIVITE VALUES ('4','anesthésie');
INSERT INTO ACTIVITE VALUES ('5','circulation extracorporelle [CEC]');
