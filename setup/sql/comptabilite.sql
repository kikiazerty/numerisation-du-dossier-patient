CREATE TABLE IF NOT EXISTS `actes_disponibles` (
  `id_acte_dispo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom_acte` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `desc_acte` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `type` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `montant_total` double NOT NULL,
  `montant_tiers` double NOT NULL,
  `date_effet` date DEFAULT NULL,
  PRIMARY KEY (`id_acte_dispo`)
);

INSERT INTO `actes_disponibles` (`id_acte_dispo`, `nom_acte`, `desc_acte`, `type`, `montant_total`, `montant_tiers`, `date_effet`) VALUES
(1, 'C', 'Consultation', 'Consultation', 23, 6.6, '0000-00-00'),
(2, 'MGE', 'Enfant de 2 à 6 ans', 'Majoration', 3, 1, '0000-00-00'),
(3, 'MNO', 'Enfant de moins de deux ans', 'Majoration', 5, 1.5, '0000-00-00'),
(6, 'MCG', 'Soins itératifs en consultation par un autre médecin que le médecin traitant, sous réserve d''un retour d''information.', 'Majoration', 3, 1, '0000-00-00'),
(17, 'CA', 'Consultation annuelle des ALD', 'Consultation', 26, 22, '0000-00-00'),
(18, 'Dep2', 'Dépassement de deux euros', 'Dépassement', 2, 0, '0000-00-00'),
(19, 'Dep3', 'Dépassement de trois euros', 'Dépassement', 3, 0, '0000-00-00'),
(20, 'V', 'Visite à domicile (ajouter MD si la visite est justifiée)', 'Visite', 23, 15.4, '0000-00-00'),
(21, 'MD', 'Si visite justifiée ou ECG en visite', 'Majoration', 10, 7, '0000-00-00'),
(22, 'CRD', 'Consultation dimanche et fériés de jour', 'Majoration Gardes Régulées', 26.5, 7.95, '0000-00-00'),
(23, 'CRN', 'Consultation de 20h-24h et 6h-8h (appel après 19h)\r\n', 'Majoration Gardes Régulées', 42.5, 12.75, '0000-00-00'),
(24, 'CRM', 'Consultation de 00h à 06h', 'Majoration Gardes Régulées', 51.5, 15.45, '0000-00-00'),
(25, 'VRD', 'Visite dimanche et fériés de jour', 'Majoration Gardes Régulées', 30, 9, '0000-00-00'),
(26, 'VRN', 'Visite de 20h-24h et 6h-8h (appel après 19h)', 'Majoration Gardes Régulées', 46, 13.8, '0000-00-00'),
(27, 'VRM', 'Visites de 00h à 06h', 'Majoration Gardes Régulées', 55, 16.5, '0000-00-00'),
(28, 'FPE', 'Examens obligatoires du nourrisson jusqu''au 24ème mois', 'Majoration', 5, 1.5, '0000-00-00'),
(29, 'ID', 'Actes techniques à domicile (non utilisable avec le V seul)', 'Visite', 3.5, 1.05, '0000-00-00'),
(32, 'Dep1', '', 'Dépassement', 1, 0, '0000-00-00'),
(33, 'Rétrocession d''honoraires', 'Enregistrez ici les rétrocessions d''honoraires pour intégration à la comptabilité', 'Rétrocession', 0, 0, '0000-00-00'),
(34, 'MCG', 'Majoration de coordination', 'Majoration', 3, 0.9, '0000-00-00'),
(35, 'MDN', 'majoration de déplacement pour visite à domicile de nuit justifiée de 20h00 à 00h00 et de 6h00 à 8h00 ', 'Majoration', 38.5, 11.55, '0000-00-00'),
(36, 'MDI', 'majoration de déplacement pour visite à domicile de nuit justifiée de 00h00 à 6h00', 'Majoration', 43.5, 13.05, '0000-00-00'),
(37, 'MDD', 'majoration de déplacement pour visite à domicile justifiée de dimanche et jour férié', 'Majoration', 22.6, 6.78, '0000-00-00'),
(38, 'N', 'majoration de nuit pour visite à domicile non justifiée de 20h00 à 00h00 et de 6h00 à 8h00', 'Majoration', 35, 0, '0000-00-00'),
(39, 'MM', 'majoration de nuit pour visite à domicile non justifiée de 00h00 à 6h00', 'Majoration', 40, 0, '0000-00-00'),
(40, 'F', 'majoration de dimanche et jour férié pour visite à domicile non justifiée', 'Majoration', 19.06, 0, '0000-00-00'),
(41, 'MU', 'majoration d''urgence', 'Majoration', 22.6, 6.78, '0000-00-00'),
(42, 'K', 'acte technique', 'acte technique', 1.92, 0.58, '0000-00-00'),
(43, 'DEQP003', 'ECG', '', 13.52, 0, '0000-00-00');

CREATE TABLE IF NOT EXISTS `comptes_bancaires` (
  `id_compte` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usr` int(10) unsigned NOT NULL,
  `libelle` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `titulaire` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `nom_banque` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `rib_code_banque` varchar(5) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `rib_code_guichet` varchar(5) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `rib_numcompte` varchar(11) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `rib_cle` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `solde_initial` double DEFAULT '0',
  `remarque` blob,
  PRIMARY KEY (`id_compte`)
);

CREATE TABLE IF NOT EXISTS `depots` (
  `id_depot` int(11) NOT NULL AUTO_INCREMENT,
  `id_usr` int(11) NOT NULL,
  `id_compte` int(10) unsigned NOT NULL,
  `type_depot` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `date` date NOT NULL,
  `periode_deb` date NOT NULL,
  `periode_fin` date NOT NULL,
  `blob_depot` blob NOT NULL,
  `remarque` blob,
  `valide` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_depot`)
);

CREATE TABLE `honoraires` (
  `id_hono` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_usr` int(11) NOT NULL,
  `id_drtux_usr` int(11) DEFAULT NULL,
  `patient` varchar(75) COLLATE utf8_bin NOT NULL,
  `id_site` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_payeur` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `GUID` varchar(36) COLLATE utf8_bin DEFAULT NULL,
  `praticien` varchar(75) COLLATE utf8_bin NOT NULL,
  `date` date NOT NULL,
  `acte` blob NOT NULL,
  `remarque` blob,
  `esp` double NOT NULL,
  `chq` double NOT NULL,
  `cb` double NOT NULL,
  `daf` double NOT NULL,
  `autre` double NOT NULL,
  `du` double NOT NULL,
  `du_par` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `valide` tinyint(1) DEFAULT '0',
  `tracabilite` blob,
  PRIMARY KEY (`id_hono`)
);

CREATE TABLE IF NOT EXISTS `immobilisations` (
  `id_immob` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usr` int(10) unsigned NOT NULL,
  `id_compte` int(10) unsigned NOT NULL,
  `libelle` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `date_service` date NOT NULL,
  `duree` int(11) NOT NULL,
  `mode` tinyint(1) NOT NULL,
  `valeur` bigint(20) unsigned NOT NULL COMMENT 'la valeur de l''immobilisation s''entend hors taxe',
  `montant_tva` double DEFAULT NULL,
  `valeur_residuelle` bigint(20) NOT NULL,
  `resultat` blob NOT NULL,
  `mouvements` blob,
  `remarque` blob,
  `tracabilite` blob,
  PRIMARY KEY (`id_immob`)
);

CREATE TABLE IF NOT EXISTS `mouvements` (
  `id_mouvement` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_mvt_dispo` int(11) NOT NULL,
  `id_usr` int(10) unsigned NOT NULL,
  `id_compte` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'O = recettes ; 1 = dépenses',
  `libelle` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `date` date NOT NULL,
  `date_valeur` date NOT NULL,
  `montant` double unsigned NOT NULL DEFAULT '0',
  `remarque` blob,
  `valide` tinyint(4) NOT NULL DEFAULT '0',
  `tracabilite` blob,
  `validation` tinyint(1) DEFAULT NULL,
  `detail` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_mouvement`),
  KEY `date_valeur` (`date_valeur`)
);

CREATE TABLE IF NOT EXISTS `mouvements_disponibles` (
  `id_mvt_dispo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_mvt_parent` int(11) DEFAULT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `libelle` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `remarque` blob,
  PRIMARY KEY (`id_mvt_dispo`)
);

INSERT INTO `mouvements_disponibles` (`id_mvt_dispo`, `id_mvt_parent`, `type`, `libelle`, `remarque`) VALUES
(1, 0, 0, 'Apports praticien', 0x7c6c6e3d4146),
(2, 0, 0, 'Autres recettes', 0x7c6c6e3d4146),
(3, 0, 0, 'Produits financiers', 0x7c6c6e3d4145),
(4, 0, 1, 'TVA récupérable', 0x4e554c4c),
(5, 0, 1, 'Prélèvements du praticien', 0x4e554c4c),
(6, 0, 1, 'Achats : fournitures et pharmacie', 0x4e554c4c),
(7, 0, 1, 'Frais de personnel', 0x4e554c4c),
(8, 6, 1, 'Salaires nets', 0x7c6c6e3d4242),
(9, 6, 1, 'Charges sociales', 0x7c6c6e3d4243),
(10, 0, 1, 'Impôts et Taxes', 0x4e554c4c),
(11, 10, 1, 'TVA Payée', 0x4e554c4c),
(12, 10, 1, 'Taxe Professionnelle', 0x7c6c6e3d4245),
(13, 10, 1, 'Autres impôts', 0x7c6c6e3d4253),
(14, 0, 1, 'Travaux, Fournitures, Services Extérieurs', 0x4e554c4c),
(15, 14, 1, 'Loyers et charges', 0x7c6c6e3d4246),
(16, 14, 1, 'Location matériel', 0x7c6c6e3d4247),
(17, 14, 1, 'Réparation et entretien', 0x7c6c6e3d4248),
(18, 14, 1, 'Personnel intérim, secrétariat téléphonique', 0x7c6c6e3d4248),
(19, 14, 1, 'Petit outillage', 0x7c6c6e3d4248),
(20, 14, 1, 'Gaz, électricité, chauffage, eau', 0x7c6c6e3d4248),
(21, 14, 1, 'Honoraires rétrocédés', 0x4e554c4c),
(22, 14, 1, 'Honoraires ne constituant pas de rétrocession', 0x7c6c6e3d4248),
(23, 14, 1, 'Assurances', 0x7c6c6e3d4248),
(24, 0, 1, 'Transports et déplacements', 0x4e554c4c),
(25, 24, 1, 'Frais de voiture', 0x7c6c6e3d424a),
(26, 24, 1, 'Frais moto', 0x7c6c6e3d424a),
(27, 24, 1, 'Autres frais de déplacement', 0x7c6c6e3d424a),
(28, 0, 1, 'Charges sociales du praticien', 0x4e554c4c),
(29, 28, 1, 'Vieillesse, Assurance Maladie, Alloc. Fam.', 0x7c6c6e3d424d),
(30, 0, 1, 'Frais divers de gestion', 0x7c6c6e3d424d),
(31, 30, 1, 'Congrès', 0x4e554c4c),
(32, 30, 1, 'Cadeaux, représentation et réception', 0x4e554c4c),
(33, 30, 1, 'Frais de bureau, documentation, et P et T', 0x4e554c4c),
(34, 30, 1, 'Cotisation professionnelle et syndicales', 0x7c6c6e3d424d),
(35, 30, 1, 'Divers', 0x7c6c6e3d424d),
(36, 0, 1, 'Frais financiers', 0x7c6c6e3d424e),
(37, 0, 1, 'Pertes', ''),
(38, 0, 1, 'Débours payés pour le compte des clients', 0x4e554c4c),
(39, 0, 1, 'Autres dépenses', 0x4e554c4c),
(40, 39, 1, 'SCM ou partage de frais', 0x4e554c4c),
(41, 39, 1, 'Immobilisations', 0x4e554c4c),
(42, 39, 1, 'Divers à réintégrer', 0x7c6c6e3d4343),
(43, 0, 1, 'Comptes d''attente', '');

CREATE TABLE IF NOT EXISTS `paiements` (
  `id_paiement` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_hono` int(11) unsigned NOT NULL,
  `date` date NOT NULL,
  `id_payeurs` bigint(20) NOT NULL,
  `acte` blob,
  `esp` double NOT NULL,
  `chq` double NOT NULL,
  `cb` double NOT NULL,
  `daf` double NOT NULL,
  `autre` double NOT NULL,
  `du` double NOT NULL,
  `valide` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_paiement`)
);

CREATE TABLE IF NOT EXISTS `payeurs` (
  `ID_Primkey` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_payeurs` bigint(20) NOT NULL,
  `nom_payeur` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresse_payeur` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ville_payeur` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `code_postal` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `telephone_payeur` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID_Primkey`)
);

CREATE TABLE IF NOT EXISTS `pourcentages` (
  `id_pourcent` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `pourcentage` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_pourcent`)
);

INSERT INTO `pourcentages` (`id_pourcent`, `type`, `pourcentage`) VALUES
(1, 'AMO_Alsace', '90'),
(2, 'AMO', '70'),
(3, 'AMC', '30'),
(4, 'AMC_Alsace', '10');

CREATE TABLE IF NOT EXISTS `seances` (
  `id_paiement` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_hono` int(11) unsigned NOT NULL,
  `date` date NOT NULL,
  `id_payeurs` bigint(20) NOT NULL,
  `acte` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `esp` double NOT NULL,
  `chq` double NOT NULL,
  `cb` double NOT NULL,
  `daf` double NOT NULL,
  `autre` double NOT NULL,
  `du` double NOT NULL,
  `valide` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_paiement`)
);

CREATE TABLE IF NOT EXISTS `sites` (
  `ID_Primkey` bigint(10) NOT NULL AUTO_INCREMENT,
  `id_site` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `site` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresse_site` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `cp_site` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ville_site` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `telsite` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `faxsite` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mailsite` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID_Primkey`)
);

CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id_usr` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom_usr` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `login` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `id_drtux_usr` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_usr`)
);

CREATE TABLE IF NOT EXISTS `z_version` (
  `infos_version` blob
);

INSERT INTO `z_version` (`infos_version`) VALUES
(0x0d0a5b56657273696f6e5d0d0a20206e6f6d202020203d20436f6d70746162696c6974c3a920706f7572204d6564696e5475780d0a20206e756d202020203d20312e31310d0a2020737562762020203d20737461626c652072656c656173650d0a2020646174652020203d203238204a75696e20323030380d0a20206372656174696f6e203d2053657074656d62726520323030370d0a2020617574657572203d2044722045726963204d41454b45520d0a20206c69656e3120203d20687474703a2f2f7777772e657269636d61656b65722e66722f0d0a20206c69656e3220203d20687474703a2f2f7777772e6d6564696e7475782e6f72672f0d0a2020696e666f3120203d2043657474652076657273696f6e207065757820636f6e74656e69722064657320657272657572732064652070726f6772616d6d6174696f6e206f752064652063616c63756c2e0d0a2020696e666f3220203d204c27617574657572206e65207065757420c3aa7472652074656e7520726573706f6e7361626c652064657320657272657572732065742f6f7520706572746573206c69c3a9657320c3a0206c277574696c69736174696f6e206465206365206c6f67696369656c2e0d0a2020696e666f3320203d204d6572636920646520666169726520766f7320726170706f727473206465206275677320646570756973203a200d0a2020696e666f3420203d206c65207369746520696e7465726e6574206465206c276175746575722028687474703a2f2f7777772e657269636d61656b65722e66722f290d0a2020696e666f3520203d206f7520737572206c6520736974652064274164756c6c616374202868747470733a2f2f6164756c6c6163742e6e65742f70726f6a656374732f6d6564696e7475782f292e0d0a20206c6963656e636520203d20436543494c4c2076657273696f6e20320d0a20206d6564696e747578203d2076322e303020657420737570c3a97269657572650d0a20206c616e6761676520203d20432b2b2f5174332f4d7953514c0d0a0d0a5b52656d65726369656d656e74735d0d0a20206c31203d20c38020526f6c616e6420536576696e20706f757220736f6e2061696465207072c3a96369657573652064616e73206c612070726f6772616d6d6174696f6e206574206c652064c3a962756767616765206465206c276170706c69636174696f6e2e0d0a20206c32203d20c3802047c3a9726172642044656c61666f6e6420706f757220736f6e20616964652061752064c3a9627567676167652c2073657320636f7272656374696f6e7320657420736573206170706f7274732e0d0a20206c33203d20c3802042656e6a616d696e20506f7175657420706f75722073657320696e666f726d6174696f6e7320636f6e6365726e616e74206c657320667569746573206dc3a96d6f697265732065742067657374696f6e20646520706f696e746575727320656e20432b2b2e0d0a0d0a);

