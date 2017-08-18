CREATE TABLE `t0A` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t0G` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` char(13) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f5` longtext,
  `fA` int(4) DEFAULT NULL,
  `fB` varchar(25) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fC` datetime DEFAULT NULL,
  `f5_UPDATED` datetime DEFAULT NULL,
  `fD` char(13) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fG` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f1`)
);

CREATE TABLE `t0H` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` char(13) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f5` longtext,
  `fA` int(4) DEFAULT NULL,
  `fB` varchar(25) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fC` datetime DEFAULT NULL,
  `f5_UPDATED` datetime DEFAULT NULL,
  `fD` char(13) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fG` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f1`)
);

CREATE TABLE `t0I` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` char(13) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f5` longtext,
  `fA` int(4) DEFAULT NULL,
  `fB` varchar(25) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fC` datetime DEFAULT NULL,
  `f5_UPDATED` datetime DEFAULT NULL,
  `fD` char(13) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fG` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f1`)
);

CREATE TABLE `t0J` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` char(13) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f6` smallint(2) DEFAULT NULL,
  `f7` float DEFAULT NULL,
  `f8` smallint(2) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t0K` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` smallint(2) DEFAULT NULL,
  `f3` char(13) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t0L` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` float DEFAULT NULL,
  `f2` float DEFAULT NULL,
  `f3` float DEFAULT NULL,
  `f4` char(3) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f5` char(3) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f6` int(4) DEFAULT NULL,
  `f7` int(4) DEFAULT NULL,
  `f8` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f9` float DEFAULT NULL,
  `fA` float DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t0M` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` varchar(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t0N` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` datetime DEFAULT NULL,
  `f3` longtext,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t0O` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t00` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` char(13) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f6` varchar(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f7` smallint(2) DEFAULT NULL,
  `f8` int(4) DEFAULT NULL,
  `f9` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fA` int(4) DEFAULT NULL,
  `fB` varchar(25) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fD` char(13) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fE` tinyint(1) unsigned DEFAULT NULL,
  `fG` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fH` char(2) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fI` varchar(30) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fJ` char(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fK` varchar(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fL` int(4) DEFAULT NULL,
  `fM` varchar(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fN` char(14) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fO` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fP` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f1`)
);

CREATE TABLE `t01` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` float DEFAULT NULL,
  `f2` float DEFAULT NULL,
  `f3` float DEFAULT NULL,
  `f4` tinyint(1) unsigned DEFAULT NULL,
  `f5` float DEFAULT NULL,
  `f6` tinyint(1) unsigned DEFAULT NULL,
  `f7` tinyint(1) unsigned DEFAULT NULL,
  `f8` bit(1) DEFAULT NULL,
  `f9` double DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t02` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` tinyint(1) unsigned DEFAULT NULL,
  `f3` tinyint(1) unsigned DEFAULT NULL,
  `f4` float DEFAULT NULL,
  `f5` float DEFAULT NULL,
  `f6` float DEFAULT NULL,
  `f7` float DEFAULT NULL,
  `f8` char(8) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f9` tinyint(1) unsigned DEFAULT NULL,
  `fA` smallint(2) DEFAULT NULL,
  `fB` tinyint(1) unsigned DEFAULT NULL,
  `fC` smallint(2) DEFAULT NULL,
  `fD` smallint(2) DEFAULT NULL,
  `fE` tinyint(1) unsigned DEFAULT NULL,
  `fF` smallint(2) DEFAULT NULL,
  `fG` int(4) DEFAULT NULL,
  `fH` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t07` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` tinyint(1) unsigned DEFAULT NULL,
  `f2` char(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` tinyint(1) unsigned DEFAULT NULL,
  `f4` float DEFAULT NULL,
  `f5` smallint(2) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t08` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` tinyint(1) unsigned DEFAULT NULL,
  `f2` int(4) DEFAULT NULL,
  `f3` bit(1) DEFAULT NULL,
  `f4` bit(1) DEFAULT NULL,
  `f5` float DEFAULT NULL,
  `f6` smallint(2) DEFAULT NULL,
  `f7` float DEFAULT NULL,
  `f8` smallint(2) DEFAULT NULL,
  `f9` smallint(2) DEFAULT NULL,
  `fA` smallint(2) DEFAULT NULL,
  `fB` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t1A` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` tinyint(1) unsigned DEFAULT NULL,
  `f1` varchar(65) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t1C` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` longtext,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t1D` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` tinyint(1) unsigned DEFAULT NULL,
  `f1` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` tinyint(1) unsigned DEFAULT NULL,
  `f3` float DEFAULT NULL,
  `f4` float DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t1F` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` tinyint(1) unsigned DEFAULT NULL,
  `f1` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t1G` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` tinyint(1) unsigned DEFAULT NULL,
  `f1` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t1L` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(3) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t1Y` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t2A` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(65) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` float DEFAULT NULL,
  `f3` float DEFAULT NULL,
  `f4` float DEFAULT NULL,
  `f5` float DEFAULT NULL,
  `f6` smallint(2) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t2C` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fZ` varchar(17) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t2G` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` double DEFAULT NULL,
  `f2` double DEFAULT NULL,
  `f3` smallint(2) DEFAULT NULL,
  `f4` int(4) DEFAULT NULL,
  `f6` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`,`f3`,`f4`)
);

CREATE TABLE `t2L` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` double DEFAULT NULL,
  `f2` double DEFAULT NULL,
  `f3` smallint(2) DEFAULT NULL,
  `f4` int(4) DEFAULT NULL,
  `f6` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`,`f3`,`f4`)
);

CREATE TABLE `t2M` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t2P` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` float DEFAULT NULL,
  `f3` smallint(2) DEFAULT NULL,
  `f4` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f5` float DEFAULT NULL,
  `f6` smallint(2) DEFAULT NULL,
  `f7` float DEFAULT NULL,
  `f8` smallint(2) DEFAULT NULL,
  `f9` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fA` float DEFAULT NULL,
  `fB` smallint(2) DEFAULT NULL,
  `fC` float DEFAULT NULL,
  `fD` smallint(2) DEFAULT NULL,
  `fE` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fF` float DEFAULT NULL,
  `fG` smallint(2) DEFAULT NULL,
  `fH` float DEFAULT NULL,
  `fI` smallint(2) DEFAULT NULL,
  `fJ` float DEFAULT NULL,
  `fK` smallint(2) DEFAULT NULL,
  `fL` float DEFAULT NULL,
  `fM` smallint(2) DEFAULT NULL,
  `fN` float DEFAULT NULL,
  `fO` smallint(2) DEFAULT NULL,
  `fP` bit(1) DEFAULT NULL,
  `fQ` float DEFAULT NULL,
  `fR` smallint(2) DEFAULT NULL,
  `fS` float DEFAULT NULL,
  `fT` smallint(2) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t2R` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` smallint(2) DEFAULT NULL,
  `f1` smallint(2) DEFAULT NULL,
  `f2` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` double DEFAULT NULL,
  `f4` double DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t2S` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` float DEFAULT NULL,
  `f3` smallint(2) DEFAULT NULL,
  `f4` float DEFAULT NULL,
  `f5` smallint(2) DEFAULT NULL,
  `f6` float DEFAULT NULL,
  `f7` smallint(2) DEFAULT NULL,
  `f8` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t3A` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` float DEFAULT NULL,
  `f4` smallint(2) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t3B` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` float DEFAULT NULL,
  `f3` smallint(2) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t3C` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` smallint(2) DEFAULT NULL,
  `f4` longtext,
  `f5` datetime DEFAULT NULL,
  `f6` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f7` smallint(2) DEFAULT NULL,
  `f8` longtext,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t3D` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t3E` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` smallint(2) DEFAULT NULL,
  `f1` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t3F` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` tinyint(1) unsigned DEFAULT NULL,
  `f3` int(4) DEFAULT NULL,
  `f5` longtext,
  `fC` datetime DEFAULT NULL,
  `f5_UPDATED` datetime DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t3G` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t3H` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t3I` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` int(4) DEFAULT NULL,
  `f3` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` float DEFAULT NULL,
  `f5` datetime DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t4A` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` longtext,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t4B` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` smallint(2) DEFAULT NULL,
  `f2` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` char(8) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` smallint(2) DEFAULT NULL,
  `f5` longtext,
  `f6` smallint(2) DEFAULT NULL,
  `f7` varchar(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f8` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f9` tinyint(1) unsigned DEFAULT NULL,
  `fA` varchar(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fB` int(4) DEFAULT NULL,
  `fC` datetime DEFAULT NULL,
  `f5_UPDATED` datetime DEFAULT NULL,
  `fD` int(4) DEFAULT NULL,
  `fE` longtext,
  `fF` bit(1) DEFAULT NULL,
  `fG` bit(1) DEFAULT NULL,
  `fI` char(30) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fJ` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fP` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t4C` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` datetime DEFAULT NULL,
  `f3` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t4D` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` tinyint(1) unsigned DEFAULT NULL,
  `f3` int(4) DEFAULT NULL,
  `f5` longtext,
  `fC` datetime DEFAULT NULL,
  `f5_UPDATED` datetime DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t4E` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` int(4) DEFAULT NULL,
  `f4` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f5` float DEFAULT NULL,
  `f6` char(3) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f7` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f8` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f9` float DEFAULT NULL,
  `fA` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fB` float DEFAULT NULL,
  `fC` datetime DEFAULT NULL,
  `fD` float DEFAULT NULL,
  `fE` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t4F` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t4G` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t4H` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t4I` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f2` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` datetime DEFAULT NULL,
  `f5` datetime DEFAULT NULL,
  `f6` varchar(250) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f7` varchar(250) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f2`,`f3`,`f4`)
);

CREATE TABLE `t4J` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(2) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` datetime DEFAULT NULL,
  `f5` datetime DEFAULT NULL,
  `f6` varchar(250) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f7` varchar(250) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`,`f3`,`f4`)
);

CREATE TABLE `t4K` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` float DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t4M` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t4N` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f2` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t4O` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t4P` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t4Q` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t4R` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` double DEFAULT NULL,
  `f3` double DEFAULT NULL,
  `f4` smallint(2) DEFAULT NULL,
  `f5` double DEFAULT NULL,
  `f6` smallint(2) DEFAULT NULL,
  `f7` double DEFAULT NULL,
  `f8` double DEFAULT NULL,
  `f9` smallint(2) DEFAULT NULL,
  `fA` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fB` double DEFAULT NULL,
  `fC` double DEFAULT NULL,
  `fD` double DEFAULT NULL,
  `fE` double DEFAULT NULL,
  `fF` float DEFAULT NULL,
  `fG` float DEFAULT NULL,
  `fH` int(4) DEFAULT NULL,
  `fI` double DEFAULT NULL,
  `fJ` bit(1) DEFAULT NULL,
  `fK` bit(1) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t4S` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t4T` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t5A` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` int(4) DEFAULT NULL,
  `f3` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t5B` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` smallint(2) DEFAULT NULL,
  `f2` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f5` longtext,
  `f7` varchar(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f8` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fC` datetime DEFAULT NULL,
  `f5_UPDATED` datetime DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t5C` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` longtext,
  `f4` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fC` datetime DEFAULT NULL,
  `f3_UPDATED` datetime DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t5D` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t5E` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t5G` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t5H` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t5I` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t5J` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t10` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` smallint(2) DEFAULT NULL,
  `f1` smallint(2) DEFAULT NULL,
  `f2` tinyint(1) unsigned DEFAULT NULL,
  `f4` int(4) DEFAULT NULL,
  `f5` longtext,
  `f6` int(4) DEFAULT NULL,
  `fC` datetime DEFAULT NULL,
  `f5_UPDATED` datetime DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t11` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t13` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` varchar(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t14` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` varchar(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` longtext,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t15` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` tinyint(1) unsigned DEFAULT NULL,
  `f1` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(4) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t16` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` tinyint(1) unsigned DEFAULT NULL,
  `f1` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` double DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t17` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` char(14) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` char(4) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f5` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f6` varchar(65) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f7` datetime DEFAULT NULL,
  `f8` int(4) DEFAULT NULL,
  `fP` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t18` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` tinyint(1) unsigned DEFAULT NULL,
  `f2` varchar(65) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f5` varchar(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f6` varchar(34) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f7` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f8` varchar(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f9` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fA` tinyint(1) unsigned DEFAULT NULL,
  `fB` varchar(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fC` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fD` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t20` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` varchar(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(65) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t23` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` longtext,
  `f2` longtext,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t24` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` smallint(2) DEFAULT NULL,
  `f1` varchar(50) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` double DEFAULT NULL,
  `f3` smallint(2) DEFAULT NULL,
  `f4` varchar(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f5` varchar(50) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t25` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` smallint(2) DEFAULT NULL,
  `f1` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` varchar(8) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f5` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t26` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` smallint(2) DEFAULT NULL,
  `f1` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` tinyint(1) unsigned DEFAULT NULL,
  `f4` longtext,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t27` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f6` smallint(2) DEFAULT NULL,
  `f8` varchar(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fB` char(2) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fC` varchar(15) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fD` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fE` longtext,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t28` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t29` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(65) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` datetime DEFAULT NULL,
  `f3` varchar(65) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` int(4) DEFAULT NULL,
  `f5` varchar(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f6` varchar(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f7` varchar(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t31` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t32` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` smallint(2) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t34` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` tinyint(1) unsigned DEFAULT NULL,
  `f1` varchar(65) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t37` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` smallint(2) DEFAULT NULL,
  `f1` smallint(2) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t38` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` bit(1) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t39` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` smallint(2) DEFAULT NULL,
  `f3` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` float DEFAULT NULL,
  `f5` smallint(2) DEFAULT NULL,
  `f6` longtext,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t41` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(2) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t42` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` varchar(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t43` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` varchar(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` tinyint(1) unsigned DEFAULT NULL,
  `f2` float DEFAULT NULL,
  `f3` smallint(2) DEFAULT NULL,
  `f4` char(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t45` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(8) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` longtext,
  `f4` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f5` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `fC` datetime DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t46` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` varchar(50) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` int(4) DEFAULT NULL,
  `fC` datetime DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t47` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(8) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` varchar(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` varchar(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t48` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t49` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t50` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` varchar(15) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` tinyint(1) unsigned DEFAULT NULL,
  `f4` tinyint(1) unsigned DEFAULT NULL,
  `f5` tinyint(1) unsigned DEFAULT NULL,
  `f6` tinyint(1) unsigned DEFAULT NULL,
  `f7` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t51` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t52` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` int(4) DEFAULT NULL,
  `f3` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f5` longtext,
  `f6` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t54` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(3) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` bit(1) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t55` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t56` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t57` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  `f2` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t58` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` char(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t59` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t60` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` tinyint(1) unsigned DEFAULT NULL,
  `f1` varchar(65) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t61` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`)
);

CREATE TABLE `t62` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(2) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t63` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` tinyint(1) unsigned DEFAULT NULL,
  `f2` longtext,
  `f3` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` int(4) DEFAULT NULL,
  `f5` tinyint(1) unsigned DEFAULT NULL,
  `f6` int(4) DEFAULT NULL,
  `f7` int(4) DEFAULT NULL,
  `f8` int(4) DEFAULT NULL,
  `f9` int(4) DEFAULT NULL,
  `fA` int(4) DEFAULT NULL,
  `fB` bit(1) DEFAULT NULL,
  `fC` bit(1) DEFAULT NULL,
  `fD` int(4) DEFAULT NULL,
  `fE` bit(1) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t64` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t65` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t70` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` char(6) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` int(4) DEFAULT NULL,
  `f4` varchar(7) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f6` varchar(5) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t71` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` int(4) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `t72` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `t73` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` int(4) DEFAULT NULL,
  `f1` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `tFB` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(250) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `tFD` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(35) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` bit(1) DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `tFE` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(2) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` varchar(75) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` tinyint(1) unsigned DEFAULT NULL,
  `f4` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f5` bit(1) DEFAULT NULL,
  `f6` tinyint(1) unsigned DEFAULT NULL,
  `f7` tinyint(1) unsigned DEFAULT NULL,
  `f8` longtext,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`)
);

CREATE TABLE `tFF` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(2) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` varchar(120) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` longtext,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`)
);

CREATE TABLE `tFH` (
  `ge` int(11) NOT NULL AUTO_INCREMENT,
  `f0` char(2) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f1` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f2` char(2) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f3` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f4` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`ge`),
  UNIQUE KEY `a0prim` (`f0`,`f1`,`f2`,`f3`,`f4`)
);

