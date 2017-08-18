<?php
/*
Page de resolution des doublons
*/
if (session_status() == PHP_SESSION_NONE) 
{
  session_start() ;

  include("config.php");
  if ( !isset( $_SESSION['login'] ) )
  {
    header('location: index.php?page=liste' );
    exit;
  }
  $tab_login=explode("::",$_SESSION['login']);
  $user=$tab_login[0];
  $sign=$tab_login[1];

  //connexion a drtux
  try {
      $strConnection = 'mysql:host='.$host.';dbname='.$base; 
      $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); 
      $pdo = new PDO($strConnection, $loginbase, $pwd, $arrExtraParam); // Instancie la connexion
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  catch(PDOException $e) {
      $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
      die($msg);
  }
}

function detectUTF8($string) //pour les notes dont l'encodage est toujours imprévisible
{
  return preg_match('%(?:
  [\\xC2-\\xDF][\\x80-\\xBF] # non-overlong 2-byte
  |\\xE0[\\xA0-\\xBF][\\x80-\\xBF] # excluding overlongs
  |[\\xE1-\\xEC\\xEE\\xEF][\\x80-\\xBF]{2} # straight 3-byte
  |\\xED[\\x80-\\x9F][\\x80-\\xBF] # excluding surrogates
  |\\xF0[\\x90-\\xBF][\\x80-\\xBF]{2} # planes 1-3
  |[\\xF1-\\xF3][\\x80-\\xBF]{3} # planes 4-15
  |\\xF4[\\x80-\\x8F][\\x80-\\xBF]{2} # plane 16
  )+%xs', $string);
}

/*
$db=mysqli_connect($host, $loginbase, $pwd);
$codage=mysqli_query($db,"SET NAMES 'UTF8'");

if(!$db)
{
	print "Erreur de connexion &agrave; $host<br />";
	exit;
}

// on choisit la bonne base

if (!mysqli_select_db($db,$base))
{
	print "Erreur ".mysqli_error($db)."<br />";
	mysqli_close($db);
	exit;
}
*/
//Recherche si l'utilisateur a des droits admin
/* modele
$sql_droit_admin=$pdo->prepare('SELECT Droits FROM Personnes WHERE Login= ?');
$sql_droit_admin->bindValue(1, $user, PDO::PARAM_STR);
$sql_droit_admin->execute();
$ligne_droit_admin=$sql_droit_admin->fetch(PDO::FETCH_ASSOC);
$sql_droit_admin->closeCursor();
*/
$sql_droit_admin=$pdo->prepare('SELECT Droits FROM Personnes WHERE Login= ?');
$sql_droit_admin->bindValue(1, $user, PDO::PARAM_STR);
$sql_droit_admin->execute();
$ligne_droit_admin=$sql_droit_admin->fetch(PDO::FETCH_ASSOC);

//$resultat_droit_admin=mysqli_query($db,$sql_droit_admin);
//$ligne_droit_admin=mysqli_fetch_array($resultat_droit_admin);
if (!preg_match("`adr`",$ligne_droit_admin["Droits"])) //si l'utilisateur n'a pas les droits directs de resolution de doublons, on verifie les droits delegues
{
  $user_GUID=$ligne_droit_admin['GUID'];
  $sql_sign=$pdo->prepare("SELECT Droits,GUID FROM Personnes WHERE Login=?");
  $sql_sign->bindValue(1, $sign, PDO::PARAM_STR);
  $sql_sign->execute();    
  $ligne_sign=$sql_sign->fetch(PDO::FETCH_ASSOC);
//  $sql_sign="SELECT Droits,GUID FROM Personnes WHERE Login='$sign'";
//  $resultat_sign=mysqli_query($db,$sql_sign);
//  $ligne_sign=mysqli_fetch_array($resultat_sign);
  $sign_GUID=$ligne_sign['GUID'];
  $sign_droits=$ligne_sign['Droits'];
  $sql_sign->closeCursor();
  //On cherche s'il a des droits delegues par n'importe quel utilisateur signataire
  if (preg_match("`adr`",$sign_droits)) //on verifie d'abord que le signataire a les droits
  {
  //On cherche si le delegue a les droits
    $sql_droits_delegues=$pdo->prepare("SELECT FriendUserDroits FROM user_perms WHERE SignataireGUID=? AND FriendUserGUID=?");
    $sql_droits_delegues->bindValue(1, $sign_GUID, PDO::PARAM_STR);
    $sql_droits_delegues->bindValue(2, $user_GUID, PDO::PARAM_STR);
    $sql_droits_delegues->execute();
    $ligne_droits_delegues=$sql_droits_delegues->fetch(PDO::FETCH_ASSOC);
 //   $sql_droits_delegues="SELECT FriendUserDroits FROM user_perms WHERE SignataireGUID='$sign_GUID' AND FriendUserGUID='$user_GUID'";
//    $resultat_droits_delegues=mysqli_query($db,$sql_droits_delegues);
//    $ligne_droits_delegues=mysqli_fetch_array($resultat_droits_delegues);
    if (!preg_match("`adr`",$ligne_droits_delegues['Droits'])) //Si le delegue n'a pas le droit adm, on sort
    {
      header ('location: liste.php' );
      exit;
    }
  }
  else //si le signataire n'a pas les droits, on sort
  {
    header ('location: liste.php' );
    exit;
  }
}

if (isset($_REQUEST['selection_patient']))
  $selection_patient=$_REQUEST['selection_patient'];
else
  $selection_patient=$FchGnrl_IDDos;
/* Les différents champs à fusionner
FchGnrl_NomDos
FchGnrl_Prenom
FchPat_NomFille
FchPat_Adresse
FchPat_Ville
FchPat_CP
FchPat_NumSS
FchPat_Nee
FchPat_Profession
FchPat_Tel1
*/
/*
if (isset($_REQUEST['contents']))
{
  echo "la fiche que vous voulez importer existe déjà";
  }
*/
if (isset($_REQUEST['button_valid_merge'])) //si on recoit le bouton de fusion
{
//on recupere les champs a fusionner et on en deduit les valeurs a mettre a jour
//seul le nom est un GUID, les autres sont les chaines a inserer
  $GUID=$_REQUEST['Nom'];
//on cherche l'id_primkey pour mettre a jour dans les intervenants et les notes
  $sql_chercher_ID=$pdo->prepare("SELECT ID_PrimKey FROM IndexNomPrenom WHERE FchGnrl_IDDos=?");
  $sql_chercher_ID->bindValue(1, $GUID, PDO::PARAM_STR);
  $sql_chercher_ID->execute();
  $sql_chercher_ID->closeCursor();
  $ligne_chercher_ID=$sql_chercher_ID->fetch(PDO::FETCH_ASSOC);
//  $sql_chercher_ID="SELECT ID_PrimKey FROM IndexNomPrenom WHERE FchGnrl_IDDos='$GUID'";
//  $resultat_chercher_ID=mysqli_query($db,$sql_chercher_ID);
//  $ligne_chercher_ID=mysqli_fetch_array($resultat_chercher_ID);
  $ID_PrimKey=$ligne_chercher_ID['ID_PrimKey'];

  $Prenom=$_REQUEST['Prenom'];
  $jeunefille=$_REQUEST['jeunefille'];
  $nee=$_REQUEST['nee'];
  $sexe=$_REQUEST['sexe'];
  $adresse=$_REQUEST['adresse'];
  $CP=$_REQUEST['CP'];
  $ville=$_REQUEST['ville'];
  $tel1=$_REQUEST['tel1'];
  $tel2=$_REQUEST['tel2'];
  $tel3=$_REQUEST['tel3'];
  $email=$_REQUEST['email'];
  $NumSS=$_REQUEST['NumSS'];
  $PatientAss=$_REQUEST['PatientAss'];
  $PrenomAss=$_REQUEST['PrenomAss'];
  $NomAss=$_REQUEST['NomAss'];
  $Profession=$_REQUEST['Profession'];
  $Titre=$_REQUEST['Titre'];
  $Notes=$_REQUEST['Notes'];
//les requetes
//on met a jour la fiche d'index
  $sql_merge_index_nom_prenom=$pdo->prepare("UPDATE IndexNomPrenom SET FchGnrl_Prenom=? WHERE FchGnrl_IDDos=?");
  $sql_merge_index_nom_prenom->bindValue(1,$Prenom , PDO::PARAM_STR);
  $sql_merge_index_nom_prenom->bindValue(2,$GUID , PDO::PARAM_STR);
  $sql_merge_index_nom_prenom->execute();
  $sql_merge_index_nom_prenom->closeCursor();
//  $sql_merge_index_nom_prenom=sprintf("UPDATE IndexNomPrenom SET FchGnrl_Prenom='%s' WHERE FchGnrl_IDDos='$GUID'",mysqli_real_escape_string($db,$Prenom));
//  $resultat_index_nom_prenom=mysqli_query($db,$sql_merge_index_nom_prenom);
//on met a jour fchpat
  $sql_merge_fchpat=$pdo->prepare("UPDATE fchpat SET FchPat_NomFille=?,FchPat_Nee=?,FchPat_Sexe=?,FchPat_Adresse=?,FchPat_CP=?,FchPat_Ville=?,FchPat_Tel1=?,FchPat_Tel2=?,FchPat_Tel3=?,FchPat_Email=?,FchPat_NumSS=?,FchPat_PatientAss=?,FchPat_NomAss=?,FchPat_PrenomAss=?,FchPat_Profession=?,FchPat_Titre=? WHERE FchPat_GUID_Doss=?");
  $sql_merge_fchpat->bindValue(1, $jeunefille , PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(2, $nee , PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(3, $sexe , PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(4, $adresse , PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(5, $CP , PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(6, $ville, PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(7, $tel1, PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(8, $tel3, PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(9, $tel3, PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(10, $email, PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(11, $NumSS, PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(12, $PatientAss, PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(13, $NomAss, PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(14, $PrenomAss, PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(15, $Profession, PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(16, $Titre, PDO::PARAM_STR);
  $sql_merge_fchpat->bindValue(17, $GUID, PDO::PARAM_STR);
  $sql_merge_fchpat->execute();
  $sql_merge_fchpat->closeCursor();
//  $sql_merge_fchpat=sprintf("UPDATE fchpat SET FchPat_NomFille='%s',FchPat_Nee='%s',FchPat_Sexe='%s',FchPat_Adresse='%s',FchPat_CP='%s',FchPat_Ville='%s',FchPat_Tel1='%s',FchPat_Tel2='%s',FchPat_Tel3='%s',FchPat_Email='%s',FchPat_NumSS='%s',FchPat_PatientAss='%s',FchPat_NomAss='%s',FchPat_PrenomAss='%s',FchPat_Profession='%s',FchPat_Titre='%s' WHERE FchPat_GUID_Doss='$GUID'",mysqli_real_escape_string($db,$jeunefille),mysqli_real_escape_string($db,$nee),mysqli_real_escape_string($db,$sexe),mysqli_real_escape_string($db,$adresse),mysqli_real_escape_string($db,$CP),mysqli_real_escape_string($db,$ville),mysqli_real_escape_string($db,$tel1),mysqli_real_escape_string($db,$tel2),mysqli_real_escape_string($db,$tel3),mysqli_real_escape_string($db,$email),mysqli_real_escape_string($db,$NumSS),mysqli_real_escape_string($db,$PatientAss),mysqli_real_escape_string($db,$NomAss),mysqli_real_escape_string($db,$PrenomAss),mysqli_real_escape_string($db,$Profession),mysqli_real_escape_string($db,$Titre));
 // $resultat_merge_fchpat=mysqli_query($db,$sql_merge_fchpat);
 
  $sql_chercher_intervenants_de_ce_patient=$pdo->prepare("SELECT * FROM fchpat_Intervenants WHERE fchpat_Intervenants_PatGUID=?");
  $sql_supprimer_doublon_intervenant=$pdo->prepare("DELETE FROM fchpat_Intervenants WHERE fchpat_Intervenants_IntervPK=? AND fchpat_Intervenants_PatGUID=?");
  $sql_merge_intervenant=$pdo->prepare("UPDATE fchpat_Intervenants SET fchpat_Intervenants_PatGUID=?,fchpat_Intervenants_PatPK=? WHERE  fchpat_Intervenants_PatGUID=?");
  $sql_rename_document=$pdo->prepare("UPDATE RubriquesBlobs SET RbDate_IDDos=? WHERE RbDate_IDDos=?");
  $sql_rename_title=$pdo->prepare("UPDATE RubriquesHead SET RbDate_IDDos=? WHERE  RbDate_IDDos=?");
  $sql_merge_notes=$pdo->prepare("UPDATE fchpat_Note SET fchpat_Note_PatGUID=?, fchpat_Note_PatPK=? WHERE fchpat_Note_PatGUID=?");
  $sql_merge_agenda=$pdo->prepare("UPDATE agenda SET GUID=? WHERE GUID=?");
  $sql_delete_fchpat=$pdo->prepare("DELETE FROM fchpat WHERE FchPat_GUID_Doss=?");
  $sql_delete_patient=$pdo->prepare("DELETE FROM IndexNomPrenom WHERE FchGnrl_IDDos=?");

//on remplace tous les GUID des patients a supprimer dans les intervenants par celui a garder
  foreach ($selection_patient AS $ce_patient) //on examine tous les GUID transmis par les hidden
  {
    if ($ce_patient != $GUID) //on supprime les fiches qui ne sont pas celle de GUID et on met a jour intervenants et notes
    {
//on supprime les liaisons aux intervenants si provoquent des doublons
//      $sql_chercher_intervenants_de_ce_patient="SELECT * FROM fchpat_Intervenants WHERE fchpat_Intervenants_PatGUID='$ce_patient' ";
//      $resultat_chercher_intervenants_de_ce_patient=mysqli_query($db,$sql_chercher_intervenants_de_ce_patient);
      $sql_chercher_intervenants_de_ce_patient->bindValue(1,$ce_patient , PDO::PARAM_STR);
      $sql_chercher_intervenants_de_ce_patient->execute();
      $ligne_chercher_intervenants_de_ce_patient_all=$sql_chercher_intervenants_de_ce_patient->fetchAll(PDO::FETCH_ASSOC);
      $sql_chercher_intervenants_de_ce_patient->closeCursor();
      
      foreach ($ligne_chercher_intervenants_de_ce_patient_all AS $ligne_chercher_intervenants_de_ce_patient)
//      while ($ligne_chercher_intervenants_de_ce_patient=mysqli_fetch_array($resultat_chercher_intervenants_de_ce_patient))
      {
	$sql_supprimer_doublon_intervenant->bindValue(1,$ligne_chercher_intervenants_de_ce_patient['fchpat_Intervenants_IntervPK'] , PDO::PARAM_STR);
	$sql_supprimer_doublon_intervenant->bindValue(2,$GUID , PDO::PARAM_STR);
	$sql_supprimer_doublon_intervenant->execute();
	$sql_supprimer_doublon_intervenant->closeCursor();
//	$sql_supprimer_doublon_intervenant="DELETE FROM fchpat_Intervenants WHERE fchpat_Intervenants_IntervPK='$doublon_a_chercher' AND fchpat_Intervenants_PatGUID='$GUID'";
//	$resultat_supprimer_doublon_intervenant=mysqli_query($db,$sql_supprimer_doublon_intervenant);
      }
//on fusionne les intervenants
//    $sql_merge_intervenant="UPDATE fchpat_Intervenants SET fchpat_Intervenants_PatGUID='$GUID',fchpat_Intervenants_PatPK='$ID_PrimKey' WHERE  fchpat_Intervenants_PatGUID='$ce_patient'";
//    $resultat_merge_intervenant=mysqli_query($db,$sql_merge_intervenant);
      $sql_merge_intervenant->bindValue(1,$GUID , PDO::PARAM_STR);
      $sql_merge_intervenant->bindValue(2,$ID_PrimKey , PDO::PARAM_STR);
      $sql_merge_intervenant->bindValue(3,$ce_patient , PDO::PARAM_STR);
      $sql_merge_intervenant->execute();
      $sql_merge_intervenant->closeCursor();
      
  //    $sql_rename_document="UPDATE RubriquesBlobs SET RbDate_IDDos='$GUID' WHERE RbDate_IDDos='$ce_patient'";
      // $resultat_rename_document=mysqli_query($db,$sql_rename_document);
      $sql_rename_document->bindValue(1,$GUID , PDO::PARAM_STR);
      $sql_rename_document->bindValue(2,$ce_patient , PDO::PARAM_STR);
      $sql_rename_document->execute();
      $sql_rename_document->closeCursor();
  //    $sql_rename_title="UPDATE RubriquesHead SET RbDate_IDDos='$GUID' WHERE  RbDate_IDDos='$ce_patient'";
  //    $resultat_rename_title=mysqli_query($db,$sql_rename_title);
      $sql_rename_title->bindValue(1,$GUID , PDO::PARAM_STR);
      $sql_rename_title->bindValue(2,$ce_patient , PDO::PARAM_STR);
      $sql_rename_title->execute();
      $sql_rename_title->closeCursor();
  //    $sql_merge_notes="UPDATE fchpat_Note SET fchpat_Note_PatGUID='$GUID', fchpat_Note_PatPK='$ID_PrimKey' WHERE fchpat_Note_PatGUID='$ce_patient'";
  //    $resultat_merge_notes=mysqli_query($db,$sql_merge_notes);
      $sql_merge_notes->bindValue(1,$GUID , PDO::PARAM_STR);
      $sql_merge_notes->bindValue(2,$ID_PrimKey , PDO::PARAM_STR);
      $sql_merge_notes->bindValue(3,$ce_patient , PDO::PARAM_STR);
      $sql_merge_notes->execute();
      $sql_merge_notes->closeCursor();
      
  //    $sql_merge_agenda="UPDATE agenda SET GUID='$GUID' WHERE fchpat_Note_PatGUID='$ce_patient'";
  //    $resultat_merge_agenda=mysqli_query($db,$sql_merge_agenda);
      $sql_merge_agenda->bindValue(1,$GUID , PDO::PARAM_STR);
      $sql_merge_agenda->bindValue(2,$ce_patient , PDO::PARAM_STR);
      $sql_merge_agenda->execute();
      $sql_merge_agenda->closeCursor();
      
  //    $sql_delete_fchpat="DELETE FROM fchpat WHERE FchPat_GUID_Doss='$ce_patient'";
  //    $resultat_delete_fchpat=mysqli_query($db,$sql_delete_fchpat);
      $sql_delete_fchpat->bindValue(1,$ce_patient , PDO::PARAM_STR);
      $sql_delete_fchpat->execute();
      $sql_delete_fchpat->closeCursor();
  //    $sql_delete_patient="DELETE FROM IndexNomPrenom WHERE FchGnrl_IDDos='$ce_patient'";
  //    $resultat_delete_patient=mysqli_query$db,$sql_delete_patient);
      $sql_delete_patient->bindValue(1,$ce_patient , PDO::PARAM_STR);
      $sql_delete_patient->execute();
      $sql_delete_patient->closeCursor();
    }
  }
//on arrive a la page du patient de destination apres fusion
header ('location: frame_patient.php?GUID='.$GUID.'&affichage=listes');
}
	include("inc/header.php");
?>
    <title>
      Fusion de dossiers
    </title>
    <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen" />
  </head>
	
  <body style="font-size:<?php echo $fontsize; ?>pt" >
    <div class="conteneur">
<?php	
// on du menu d'en-tete	
$anchor='Fusion_de_fiches';
include("inc/menu-horiz.php");		
?>
   <div class="groupe">
      <h1>
	MedWebTux - Fusion de dossiers
      </h1>
	<p class="information">
	  Cette page vous permet de fusionner plusieurs dossiers.<br />
	  En effet, il peut arriver qu'un même patient possède plusieurs dossiers, souvent à la suite d'une erreur de saisie.<br />
	  Grâce à cette interface, vous pouvez choisir quels éléments vous gardez de chaque dossier (un dossier peut être exact en ce qui concerne, le nom, tandis qu'un autre peut avoir l'adresse exacte).<br />
	  Choisissez au moyen des boutons radio les éléments que vous voulez conserver, puis validez.<br />
	  Les éléments qui n'entrent pas en conflit (observations, ordonnances, rendez-vous, etc.) s'ajouteront automatiquement dans l'unique dossier.
	</p><br />

	<div class="tableau">
	<form action="merge.php" method="post">
	  <table>
	    <tr>
	      <th class="fond_th">
		Nom
	      </th>
	      <th class="fond_th">
		Prénom
	      </th>
	      <th class="fond_th">
		Jeune fille
	      </th>
	      <th class="fond_th">
		Naissance
	      </th>
	      <th class="fond_th">
		Sexe
	      </th>
	      <th class="fond_th">
		Adresse
	      </th>
	      <th class="fond_th">
		Code postal
	      </th>
	      <th class="fond_th">
		Ville
	      </th>
	      <th class="fond_th">
		Tel1
	      </th>
	      <th class="fond_th">
		Tel2
	      </th>
	      <th class="fond_th">
		Tel3
	      </th>
	      <th class="fond_th">
		E-Mail
	      </th>
	      <th class="fond_th">
		Num sécu
	      </th>
	      <th class="fond_th">
		Patient ass
	      </th>
	      <th class="fond_th">
		Nom ass
	      </th>
	      <th class="fond_th">
		Prénom ass
	      </th>
	      <th class="fond_th">
		Profession
	      </th>
	      <th class="fond_th">
		Titre
	      </th>
	      <th class="fond_th">
		Notes
	      </th>
	    </tr>
<?php
foreach ($selection_patient AS $ce_patient)
{
?>
	    <tr>
	      <td class="fond_td">
		<input name="selection_patient[]" type="hidden" value="<?php echo $ce_patient ?>" />
<?php
  $resultat="";
//  $sql="SELECT * FROM IndexNomPrenom INNER JOIN fchpat ON IndexNomPrenom.FchGnrl_IDDos=fchpat.FchPat_GUID_Doss WHERE FchGnrl_IDDos = '$ce_patient'";
  $sql=$pdo->prepare("SELECT * FROM IndexNomPrenom INNER JOIN fchpat ON IndexNomPrenom.FchGnrl_IDDos=fchpat.FchPat_GUID_Doss WHERE FchGnrl_IDDos = ?");
  $sql->bindValue(1,$ce_patient , PDO::PARAM_STR);
  $sql->execute();
//  $resultat=mysqli_query($db,$sql);
//  while ($ligne=mysqli_fetch_array($resultat))
  while ($ligne=$sql->fetch(PDO::FETCH_ASSOC))
  {
?>
		<input type="radio" value="<?php echo $ce_patient ?>" name="Nom" checked="checked" />
<?php

    echo "
		<a href=\"frame_patient.php?GUID=".$ce_patient."\">".$ligne['FchGnrl_NomDos']."</a>";
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchGnrl_Prenom'] ?>" name="Prenom" checked="checked"  />
<?php
    echo '
		'.$ligne['FchGnrl_Prenom'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_NomFille'] ?>" name="jeunefille" checked="checked"  />
<?php
    echo '
		'.$ligne['FchPat_NomFille'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_Nee'] ?>" name="nee" checked="checked"  />
<?php
    echo '
		'.$ligne['FchPat_Nee'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_Sexe'] ?>" name="sexe" checked="checked"  />
<?php
    echo '
		'.$ligne['FchPat_Sexe'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_Adresse'] ?>" name="adresse" checked="checked"  />
<?php
    echo '
		'.$ligne['FchPat_Adresse'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_CP'] ?>" name="CP" checked="checked"  />
<?php
    echo '
		'.$ligne['FchPat_CP'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_Ville'] ?>" name="ville" checked="checked"  />
<?php
    echo '
		'.$ligne['FchPat_Ville'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_Tel1'] ?>" name="tel1" checked="checked"  />
<?php
    echo '
		'.$ligne['FchPat_Tel1'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_Tel2'] ?>" name="tel2" checked="checked"  />
<?php
    echo '
		'.$ligne['FchPat_Tel2'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_Tel3'] ?>" name="tel3" checked="checked"  />
<?php
    echo '
		'.$ligne['FchPat_Tel3'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_Email'] ?>" name="email" checked="checked"  />
<?php
    echo '
		'.$ligne['FchPat_Email'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_NumSS'] ?>" name="NumSS" checked="checked"  />
<?php
    echo '
		'.$ligne['FchPat_NumSS'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_PatientAs'] ?>" name="PatientAss" checked="checked"  />
<?php
    echo '
	      '.$ligne['FchPat_PatientAss'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_PrenomAss'] ?>" name="PrenomAss" checked="checked"  />
<?php
    echo '
	      '.$ligne['FchPat_PrenomAss'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_NomAss'] ?>" name="NomAss" checked="checked"  />
<?php
    echo '
	      '.$ligne['FchPat_NomAss']
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_Profession'] ?>" name="Profession" checked="checked"  />
<?php
    echo '
	      '.$ligne['FchPat_Profession'];
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $ligne['FchPat_Titre'] ?>" name="Titre" checked="checked"  />
<?php
    echo '
		'.$ligne['FchPat_Titre']."
	      </td>";
  }
    $sql->closeCursor();
//$sql_notes=sprintf("SELECT * FROM fchpat_Note WHERE fchpat_Note_PatGUID='%s'",mysqli_real_escape_string($db,$ce_patient));
$sql_notes=$pdo->prepare("SELECT * FROM fchpat_Note WHERE fchpat_Note_PatGUID=?");
$sql_notes->bindValue(1,$ce_patient , PDO::PARAM_STR);
$sql_notes->execute();
//$resultat_notes=mysqli_query($db,$sql_notes);

$ligne_notes=$sql_notes->fetch(PDO::FETCH_ASSOC);

$sql_notes->closeCursor();
?>
	      <td class="fond_td">
		<input type="radio" value="<?php if (!detectUTF8($ligne_notes['fchpat_Note_Html'])) echo $ligne_notes['fchpat_Note_Html']; else echo  utf8_encode($ligne_notes['fchpat_Note_Html']); ?>" name="Notes" checked="checked"  />
<?php
if (detectUTF8($ligne_notes['fchpat_Note_Html'])) echo $ligne_notes['fchpat_Note_Html']; else echo  utf8_encode($ligne_notes['fchpat_Note_Html']);"
	      </td>
	    </tr>";
}//fin foreach
if (!isset($_REQUEST['bouton_valider_coches'])) //on vient de la page import et non de la liste des patients
{
?>
	    <tr>
	      <td class="fond_td">
		<input name="selection_patient[]" type="hidden" value="<?php echo $FchGnrl_IDDos ?>" />
		<input type="radio" value="<?php echo $FchGnrl_IDDos ?>" name="Nom"  />
		<a href="frame_patient.php?GUID=<?php echo $FchGnrl_IDDos ?>"><?php echo $FchGnrl_NomDos ?></a>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $FchGnrl_Prenom ?>" name="Prenom"   />
<?php
    echo '
		'.$FchGnrl_Prenom;
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo preg_replace($pattern,$replacement,$contents->identite->FchPat_NomFille) ?>" name="jeunefille"   />
<?php
    echo '
		'.preg_replace($pattern,$replacement,$contents->identite->FchPat_NomFille);
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php $contents->identite->FchPat_Nee ?>" name="nee"   />
<?php
    echo '
		'.$contents->identite->FchPat_Nee;
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $contents->identite->FchPat_Sexe ?>" name="sexe"   />
<?php
    echo '
		'.$contents->identite->FchPat_Sexe;
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo preg_replace($pattern,$replacement,$contents->identite->FchPat_Adresse); ?>" name="adresse"   />
<?php
    echo '
		'.preg_replace($pattern,$replacement,$contents->identite->FchPat_Adresse);
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $contents->identite->FchPat_CP ?>" name="CP"   />
<?php
    echo '
		'.$contents->identite->FchPat_CP;
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo preg_replace($pattern,$replacement,$contents->identite->FchPat_Ville); ?>" name="ville"   />
<?php
    echo '
		'.preg_replace($pattern,$replacement,$contents->identite->FchPat_Ville);
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $contents->identite->FchPat_Tel1 ?>" name="tel1"   />
<?php
    echo '
		'.$contents->identite->FchPat_Tel1;
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $contents->identite->FchPat_Tel2 ?>" name="tel2"   />
<?php
    echo '
		'.$contents->identite->FchPat_Tel2;
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $contents->identite->FchPat_Tel3 ?>" name="tel3"   />
<?php
    echo '
		'.$contents->identite->FchPat_Tel3;
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $contents->identite->FchPat_Email ?>" name="email"   />
<?php
    echo '
		'.$contents->identite->FchPat_Email;
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $contents->identite->FchPat_NumSS ?>" name="NumSS"   />
<?php
    echo '
		'.$contents->identite->FchPat_NumSS;
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo $contents->identite->FchPat_PatientAss ?>" name="PatientAss"   />
<?php
    echo '
	      '.$contents->identite->FchPat_PatientAss;
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo preg_replace($pattern,$replacement,$contents->identite->FchPat_PrenomAss) ?>" name="PrenomAss"   />
<?php
    echo '
	      '.preg_replace($pattern,$replacement,$contents->identite->FchPat_PrenomAss);
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo preg_replace($pattern,$replacement,$contents->identite->FchPat_NomAss) ?>" name="NomAss"   />
<?php
    echo '
	      '.preg_replace($pattern,$replacement,$contents->identite->FchPat_NomAss);
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo preg_replace($pattern,$replacement,$contents->identite->FchPat_Profession) ?>" name="Profession"   />
<?php
    echo '
	      '.preg_replace($pattern,$replacement,$contents->identite->FchPat_Profession);
?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo preg_replace($pattern,$replacement,$contents->identite->FchPat_Titre) ?>" name="Titre"   />
<?php
    echo '
		'.preg_replace($pattern,$replacement,$contents->identite->FchPat_Titre); ?>
	      </td>
	      <td class="fond_td">
		<input type="radio" value="<?php echo 'fchpat_Note_Html'; ?>" name="Notes"   />
<?php
echo '
		'.'fchpat_Note_Html'?>
	      </td>
	    </tr>
<?php
}
?>
	  </table>
	  <p>
	    <input name="button_valid_merge" type="submit" value="Fusionner" />
	  </p>
	</form>
	</div>
   </div>
<?php
include("inc/footer.php");
?>