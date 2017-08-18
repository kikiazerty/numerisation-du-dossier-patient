CREATE TABLE `COD_REGROUP` (
  `CODE` char(3) NOT NULL DEFAULT '',
  `LIBELLE` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CODE`)
); 

INSERT INTO COD_REGROUP VALUES ('ACO','Acte d obstetrique');
INSERT INTO COD_REGROUP VALUES ('ADA','Acte d anesthesie');
INSERT INTO COD_REGROUP VALUES ('ADC','Actes de chirurgie');
INSERT INTO COD_REGROUP VALUES ('ADE','Acte d echographie');
INSERT INTO COD_REGROUP VALUES ('ADI','Acte d imagerie');
INSERT INTO COD_REGROUP VALUES ('ATM','Acte technique medical');
INSERT INTO COD_REGROUP VALUES ('AXI','Actes de Prophylaxie et Pr�vention');
INSERT INTO COD_REGROUP VALUES ('CAM','Code Interne');
INSERT INTO COD_REGROUP VALUES ('DEN','Acte dentaire');
INSERT INTO COD_REGROUP VALUES ('END','actes d endodontie');
INSERT INTO COD_REGROUP VALUES ('ICO','Inlay-core');
INSERT INTO COD_REGROUP VALUES ('IMP','Pose d implants ou de mat�riel pour implantologie');
INSERT INTO COD_REGROUP VALUES ('INO','Actes Inlay-Onlay');
INSERT INTO COD_REGROUP VALUES ('PAM','Proth�ses amovibles d�finitives m�tallique');
INSERT INTO COD_REGROUP VALUES ('PAR','Proth�ses amovibles d�finitives r�sine');
INSERT INTO COD_REGROUP VALUES ('PDT','Proth�ses dentaires provisoires');
INSERT INTO COD_REGROUP VALUES ('PFC','Proth�ses Fixes C�ramiques');
INSERT INTO COD_REGROUP VALUES ('PFM','Proth�ses Fixes M�talliques');
INSERT INTO COD_REGROUP VALUES ('RPN','R�parations sur proth�se');
INSERT INTO COD_REGROUP VALUES ('SDE','Soins Dentaires');
INSERT INTO COD_REGROUP VALUES ('TDS','Parodontologie (actes sur tissus de soutien de la dent)');
