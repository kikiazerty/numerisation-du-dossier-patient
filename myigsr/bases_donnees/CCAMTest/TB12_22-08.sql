CREATE TABLE `TB12` (
  `SERIE` int(11) NOT NULL AUTO_INCREMENT,
  `DATEDEBUT` char(8) DEFAULT NULL,
  `DATEFIN` char(8) DEFAULT NULL,
  `FORFAIT` decimal(7,2) DEFAULT NULL,
  `COEF` decimal(4,3) DEFAULT NULL,
  PRIMARY KEY (`SERIE`)
); 

INSERT INTO TB12 VALUES ('17','20050325','00000000','0.00','0.160');
