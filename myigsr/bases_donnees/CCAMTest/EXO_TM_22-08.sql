CREATE TABLE `EXO_TM` (
  `CODE` char(1) NOT NULL DEFAULT '',
  `LIBELLE` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`CODE`)
); 

INSERT INTO EXO_TM VALUES ('1','Acte pouvant etre exonere par la regle du seuil et exonerant alors la facture');
INSERT INTO EXO_TM VALUES ('2','Acte pouvant etre exonere par la regle du seuil mais n''exonerant pas la facture');
INSERT INTO EXO_TM VALUES ('3','Acte exonere par nature et non exonerant pour la facture');
INSERT INTO EXO_TM VALUES ('4','Acte exonere par nature et exonerant toute la facture');
INSERT INTO EXO_TM VALUES ('5','Acte ne pouvant pas être exonéré par la règle du seuil');
INSERT INTO EXO_TM VALUES ('7','Acte pouvant etre exonere par nature dans le cadre d''un dispositif de prevention');
