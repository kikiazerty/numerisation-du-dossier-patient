CREATE TABLE `REC_MED_ACTE` (
  `ACTE` varchar(13) DEFAULT NULL,
  `ACTIVITE` char(1) DEFAULT NULL,
  `RECMED` varchar(5) DEFAULT NULL,
  `TEXTE` text,
  KEY `ACTE` (`ACTE`),
  KEY `ACTIVITE` (`ACTIVITE`),
  KEY `RECMED` (`RECMED`)
); 

