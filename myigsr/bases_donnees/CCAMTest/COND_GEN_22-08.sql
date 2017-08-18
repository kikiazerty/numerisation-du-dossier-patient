CREATE TABLE `COND_GEN` (
  `CODE` varchar(4) NOT NULL DEFAULT '',
  `LIBELLE` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CODE`)
); 

INSERT INTO COND_GEN VALUES ('0001','Pris en charge et remboursable');
INSERT INTO COND_GEN VALUES ('0002','Pris en charge mais non affecté d''un honoraire');
INSERT INTO COND_GEN VALUES ('0003','Pris en charge et remboursable sous conditions');
INSERT INTO COND_GEN VALUES ('0004','Non pris en charge');
