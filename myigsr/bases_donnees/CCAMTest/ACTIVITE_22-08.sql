CREATE TABLE `ACTIVITE` (
  `CODE` char(1) NOT NULL DEFAULT '',
  `LIBELLE` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CODE`)
); 

INSERT INTO ACTIVITE VALUES ('1','1� activit� chir/med');
INSERT INTO ACTIVITE VALUES ('2','2� activit� chir/med');
INSERT INTO ACTIVITE VALUES ('3','3� activit� chir/med');
INSERT INTO ACTIVITE VALUES ('4','anesth�sie');
INSERT INTO ACTIVITE VALUES ('5','circulation extracorporelle [CEC]');
