CREATE TABLE `ACTION1` (
  `VERBE` varchar(20) NOT NULL DEFAULT '',
  `CODE` char(1) DEFAULT NULL,
  `LIBELLE` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`VERBE`),
  KEY `CODE` (`CODE`)
); 

INSERT INTO ACTION1 VALUES ('AGRANDIR','A','augmenter les dimensions (longueur, calibre, surface ou volume) d''un �l�ment de l''organisme');
INSERT INTO ACTION1 VALUES ('COMBLER','B','emplir un espace ou une cavit� en y apportant un mat�riau biologique ou artificiel');
INSERT INTO ACTION1 VALUES ('COMPRIMER','B','maintenir une pression sur une partie de l''organisme');
INSERT INTO ACTION1 VALUES ('R�TR�CIR','B','diminuer les dimensions (longueur, calibre, surface ou volume) d''un �l�ment de l''organisme');
INSERT INTO ACTION1 VALUES ('D�VIER','C','modifier le trajet d''un �l�ment de l''organisme ou le parcours physiologique d''un fluide organique, pour contourner un obstacle ou mettre hors circuit [exclure] un segment tubulaire de l''organisme, sans pratiquer d''ex�r�se');
INSERT INTO ACTION1 VALUES ('OUVRIR','C','r�aliser un orifice dans un organe � l''aide d''un instrument pointu');
INSERT INTO ACTION1 VALUES ('R�UNIR','C','assembler des �l�ments anatomiques dont la continuit� a �t� accidentellement ou intentionnellement interrompue. �tablir la communication entre une cavit� ou un organe creux (tubulaire ou non), et une autre partie creuse de l''organisme ou l''ext�rieur');
INSERT INTO ACTION1 VALUES ('FIXER','D','placer un organe, un segment anatomique ou un dispositif dans une position fixe et durable');
INSERT INTO ACTION1 VALUES ('D�PLACER','E','modifier la place d''un organe ou d''une structure anatomique, afin de pallier l''absence ou la d�faillance d''un organe ou d''une structure identique du m�me organisme ou d''un organisme diff�rent');
INSERT INTO ACTION1 VALUES ('REPLACER','E','ramener un organe d�plac� dans sa position anatomique normale, ou dans une position appropri�e');
INSERT INTO ACTION1 VALUES ('EXCISER','F','retirer une partie de l''organisme en la s�parant de celui-ci par une section');
INSERT INTO ACTION1 VALUES ('ENLEVER','G','retirer de l''organisme un �l�ment qui lui �tranger (corps �tranger, mat�riau synth�tique, proth�se)');
INSERT INTO ACTION1 VALUES ('�VIDER','G','retirer une partie de l''organisme en la s�parant de celui-ci par clivage, creusement, forage ou grattage');
INSERT INTO ACTION1 VALUES ('EXTRAIRE','G','retirer un �l�ment de l''organisme en le s�parant de celui-ci par une traction plus ou moins importante exerc�e sur lui');
INSERT INTO ACTION1 VALUES ('PR�LEVER','H','prendre une partie d''un �l�ment biologique (solide ou liquide) ou d''une structure anatomique, pour examen ou analyse');
INSERT INTO ACTION1 VALUES ('DRAINER','J','diriger l''�vacuation d''un fluide hors de son lieu d''origine dans l''organisme, par l''interm�diaire d''un dispositif adapt� (drain, m�che, lame...) laiss� en place');
INSERT INTO ACTION1 VALUES ('�VACUER','J','faire sortir un fluide de l''organisme sans laisser en place de dispositif particulier');
INSERT INTO ACTION1 VALUES ('NETTOYER','J','d�barrasser une partie de l''organisme d''�l�ments ind�sirables ou nocifs');
INSERT INTO ACTION1 VALUES ('CHANGER','K','enlever un dispositif de l''organisme et remettre simultan�ment en place un dispositif identique ou analogue dans le m�me site');
INSERT INTO ACTION1 VALUES ('REMPLACER','K','retirer une partie alt�r�e de l''organisme et y substituer simultan�ment un dispositif ou une structure anatomiquement identique et apte � remplir sa fonction');
INSERT INTO ACTION1 VALUES ('APPLIQUER','L','disposer un agent th�rapeutique � vis�e locale ou g�n�rale � la surface de l''organisme ou d''une de ses parties, sans effraction des t�guments');
INSERT INTO ACTION1 VALUES ('IMPLANTER','L','introduire un dispositif au sein d''une partie de l''organisme sans ex�r�se simultan�e,  pour renforcer ou prot�ger une structure, surveiller, assister ou relayer une fonction, ou prendre la place d''une structure d�ficiente ou absente');
INSERT INTO ACTION1 VALUES ('INJECTER','L','introduire un agent biologique ou pharmacologique dans l''organisme, par un orifice naturel, un dispositif implant� ou une ponction');
INSERT INTO ACTION1 VALUES ('FABRIQUER','M','concevoir et r�aliser un �l�ment, une structure, une disposition ou un appareillage');
INSERT INTO ACTION1 VALUES ('PR�PARER','M','rendre possible la r�alisation d''un acte par un travail pr�alable');
INSERT INTO ACTION1 VALUES ('R�GLER','M','mettre au point ou v�rifier le fonctionnement d''un m�canisme ou d''un appareillage');
INSERT INTO ACTION1 VALUES ('R�PARER','M','remettre une structure anatomique alt�r�e dans un �tat normal ou proche de son �tat normal sans la remplacer, de fa�on � lui permettre de remplir son r�le physiologique ou de lui redonner un aspect proche de la normale. Se substituer � une fonction physio');
INSERT INTO ACTION1 VALUES ('R�VISER','M','retourner sur un site pour apporter une correction au r�sultat pr�c�dent ou en v�rifier le r�sultat');
INSERT INTO ACTION1 VALUES ('D�TRUIRE','N','alt�rer la structure d''�l�ments physiologiques ou pathologiques de l''organisme au moyen d''agents m�caniques, physiques ou chimiques, de fa�on � en provoquer la disparition');
INSERT INTO ACTION1 VALUES ('FRAGMENTER','N','diviser en petites parties un �l�ment solide contenu dans l''organisme');
INSERT INTO ACTION1 VALUES ('COUPER','P','sectionner un �l�ment anatomique sans l''enlever');
INSERT INTO ACTION1 VALUES ('LIB�RER','P','d�gager un �l�ment anatomique comprim� ou g�n� dans son fonctionnement, au sein de l''organisme');
INSERT INTO ACTION1 VALUES ('S�PARER','P','disjoindre des �l�ments anatomiques contigus. Isoler certains �l�ments contenus dans un milieu biologique � l''aide de techniques particuli�res de tri');
INSERT INTO ACTION1 VALUES ('ENREGISTRER','Q','produire et analyser un document durable reproduisant l''image du corps ou de ses organes, ou traduisant l''activit� d''un organe, � l''aide d''un appareillage appropri�');
INSERT INTO ACTION1 VALUES ('EXAMINER','Q','observer l''organisme ou un de ses �l�ments, directement ou � l''aide d''instruments, pour en �tudier ou en suivre le fonctionnement, sans produire d''enregistrement durable');
INSERT INTO ACTION1 VALUES ('GUIDER','Q','aider � atteindre un �l�ment profond de l''organisme dont l''abord aveugle � travers les t�guments serait trop difficile ou trop dangereux, en orientant la trajectoire d''un instrument');
INSERT INTO ACTION1 VALUES ('MESURER','Q','d�terminer la qualit� ou la quantit� de certains �l�ments biologiques au moyen d''une instrumentation ou d''une exp�rience adapt�e');
INSERT INTO ACTION1 VALUES ('�DUQUER','R','mettre en �uvre des moyens propres � am�liorer la formation et les connaissances d''un individu');
INSERT INTO ACTION1 VALUES ('PROVOQUER','R','susciter une r�action de l''organisme sous l''effet d''un facteur externe contr�l� ou d''un agent pharmacologique, de mani�re � en modifier le comportement ou �  en corriger une alt�ration');
INSERT INTO ACTION1 VALUES ('R��DUQUER','R','appliquer une m�thode non effractive destin�e � recouvrer l''usage partiel ou total d''une partie de l''organisme l�s�e');
INSERT INTO ACTION1 VALUES ('OCCLURE','S','fermer l''orifice ou la lumi�re d''une structure anatomique tubulaire sans la couper');
