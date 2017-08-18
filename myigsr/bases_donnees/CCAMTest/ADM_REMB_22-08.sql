CREATE TABLE `ADM_REMB` (
  `CODE` char(1) NOT NULL DEFAULT '',
  `LIBELLE` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`CODE`)
); 

INSERT INTO ADM_REMB VALUES ('1','Acte remboursable');
INSERT INTO ADM_REMB VALUES ('2','Acte non remboursable');
INSERT INTO ADM_REMB VALUES ('3','Acte remboursable ou non suivant circonstances');
