CREATE TABLE `RGL_TARIF` (
  `CODE` char(1) NOT NULL DEFAULT '',
  `LIBELLE` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CODE`)
); 

INSERT INTO RGL_TARIF VALUES ('6','Association avec un geste complementaire ou un supplement');
