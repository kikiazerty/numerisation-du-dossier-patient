<?php
session_start() ;

//http://localhost/MedWebTux/liaison_correspondant.php?ID_corresp=309&patient_ID=22550&patient_GUID=7bd2ea3b-b368-4d3f-af1e-e1f31eb9b83b&soumettre=Lier
if ( !isset( $_SESSION['login'] ) ) 
{
  header('location: index.php?page=correspondant' );
  exit;
}

include("config.php");

try 
{
  $strConnection = 'mysql:host='.$host.';dbname='.$base; 
  $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); 
  $pdo = new PDO($strConnection, $loginbase, $pwd, $arrExtraParam); // Instancie la connexion
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) 
{
  $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
  die($msg);
}

$ID_corresp="";
$envoyer="";
$soumettre="";
$soumettre_patient="";
$exact="";

if (isset($_GET['ID_corresp']))
  $ID_corresp=$_GET['ID_corresp'];
if (isset($_GET['envoyer']))
  $envoyer=$_GET['envoyer'];
if (isset($_GET['soumettre']))
  $soumettre=$_GET['soumettre'];
if (isset($_GET['cle']))
  $cle=$_GET['cle'];
if (isset($_GET['patient_ID']))
  $patient_ID=$_GET['patient_ID'];
if (isset($_GET['patient_GUID']))
  $patient_GUID=$_GET['patient_GUID'];
if (isset($_GET['ID_fiche_liaison']))
  $ID_fiche_liaison=$_GET['ID_fiche_liaison'];
if (isset($_GET['from']))
  $from=$_GET['from'];
if (isset($_GET['soumettre_patient']))
  $soumettre_patient=$_GET['soumettre_patient'];
if (isset($_GET['ID_patient']))
  $ID_patient=$_GET['ID_patient'];
if (isset($_GET['exact']))
  $exact=$_GET['exact'];
if (isset($_GET['liste_spe']))
  $liste_spe=$_GET['liste_spe'];
$type_intervenant=' ';

if (isset($_GET['select_type']))
{
  $type_intervenant=$_GET['select_type']; //on recupere le select
}
if (isset($_GET['text_type']))
{
  if ($_GET['text_type'])
  $type_intervenant=$_GET['text_type']; //si le ligne de texte est nonn vide, on l'utilise pour ecraser le select
}

if ($soumettre=="Lier")
{
//On fait la liaison et on revient a la fiche patient
//on cherche si la liaison n'existe pas deja pour eviter les doublons
  $sql_chercher_doublons=$pdo->prepare('SELECT * FROM fchpat_Intervenants WHERE fchpat_Intervenants_IntervPK=? AND fchpat_Intervenants_PatGUID=?');
  $sql_chercher_doublons->bindValue(1, $ID_corresp, PDO::PARAM_STR);
  $sql_chercher_doublons->bindValue(2, $patient_GUID, PDO::PARAM_STR);
  $sql_chercher_doublons->execute();
  $ligne_chercher_doublons=$sql_chercher_doublons->fetch(PDO::FETCH_ASSOC); //un seul suffit
  $sql_chercher_doublons->closeCursor();

  if (!$ligne_chercher_doublons)
  {
    $sql_liaison=$pdo->prepare('INSERT INTO fchpat_Intervenants (fchpat_Intervenants_PatGUID,fchpat_Intervenants_PatPK,fchpat_Intervenants_IntervPK,fchpat_Intervenants_Type) VALUES (?,?,?,?)');
    $sql_liaison->bindValue(1, $patient_GUID, PDO::PARAM_STR);
    $sql_liaison->bindValue(2, $patient_ID, PDO::PARAM_STR);
    $sql_liaison->bindValue(3, $ID_corresp, PDO::PARAM_STR);
    $sql_liaison->bindValue(4, $type_intervenant, PDO::PARAM_STR);
    $sql_liaison->execute();
    $sql_liaison->closeCursor();
  }
?>
  <script language="javascript">
  top.location.href = "frame_patient.php?GUID=<?php echo $patient_GUID ?>"
  </script>
<?php  exit;
}
$sql=$pdo->prepare('SELECT Nom,Prenom FROM Personnes WHERE ID_PrimKey=?');
$sql->bindValue(1, $ID_corresp, PDO::PARAM_STR);
$sql->execute();
$liste=$sql->fetch(PDO::FETCH_ASSOC); //un seul suffit
$sql->closeCursor();


if ($envoyer=="Oui") //On confirme la suppression de la fiche
{
  $sql_supprimer_corresp=$pdo->prepare('DELETE FROM fchpat_Intervenants WHERE fchpat_Intervenants_PK=?');
  $sql_supprimer_corresp->bindValue(1, $ID_fiche_liaison, PDO::PARAM_STR);
  $sql_supprimer_corresp->execute();
  $sql_supprimer_corresp->closeCursor();
  
  
  if ($from=="intervenant")
    header('location: fiche_intervenant.php?intervenant='.$ID_corresp );
  elseif ($from=="patient")
  {
?>   
    <script language="javascript">
      top.location.href = "frame_patient.php?GUID=<?php echo $patient_ID ?>"
    </script>
<?php
    exit;
  }
}
elseif ($envoyer=="Non") //on refuse la suppression de la fiche
{
  if ($from=="intervenant")
    header('location: fiche_intervenant.php?intervenant='.$ID_corresp.'&montrer_patients=Montrer');
  elseif ($from=="patient")
{
?>
    <script language="javascript">
    top.location.href = "frame_patient.php?GUID=<?php echo $patient_ID ?>"
    </script>
<?php
}

  exit;
}
include("inc/header.php");
?>
    <title>
      Liaison à <?php echo $liste['Nom'].' '.$liste['Prenom'] ?> - Utilisateur <?php echo $_SESSION['login'] ?>
    </title>
    <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen" />
<?php
if ($envoyer!="Annuler le lien") //pas besoin de javascript sur la page d'annulation
{
?>
<script type="text/javascript">
<!--
function donner_focus(chp)
{
document.getElementById(chp).focus();
}
-->
</script>

<script type="text/javascript">
<!--
function verif_champ(champ)
{
  if (champ == "")
  {
    alert("Le champ n'est pas rempli\nMettez le signe % si vous voulez vraiment afficher tout le fichier");
    return false;
  }
  return true;
}
-->
</script>
<?php
}
?>
  </head>
    <body style="font-size:<?php echo $fontsize; ?>pt" onload="donner_focus('cle')">
      <div class="conteneur">
<?php	
// insertion du menu d'en-tete	
$anchor='Liaison_à_un_intervenant';
if ($envoyer!='patient')
  include("inc/menu-horiz.php");
?>
<?php

if ($envoyer=="Annuler le lien")
{
  $sql_ID=$pdo->prepare('SELECT FchGnrl_NomDos,FchGnrl_Prenom FROM IndexNomPrenom WHERE FchGnrl_IDDos=?');
  $sql_ID->bindValue(1, $patient_ID, PDO::PARAM_STR);
  $sql_ID->execute();
  $ligne_ID=$sql_ID->fetch(PDO::FETCH_ASSOC); //un seul suffit
  $sql_ID->closeCursor();

  $sql_corresp=$pdo->prepare('SELECT Nom FROM Personnes WHERE ID_PrimKey=?');
  $sql_corresp->bindValue(1, $ID_corresp, PDO::PARAM_STR);
  $sql_corresp->execute();
  $ligne_corresp=$sql_corresp->fetch(PDO::FETCH_ASSOC); //un seul suffit
  $sql_corresp->closeCursor();

?>
  <div class="groupe">
	<h1>
	  MedWebTux - Liaison &agrave; l'intervenant
	</h1>

	Confirmez-vous la suppression de la liaison entre le patient <b><?php echo $ligne_ID['FchGnrl_NomDos']." ".$ligne_ID['FchGnrl_Prenom'] ?></b> et l'intervenant <b><?php echo  $ligne_corresp['Nom'] ?></b>&nbsp;?
	<br />
	<form action="liaison_correspondant.php" method="get" target="_top">
	  <div style="text-align:center;">
	    <input name="from" type="hidden" value="<?php echo $from ?>" />
	    <input name="ID_corresp" type="hidden" value="<?php echo $ID_corresp ?>" />
	    <input name="patient_ID" type="hidden" value="<?php echo $patient_ID ?>" />
	    <input name="ID_fiche_liaison" type="hidden" value="<?php echo $ID_fiche_liaison ?>" />
	    <input name="envoyer" type="submit" value="Oui" />
	    <input name="envoyer" type="submit" value="Non" />
	  </div>
	</form>
  </div>
<?php
}

if ($envoyer=="Lier un nouveau patient" or $soumettre=="Chercher")
{
?>
  <div class="groupe">
	<h1>
	  Liaison &agrave; l'intervenant <a href="fiche_intervenant.php?intervenant=<?php echo $ID_corresp ?>" target="_top"><?php echo $liste['Nom']?></a>
	</h1>
	<table>
	  <tr>
	    <td>
	      <form action="liaison_correspondant.php" method="get" onsubmit="return verif_champ(this.cle.value);" >
		<fieldset>
		  <legend>
		    Nom du patient : 
		  </legend>
		  <input name="ID_corresp" type="hidden" value="<?php echo $ID_corresp ?>" />
		  <input name="cle" id="cle" type="text" value="" size="40" title="Donnez ici une partie du nom du patient &agrave; lier ou le nom exact si vous cochez la case en dessous" />
		  <br />
		  <input name="exact" id="exact" type="checkbox" value="1" />
		  <label for="exact">
		    Correspondance exacte
		  </label>
		</fieldset>
		<p>			
		  <input name="soumettre" type="submit" value="Chercher" />
		</p>
	      </form>
	    </td>
	  </tr>
	</table>
    </div>
<?php
}
elseif ($envoyer=="patient" or $soumettre_patient=="Chercher")
{
  $GUID=$_GET['GUID'];
  
  $sql_nom=$pdo->prepare('SELECT FchGnrl_NomDos,ID_PrimKey FROM IndexNomPrenom WHERE FchGnrl_IDDos=?');
  $sql_nom->bindValue(1, $GUID, PDO::PARAM_STR);
  $sql_nom->execute();
  $ligne_nom=$sql_nom->fetch(PDO::FETCH_ASSOC); //un seul suffit
  $sql_nom->closeCursor();
?>
  <div class="groupe">
      <h1>
	Liaison au patient <a href="frame_patient.php?GUID=<?php echo $GUID ?>" target="_top"><?php echo $ligne_nom['FchGnrl_NomDos'] ?></a>
      </h1>
      <table>
	<tr>
	  <td>
	    <form action="liaison_correspondant.php" method="get"  onsubmit="return verif_champ(this.cle.value);">
	      <p>
		Sp&eacute;cialit&eacute; : 
		<select name="liste_spe" >
		  <option value="%">
		    Toutes
		  </option>
<?php

  $sql_specialite=$pdo->prepare('SELECT Qualite FROM Personnes GROUP BY Qualite ORDER BY Qualite');
  $sql_specialite->execute();
  $ligne_specialite_all=$sql_specialite->fetchAll(PDO::FETCH_ASSOC);
  $sql_specialite->closeCursor();

  foreach ($ligne_specialite_all AS $ligne_specialite)//recherche des specialites pour en faire un deroulant
  { 
    if ($ligne_specialite['Qualite'])//suppression des lignes vides
    {
?>
		  <option value="<?php echo $ligne_specialite['Qualite'];?>" >
		    <?php echo $ligne_specialite['Qualite'] ?>
		  </option>
<?php
    }
  }
?>
		</select>
	      </p>
	      <p>
		Nom de l'intervenant : 
		<input name="cle" type="text" value="" size="40" />
		<input name="GUID" type="hidden" value="<?php echo $GUID ?>" />
	      </p>
	      <p>
		<input name="exact" type="checkbox" value="1" />
		Correspondance exacte
	      </p>
	      <p>
		<input name="soumettre_patient" type="submit" value="Chercher"  />
	      </p>
	    </form>
	  </td>
	</tr>
      </table>
  </div>
<?php
}
//Creer un deroulant avec tous les types de liens connus
$sql_chercher_types=$pdo->prepare('SELECT fchpat_Intervenants_Type FROM fchpat_Intervenants GROUP BY fchpat_Intervenants_Type');
$sql_chercher_types->execute();
$ligne_chercher_types_all=$sql_chercher_types->fetchAll(); 
$sql_chercher_types->closeCursor();

//mode de liaison a partir du dossier patient
if ($soumettre=="Chercher") //recherche de patient selon la cle fournie pour remplir la liste et les valeurs cachees des boutons Lier
{
  if ($exact!=1)
    $cle="%".$cle."%";

  $sql_patient=$pdo->prepare('SELECT * FROM IndexNomPrenom INNER JOIN fchpat ON IndexNomPrenom.FchGnrl_IDDos=fchpat.FchPat_GUID_Doss WHERE IndexNomPrenom.FchGnrl_NomDos LIKE ? ORDER BY FchGnrl_NomDos,FchGnrl_Prenom');
  $sql_patient->bindValue(1, $cle, PDO::PARAM_STR);
  $sql_patient->execute();
  $liste_patient_all=$sql_patient->fetchAll(PDO::FETCH_ASSOC); //un seul suffit
  $sql_patient->closeCursor();

?>
    <div class="groupe">
      <table>
	<tr>
	  <th class="fond_th">
	    Nom
	  </th>
	  <th class="fond_th">
	    Pr&eacute;nom
	  </th>
	  <th class="fond_th">
	    Date de naissance
	  </th>
	  <th class="fond_th">
	    Intervenant
	  </th>
	</tr>
<?php
  $sql_chercher_doublons=$pdo->prepare('SELECT * FROM fchpat_Intervenants WHERE fchpat_Intervenants_IntervPK=? AND fchpat_Intervenants_PatGUID=?');

  foreach ($liste_patient_all AS $liste_patient)
  {
    $annee = substr($liste_patient["FchPat_Nee"], 0, 4);
    $mois = substr($liste_patient["FchPat_Nee"], 5, 2);
    $jour = substr($liste_patient["FchPat_Nee"], 8, 2);
?>
	<tr>
	  <td class="fond_td">
	    <?php echo $liste_patient['FchGnrl_NomDos'] ?>
	  </td>
	  <td class="fond_td">
	    <?php echo $liste_patient['FchGnrl_Prenom'] ?>
	  </td>
	  <td class="fond_td">
	    <?php echo  $jour."-". $mois."-". $annee ?>
	  </td>
	  <td class="fond_td">
<?php
//on cherche si la liaison n'existe pas deja pour eviter les doublons
    $GUID=$liste_patient['FchGnrl_IDDos'];
    
    $sql_chercher_doublons->bindValue(1, $ID_corresp, PDO::PARAM_STR);
    $sql_chercher_doublons->bindValue(2, $GUID, PDO::PARAM_STR);
    $sql_chercher_doublons->execute();
    $ligne_chercher_doublons=$sql_chercher_doublons->fetch(PDO::FETCH_ASSOC); //un seul suffit
      
    if (!$ligne_chercher_doublons)
    {
?>
	    <form action="liaison_correspondant.php"><!--Bouton Lier -->
	      <div>
		<input name="ID_corresp" type="hidden" value="<?php echo $ID_corresp ?>" />
		<input name="patient_ID" type="hidden" value="<?php echo $liste_patient['ID_PrimKey'] ?>" />
		<input name="patient_GUID" type="hidden" value="<?php echo $liste_patient['FchGnrl_IDDos'] ?>" />
                <input type="text" name="text_type" id ="text_type" />
                <select name="select_type" id="select_type">
                  <!--<option value="type">
                    Type de liaison
                  </option>-->
                  <?php
                  foreach ($ligne_chercher_types_all AS $ligne_chercher_types)
                  {

                  echo "<option value=\"$ligne_chercher_types[0]\">";echo $ligne_chercher_types[0];echo "</option>";
                  }
                  ?>
		</select>
		<input name="soumettre" type="submit" value="Lier" /> 
	      </div>
	    </form>
<?php
    }
    else
      echo "
	    Intervenant déjà lié";
?>
	  </td>
	</tr>
<?php
  }
  $sql_chercher_doublons->closeCursor();
?>
      </table>
  </div>
<?php
}
//Mode de liaison a partir du dossier intervenant
if ($soumettre_patient=="Chercher")
{
  if ($exact==1)
    $cle="%".$cle."%";
  
  $sql_intervenant=$pdo->prepare('SELECT * FROM Personnes WHERE Nom LIKE ? AND Qualite LIKE ? ORDER BY Nom');
  $sql_intervenant->bindValue(1, $cle, PDO::PARAM_STR);
  $sql_intervenant->bindValue(2, $liste_spe, PDO::PARAM_STR);
  $sql_intervenant->execute();
  $liste_intervenant_all=$sql_intervenant->fetchAll(PDO::FETCH_ASSOC); //un seul suffit
  $sql_intervenant->closeCursor();
?>
   <div class="groupe">
      <table>
	<tr>
	  <th class="fond_th">
	    Nom
	  </th>
	  <th class="fond_th">
	    Adresse
	  </th>
	  <th class="fond_th">
	    Sp&eacute;cialit&eacute;
	  </th>
	  <th class="fond_th">
	    Patient
	  </th>
	</tr>
<?php

  $sql_chercher_doublons=$pdo->prepare('SELECT * FROM fchpat_Intervenants WHERE fchpat_Intervenants_IntervPK=? AND fchpat_Intervenants_PatGUID=?');

 //   print_r($ligne_chercher_types_all);
    
  foreach ($liste_intervenant_all AS $liste_intervenant)
  {
?>
	<tr>
	  <td class="fond_td">
	    <a href="fiche_intervenant.php?intervenant=<?php echo $liste_intervenant['ID_PrimKey']?>" ><?php echo $liste_intervenant['Nom'] ?> <?php echo $liste_intervenant['Prenom'] ?></a>
	  </td>
	  <td class="fond_td">
	    <?php echo $liste_intervenant['Adresse'] ?> <?php echo $liste_intervenant['CodePostal'] ?>
	  </td>
	  <td class="fond_td">
	    <?php echo $liste_intervenant['Qualite'] ?>
	  </td>
	  <td class="fond_td">
<?php
//on cherche si la liaison n'existe pas deja pour eviter les doublons
    $ID_corresp=$liste_intervenant['ID_PrimKey'];
    
    $sql_chercher_doublons->bindValue(1, $ID_corresp, PDO::PARAM_STR);
    $sql_chercher_doublons->bindValue(2, $GUID, PDO::PARAM_STR);
    $sql_chercher_doublons->execute();
    $ligne_chercher_doublons=$sql_chercher_doublons->fetch(PDO::FETCH_ASSOC); //un seul suffit
    
    
    if (!$ligne_chercher_doublons)
    {
?>
	    <form action="liaison_correspondant.php"><!--Bouton Lier -->
	      <div>
		<input name="ID_corresp" type="hidden" value="<?php echo $liste_intervenant['ID_PrimKey'] ?>" />
		<input name="patient_ID" type="hidden" value="<?php echo $ligne_nom['ID_PrimKey'] ?>" />
		<input name="patient_GUID" type="hidden" value="<?php echo $GUID ?>" />
<!-- 		Mecanisme basique. Reprendre celui de nouveau correspondant -->
                <input type="text" name="text_type" id ="text_type" />
                <select name="select_type" id="select_type">
                  <!--<option value="type">
                    Type de liaison
                  </option>-->
                  <?php
                  foreach ($ligne_chercher_types_all AS $ligne_chercher_types)
                  {

                  echo "<option value=\"$ligne_chercher_types[0]\">";echo $ligne_chercher_types[0];echo "</option>";
                  }
                  ?>
		</select>
		<input name="soumettre" type="submit" value="Lier" /> 
	      </div>
	    </form>
<?php
    }
    else
      echo "
	    Intervenant déjà lié";
?>

	  </td>

	</tr>
<?php
  }
  $sql_chercher_doublons->closeCursor();
 ?>
      </table>
  </div>
<?php
}
?>
<?php
include("inc/footer.php");
?>