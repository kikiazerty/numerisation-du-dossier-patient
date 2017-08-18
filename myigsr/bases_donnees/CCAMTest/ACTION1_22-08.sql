CREATE TABLE `ACTION1` (
  `VERBE` varchar(20) NOT NULL DEFAULT '',
  `CODE` char(1) DEFAULT NULL,
  `LIBELLE` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`VERBE`),
  KEY `CODE` (`CODE`)
); 

INSERT INTO ACTION1 VALUES ('AGRANDIR','A','augmenter les dimensions (longueur, calibre, surface ou volume) d''un élément de l''organisme');
INSERT INTO ACTION1 VALUES ('COMBLER','B','emplir un espace ou une cavité en y apportant un matériau biologique ou artificiel');
INSERT INTO ACTION1 VALUES ('COMPRIMER','B','maintenir une pression sur une partie de l''organisme');
INSERT INTO ACTION1 VALUES ('RÉTRÉCIR','B','diminuer les dimensions (longueur, calibre, surface ou volume) d''un élément de l''organisme');
INSERT INTO ACTION1 VALUES ('DÉVIER','C','modifier le trajet d''un élément de l''organisme ou le parcours physiologique d''un fluide organique, pour contourner un obstacle ou mettre hors circuit [exclure] un segment tubulaire de l''organisme, sans pratiquer d''exérèse');
INSERT INTO ACTION1 VALUES ('OUVRIR','C','réaliser un orifice dans un organe à l''aide d''un instrument pointu');
INSERT INTO ACTION1 VALUES ('RÉUNIR','C','assembler des éléments anatomiques dont la continuité a été accidentellement ou intentionnellement interrompue. Établir la communication entre une cavité ou un organe creux (tubulaire ou non), et une autre partie creuse de l''organisme ou l''extérieur');
INSERT INTO ACTION1 VALUES ('FIXER','D','placer un organe, un segment anatomique ou un dispositif dans une position fixe et durable');
INSERT INTO ACTION1 VALUES ('DÉPLACER','E','modifier la place d''un organe ou d''une structure anatomique, afin de pallier l''absence ou la défaillance d''un organe ou d''une structure identique du même organisme ou d''un organisme différent');
INSERT INTO ACTION1 VALUES ('REPLACER','E','ramener un organe déplacé dans sa position anatomique normale, ou dans une position appropriée');
INSERT INTO ACTION1 VALUES ('EXCISER','F','retirer une partie de l''organisme en la séparant de celui-ci par une section');
INSERT INTO ACTION1 VALUES ('ENLEVER','G','retirer de l''organisme un élément qui lui étranger (corps étranger, matériau synthétique, prothèse)');
INSERT INTO ACTION1 VALUES ('ÉVIDER','G','retirer une partie de l''organisme en la séparant de celui-ci par clivage, creusement, forage ou grattage');
INSERT INTO ACTION1 VALUES ('EXTRAIRE','G','retirer un élément de l''organisme en le séparant de celui-ci par une traction plus ou moins importante exercée sur lui');
INSERT INTO ACTION1 VALUES ('PRÉLEVER','H','prendre une partie d''un élément biologique (solide ou liquide) ou d''une structure anatomique, pour examen ou analyse');
INSERT INTO ACTION1 VALUES ('DRAINER','J','diriger l''évacuation d''un fluide hors de son lieu d''origine dans l''organisme, par l''intermédiaire d''un dispositif adapté (drain, mèche, lame...) laissé en place');
INSERT INTO ACTION1 VALUES ('ÉVACUER','J','faire sortir un fluide de l''organisme sans laisser en place de dispositif particulier');
INSERT INTO ACTION1 VALUES ('NETTOYER','J','débarrasser une partie de l''organisme d''éléments indésirables ou nocifs');
INSERT INTO ACTION1 VALUES ('CHANGER','K','enlever un dispositif de l''organisme et remettre simultanément en place un dispositif identique ou analogue dans le même site');
INSERT INTO ACTION1 VALUES ('REMPLACER','K','retirer une partie altérée de l''organisme et y substituer simultanément un dispositif ou une structure anatomiquement identique et apte à remplir sa fonction');
INSERT INTO ACTION1 VALUES ('APPLIQUER','L','disposer un agent thérapeutique à visée locale ou générale à la surface de l''organisme ou d''une de ses parties, sans effraction des téguments');
INSERT INTO ACTION1 VALUES ('IMPLANTER','L','introduire un dispositif au sein d''une partie de l''organisme sans exérèse simultanée,  pour renforcer ou protéger une structure, surveiller, assister ou relayer une fonction, ou prendre la place d''une structure déficiente ou absente');
INSERT INTO ACTION1 VALUES ('INJECTER','L','introduire un agent biologique ou pharmacologique dans l''organisme, par un orifice naturel, un dispositif implanté ou une ponction');
INSERT INTO ACTION1 VALUES ('FABRIQUER','M','concevoir et réaliser un élément, une structure, une disposition ou un appareillage');
INSERT INTO ACTION1 VALUES ('PRÉPARER','M','rendre possible la réalisation d''un acte par un travail préalable');
INSERT INTO ACTION1 VALUES ('RÉGLER','M','mettre au point ou vérifier le fonctionnement d''un mécanisme ou d''un appareillage');
INSERT INTO ACTION1 VALUES ('RÉPARER','M','remettre une structure anatomique altérée dans un état normal ou proche de son état normal sans la remplacer, de façon à lui permettre de remplir son rôle physiologique ou de lui redonner un aspect proche de la normale. Se substituer à une fonction physio');
INSERT INTO ACTION1 VALUES ('RÉVISER','M','retourner sur un site pour apporter une correction au résultat précédent ou en vérifier le résultat');
INSERT INTO ACTION1 VALUES ('DÉTRUIRE','N','altérer la structure d''éléments physiologiques ou pathologiques de l''organisme au moyen d''agents mécaniques, physiques ou chimiques, de façon à en provoquer la disparition');
INSERT INTO ACTION1 VALUES ('FRAGMENTER','N','diviser en petites parties un élément solide contenu dans l''organisme');
INSERT INTO ACTION1 VALUES ('COUPER','P','sectionner un élément anatomique sans l''enlever');
INSERT INTO ACTION1 VALUES ('LIBÉRER','P','dégager un élément anatomique comprimé ou gêné dans son fonctionnement, au sein de l''organisme');
INSERT INTO ACTION1 VALUES ('SÉPARER','P','disjoindre des éléments anatomiques contigus. Isoler certains éléments contenus dans un milieu biologique à l''aide de techniques particulières de tri');
INSERT INTO ACTION1 VALUES ('ENREGISTRER','Q','produire et analyser un document durable reproduisant l''image du corps ou de ses organes, ou traduisant l''activité d''un organe, à l''aide d''un appareillage approprié');
INSERT INTO ACTION1 VALUES ('EXAMINER','Q','observer l''organisme ou un de ses éléments, directement ou à l''aide d''instruments, pour en étudier ou en suivre le fonctionnement, sans produire d''enregistrement durable');
INSERT INTO ACTION1 VALUES ('GUIDER','Q','aider à atteindre un élément profond de l''organisme dont l''abord aveugle à travers les téguments serait trop difficile ou trop dangereux, en orientant la trajectoire d''un instrument');
INSERT INTO ACTION1 VALUES ('MESURER','Q','déterminer la qualité ou la quantité de certains éléments biologiques au moyen d''une instrumentation ou d''une expérience adaptée');
INSERT INTO ACTION1 VALUES ('ÉDUQUER','R','mettre en œuvre des moyens propres à améliorer la formation et les connaissances d''un individu');
INSERT INTO ACTION1 VALUES ('PROVOQUER','R','susciter une réaction de l''organisme sous l''effet d''un facteur externe contrôlé ou d''un agent pharmacologique, de manière à en modifier le comportement ou à  en corriger une altération');
INSERT INTO ACTION1 VALUES ('RÉÉDUQUER','R','appliquer une méthode non effractive destinée à recouvrer l''usage partiel ou total d''une partie de l''organisme lésée');
INSERT INTO ACTION1 VALUES ('OCCLURE','S','fermer l''orifice ou la lumière d''une structure anatomique tubulaire sans la couper');
