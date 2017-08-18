<?php
session_start() ;
if ( !isset( $_SESSION['login'] ) ) 
{
  header('location: index.php?page=agenda' );
  exit;
}
$user=$_SESSION['login'];
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

function uuid() //génération de nombres aleatoires
{
  return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	  mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
	  mt_rand( 0, 0x0fff ) | 0x4000,
	  mt_rand( 0, 0x3fff ) | 0x8000,
	  mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
}
//le mode vcard
if (isset($_FILES['selection'])) 
{
  if ($selection = $_FILES['selection']['tmp_name'])
    echo "";
  else
    echo "echec de FILES<br>"; //pas bon :-(
  if ($fichier=fopen($selection,"r")) //ouverture du fichier renvoye par l'interface
    echo ""; 
  else
    echo "echec fichier <br />";
  $filename_components=explode('.',$_FILES['selection']['name']);
  $contents=fread( $fichier,filesize($selection));
  $line=explode("\n",$contents);
  foreach ($line AS $this_line)
  {
    if ($this_line!='')
    {
    //correction pour apicrypt : on remplace le deux points par un point virgule sur la ligne nom
      $this_line=preg_replace('`^N:`','N;',$this_line);
      $encodage="";
      if (preg_match ('`BEGIN:`',$this_line))
      {
	$new_file=1;
      }
      elseif (preg_match ("`END:`",$this_line))
      {
	$new_file=0;
      }
      if ($new_file)
      {
	$elements=array_map('trim',explode(";",$this_line));
	$zero=$elements[0]; //la cle de debut de ligne
	if ($zero=="N")
	{
	  $titre='';
	  if (isset($elements[4]))
	    $titre=$elements[4];
	  $nom_long=$elements[1]; //champ qui suit la cle de debut de ligne
	  if (preg_match('`:`',$nom_long))
	  {
	    $nom_tableau=explode(":",$nom_long); // separe l'encodage
	    $encodage=strtoupper($nom_tableau[0]);
	    if ($encodage=='UTF-8')
	      $nom=$nom_tableau[1];
	    else
	      $nom=utf8_encode($nom_tableau[1]);
	  }
	  else //encodage absent
	    $nom=$nom_long;
	  if ($encodage=='UTF-8')
	    $prenom=$elements[2];
	  else
	    $prenom=utf8_encode($elements[2]);
	}
	elseif ($zero=="TITLE")
	{
	  $specialite_long=$elements[1];
	  $specialite_tableau=explode(":",$specialite_long);
	  $specialite=$specialite_tableau[1];
	}
	elseif ($zero=="ADR")
	{
	  $address=$elements[3];
	  $town=$elements[4]; //region = champ 5
	  $zipcode=$elements[6];
	}
	elseif ($zero=="TEL")
	{
	  $tel=explode(":",$elements[1]);
	}
	elseif ($zero=="EMAIL")
	{
	  $email=explode(":",$elements[2]);
	}
	elseif ($zero=="NOTE")
	{
	  $note=explode(":",$elements[1]);
	}
      }
      if ($new_file==0 AND $nom) //ne pas inserer fiche si nom vide
      {
	$GUID=uuid();
		
	$sql_insert_file=$pdo->prepare('INSERT INTO Personnes (GUID,Nom,Prenom,Adresse,CodePostal,Ville,Tel_1,EMail,Note,Qualite,Titre) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
        $sql_insert_file->bindValue(1, $GUID, PDO::PARAM_STR);
        $sql_insert_file->bindValue(2, $nom, PDO::PARAM_STR);
        $sql_insert_file->bindValue(3, $prenom, PDO::PARAM_STR);
        $sql_insert_file->bindValue(4, $address, PDO::PARAM_STR);
        $sql_insert_file->bindValue(5, $zipcode, PDO::PARAM_STR);
        $sql_insert_file->bindValue(6, $town, PDO::PARAM_STR);
        $sql_insert_file->bindValue(7, $tel[1], PDO::PARAM_STR);
        $sql_insert_file->bindValue(8, $email[1], PDO::PARAM_STR);
        $sql_insert_file->bindValue(9, $note[1], PDO::PARAM_STR);
        $sql_insert_file->bindValue(10, $specialite, PDO::PARAM_STR);
        $sql_insert_file->bindValue(11, $titre, PDO::PARAM_STR);
        $sql_insert_file->execute();
        $sql_insert_file->closeCursor();
      }
    }
  }
}

//mode suppression
$confirmer=$_GET['confirmer'];
$Nom=$_GET['Nom'];
if ($confirmer=="Conserver")
{
  header('location: correspondant.php?&critere_recherche=Nom&cle='.$Nom.'&envoyer=Chercher&intervenant_user[]=users&intervenant_user[]=no_users');
  exit;
}
elseif ($confirmer=="Supprimer") //suppression de fiche apres confirmation
{
  $ID_PrimKey=$_GET['ID_PrimKey'];
  
  $sql_supprimer_liaisons=$pdo->prepare('DELETE FROM fchpat_Intervenants WHERE fchpat_Intervenants_PK= ?');
  $sql_supprimer_liaisons->bindValue(1, $ID_PrimKey, PDO::PARAM_STR);
  $sql_supprimer_liaisons->execute();
  $sql_supprimer_liaisons->closeCursor();
  
  $sql_supprimer_fiche_corresp=$pdo->prepare('DELETE FROM Personnes WHERE ID_PrimKey=?');
  $sql_supprimer_fiche_corresp->bindValue(1, $ID_PrimKey, PDO::PARAM_STR);
  $sql_supprimer_fiche_corresp->execute();
  $sql_supprimer_fiche_corresp->closeCursor();
  
  header('location: correspondant.php?&critere_recherche=Nom&cle='.$Nom.'&envoyer=Chercher&intervenant_user[]=users&intervenant_user[]=no_users');
  exit;
}
$envoyer=$_GET['envoyer'];
$nom_corresp=$_GET['nom_corresp'];
$prenom_corresp=$_GET['prenom_corresp'];
$titre_corresp=$_GET['titre_corresp'];
$ordre_corresp=$_GET['ordre_corresp'];
$rpps_corresp=$_GET['rpps_corresp'];
$Adresse_corresp=$_GET['Adresse_corresp'];
$CP=$_GET['CP'];
$Ville=$_GET['Ville'];
$mail_corresp=$_GET['mail_corresp'];
$tel1_corresp=str_replace(" ",'',$_GET['tel1_corresp']);
$tel2_corresp=str_replace(" ",'',$_GET['tel2_corresp']);
$tel3_corresp=str_replace(" ",'',$_GET['tel3_corresp']);	
$tel1_abr_corresp=$_GET['tel1_abr_corresp'];
$tel2_abr_corresp=$_GET['tel2_abr_corresp'];
$tel3_abr_corresp=$_GET['tel3_abr_corresp'];

$liste_type_tel1=$_GET['liste_type_tel1'];

if ($liste_type_tel1=="Autre")
  $tel1_type_corresp=$_GET['tel1_type_corresp'];
else
  $tel1_type_corresp=$liste_type_tel1;

$liste_type_tel2=$_GET['liste_type_tel2'];

if ($liste_type_tel2=="Autre")
  $tel2_type_corresp=$_GET['tel2_type_corresp'];
else
  $tel2_type_corresp=$liste_type_tel2;

$liste_type_tel3=$_GET['liste_type_tel3'];

if ($liste_type_tel3=="Autre")
  $tel3_type_corresp=$_GET['tel3_type_corresp'];
else
  $tel3_type_corresp=$liste_type_tel3;

$sexe_intervenant=$_GET['sexe_intervenant'];
$politesse=$_GET['politesse'];
$Notes_corresp=$_GET['Notes_corresp'];

$liste_spe=$_GET['liste_spe']; //On recupere la specialite ou Autre

if ($liste_spe=="Autre")
  $specialite=$_GET['specialite']; //On recupere la nouvelle specialite si Autre
else
  $specialite=$liste_spe;

if ($envoyer=="Modifier")
{
  $ID_PrimKey=$_GET['ID_PrimKey'];

  $sql_modifier=$pdo->prepare('UPDATE Personnes SET Nom=?,Prenom=?,Adresse=?,CodePostal=?,Ville=?,NumOrdre=?,NumRPPS=?,Tel_1=?,Tel_2=?,Tel_3=?,Tel_Type1=?,Tel_Type2=?,Tel_Type3=?,Tel_Abr_1=?,Tel_Abr_2=?,Tel_Abr_3=?,Email=?,Qualite=?,Titre=?,Note=?,Sexe=?,Cher=? WHERE ID_PrimKey=?');
  $sql_modifier->bindValue(1, $nom_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(2, $prenom_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(3, $Adresse_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(4, $CP, PDO::PARAM_STR);
  $sql_modifier->bindValue(5, $Ville, PDO::PARAM_STR);
  $sql_modifier->bindValue(6, $ordre_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(7, $rpps_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(8, $tel1_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(9, $tel2_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(10, $tel3_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(11, $tel1_type_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(12, $tel2_type_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(13, $tel3_type_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(14, $tel1_abr_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(15, $tel2_abr_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(16, $tel3_abr_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(17, $mail_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(18, $specialite, PDO::PARAM_STR);
  $sql_modifier->bindValue(19, $titre_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(20, $Notes_corresp, PDO::PARAM_STR);
  $sql_modifier->bindValue(21, $sexe_intervenant, PDO::PARAM_STR);
  $sql_modifier->bindValue(22, $politesse, PDO::PARAM_STR);
  $sql_modifier->bindValue(23, $ID_PrimKey, PDO::PARAM_STR);
  $sql_modifier->execute();
  $sql_modifier->closeCursor();

  header('location:fiche_intervenant.php?intervenant='.$ID_PrimKey );
  exit;
}
elseif ($envoyer=="Ajouter")
{
  $GUID=uuid();

  $sql_ajouter=$pdo->prepare('INSERT INTO Personnes (GUID,Sexe,NumOrdre,NumRPPS,Nom,Prenom,Adresse,CodePostal,Ville,Cher,Tel_1,Tel_2,Tel_3,EMail,Note,Qualite,Titre,Tel_Type1,Tel_Type2,Tel_Type3,Tel_Abr_1,Tel_Abr_2,Tel_Abr_3) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');

  $sql_ajouter->bindValue(1, $GUID, PDO::PARAM_STR);
  $sql_ajouter->bindValue(2, $sexe_intervenant, PDO::PARAM_STR);
  $sql_ajouter->bindValue(3, $ordre_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(4, $rpps_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(5, $nom_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(6, $prenom_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(7, $Adresse_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(8, $CP, PDO::PARAM_STR);
  $sql_ajouter->bindValue(9, $Ville, PDO::PARAM_STR);
  $sql_ajouter->bindValue(10, $politesse, PDO::PARAM_STR);
  $sql_ajouter->bindValue(11, $tel1_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(12, $tel2_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(13, $tel3_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(14, $mail_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(15, $Notes_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(16, $specialite, PDO::PARAM_STR);
  $sql_ajouter->bindValue(17, $titre_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(18, $tel1_type_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(19, $tel2_type_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(20, $tel3_type_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(21, $tel1_abr_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(22, $tel2_abr_corresp, PDO::PARAM_STR);
  $sql_ajouter->bindValue(23, $tel3_abr_corresp, PDO::PARAM_STR);
  $sql_ajouter->execute();
  $sql_ajouter->closeCursor();
  $id= $pdo->lastInsertId(); //recuperation de l'ID de la fiche qui vient d'etre inseree
  
  header('location:fiche_intervenant.php?intervenant='.$id );
  exit;
}
?>
