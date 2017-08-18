<?php
session_start() ;
//page non affichable. Cree un nouveau dossier et renvoie sur le dossier cree
if ( !isset( $_SESSION['login'] ) ) 
{
  header('location: index.php?page=nouveau_dossier' );
  exit;
}
$tab_login=explode("::",$_SESSION['login']);
$user=$tab_login[0];
$signuser=$tab_login[1];

include("config.php");
//variables a initialiser pour l'include de templates - ne serviront pas
$naissance='';
$adresse='';
$secu='';
$profession='';
$sexe='';
$genre[]='';
$nom='';
$prenom='';
$date='';
$titre='';
$sexe='M';
$genre['M']='';

include("templates.php"); //pour le terrain

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
function fullUpper($string){
  return strtr(strtoupper($string), array(
      "à" => "À",
      "è" => "È",
      "ì" => "Ì",
      "ò" => "Ò",
      "ù" => "Ù",
          "á" => "Á",
      "é" => "É",
      "í" => "Í",
      "ó" => "Ó",
      "ú" => "Ú",
          "â" => "Â",
      "ê" => "Ê",
      "î" => "Î",
      "ô" => "Ô",
      "û" => "Û",
          "ç" => "Ç",
    ));
}

function fixBirthDate($birthdate) //corriger certains formats de date de naissance
{
  $find=array('/',' ');//remplacer les mauvais séparateurs
  $replace=array('-','-');
  $birthdate=str_replace ($find,$replace,$birthdate);
  return $birthdate;
}

if (isset($_REQUEST['ID_PrimKey']))
  $ID_PrimKey=$_REQUEST['ID_PrimKey'];
if (isset($_REQUEST['Nom']))
  $Nom=fullUpper($_REQUEST['Nom']);
if (isset($_REQUEST['Prenom']))
  $Prenom=fullUpper($_REQUEST['Prenom']);
if (isset($_REQUEST['NomJF']))
  $NomJF=fullUpper($_REQUEST['NomJF']);
if (isset($_REQUEST['naissance']))
  $Date=fixBirthDate($_REQUEST['naissance']);
$list_date=explode ("-",$Date);
if ($date_format=='fr')
{
//on repasse en iso les dates qui arrivent en francais
  $Date=$list_date[2].'-'.$list_date[1].'-'.$list_date[0];
}
elseif ($date_format=='en')
  $Date=$list_date[2].'-'.$list_date[0].'-'.$list_date[1];

$DN_longue=str_replace(" ","",$Date)." 00:00:00";

if (isset($_REQUEST['sexe']))
  $sexe=$_REQUEST['sexe'];
if (isset($_REQUEST['gem']))
  $gem=$_REQUEST['gem'];
if (isset($_REQUEST['Tel1']))
  $Tel1=$_REQUEST['Tel1'];
$Tel1=str_replace(" ","",$Tel1);
if (isset($_REQUEST['Tel2']))
  $Tel2=$_REQUEST['Tel2'];
$Tel2=str_replace(" ","",$Tel2);
if (isset($_REQUEST['Tel3']))
  $Tel3=$_REQUEST['Tel3'];
$Tel3=str_replace(" ","",$Tel3);
if (isset($_REQUEST['Profession']))
  $Profession=$_REQUEST['Profession'];
if (isset($_REQUEST['Adresse']))
  $Adresse=$_REQUEST['Adresse'];
if (isset($_REQUEST['CP']))
  $CP=$_REQUEST['CP'];
if (isset($_REQUEST['Ville']))
  $Ville=$_REQUEST['Ville'];
if (isset($_REQUEST['Secu']))
{
  $Secu=$_REQUEST['Secu'];
$Secu=str_replace(" ","",$Secu);
}
if (isset($_REQUEST['cle_secu']))
{
  $cle_secu=$_REQUEST['cle_secu'];
  $Secu=$Secu.$cle_secu;
}
if (isset($_REQUEST['pas_patient']))
{
  $pas_patient="0"; //case cochee
}
else
  $pas_patient="1";
if (isset($_REQUEST['email']))
  $email=$_REQUEST['email'];
if (isset($_REQUEST['nom_assure']))
  $nom_assure=$_REQUEST['nom_assure'];
if (isset($_REQUEST['prenom_assure']))
  $prenom_assure=$_REQUEST['prenom_assure'];
if (isset($_REQUEST['titre']))
  $titre=$_REQUEST['titre'];
if (isset($_REQUEST['Note']))
  $Note=$_REQUEST['Note'];
if (isset($_REQUEST['Envoyer']))
  $Envoyer=$_REQUEST['Envoyer'];

if ($Envoyer=="Ajouter")//Execution de la creation de fiche
{
  $sql_chercher_doublon=$pdo->prepare('SELECT * FROM IndexNomPrenom INNER JOIN fchpat ON IndexNomPrenom.FchGnrl_IDDos=fchpat.FchPat_GUID_Doss WHERE FchGnrl_Prenom=? AND FchGnrl_NomDos=? AND FchPat_Nee=?');
  $sql_chercher_doublon->bindValue(1, $Prenom, PDO::PARAM_STR);
  $sql_chercher_doublon->bindValue(2, $Nom, PDO::PARAM_STR);
  $sql_chercher_doublon->bindValue(3, $DN_longue, PDO::PARAM_STR);
  $sql_chercher_doublon->execute();
  $ligne_chercher_doublon=$sql_chercher_doublon->fetch(PDO::FETCH_ASSOC); //un seul suffit
  $sql_chercher_doublon->closeCursor();

  if ($ligne_chercher_doublon) //s'il existe un doublon, on ne cree pas la fiche et on va directement dessus
  {
    header ("location:frame_patient.php?GUID=".$ligne_chercher_doublon['FchGnrl_IDDos']);
    exit;
  }
  $GUID=uuid(); //On fabrique un nouveau GUID
  
  $sql1=$pdo->prepare('INSERT INTO IndexNomPrenom (FchGnrl_IDDos,FchGnrl_NomDos,FchGnrl_Prenom) VALUES (?,?,?)');
  $sql1->bindValue(1, $GUID, PDO::PARAM_STR);
  $sql1->bindValue(2, $Nom, PDO::PARAM_STR);
  $sql1->bindValue(3, $Prenom, PDO::PARAM_STR);
  $sql1->execute();
  $sql1->closeCursor();  
  
  $id= $pdo->lastInsertId();//recuperation de l'ID de la fiche qui vient d'etre inseree
  
  $sql2=$pdo->prepare('INSERT INTO fchpat (FchPat_GUID_Doss,FchPat_NomFille,FchPat_Nee,FchPat_Sexe,FchPat_NbEnfant,FchPat_Adresse,FchPat_CP,FchPat_Ville,FchPat_Cdx,FchPat_Pays,FchPat_Tel1,FchPat_Tel1_Typ,FchPat_Tel2,  	FchPat_Tel2_Typ,FchPat_Tel3,FchPat_Tel3_Typ,FchPat_Email,FchPat_NumSS,FchPat_PatientAss,FchPat_NomAss,FchPat_PrenomAss,FchPat_Profession,FchPat_Titre,FchPat_RefPk,FchPat_Geme) VALUES (?,?,?,?,NULL,?,?,?,NULL,NULL,?,NULL,?,NULL,?,NULL,?,?,?,?,?,?,?,?,?)');
  $sql2->bindValue(1, $GUID, PDO::PARAM_STR);
  $sql2->bindValue(2, $NomJF, PDO::PARAM_STR);
  $sql2->bindValue(3, $DN_longue, PDO::PARAM_STR);
  $sql2->bindValue(4, $sexe, PDO::PARAM_STR);
  $sql2->bindValue(5, $Adresse, PDO::PARAM_STR);
  $sql2->bindValue(6, $CP, PDO::PARAM_STR);
  $sql2->bindValue(7, $Ville, PDO::PARAM_STR);
  $sql2->bindValue(8, $Tel1, PDO::PARAM_STR);
  $sql2->bindValue(9, $Tel2, PDO::PARAM_STR);
  $sql2->bindValue(10, $Tel3, PDO::PARAM_STR);
  $sql2->bindValue(11, $email, PDO::PARAM_STR);
  $sql2->bindValue(12, $Secu, PDO::PARAM_STR);
  $sql2->bindValue(13, $pas_patient, PDO::PARAM_STR);
  $sql2->bindValue(14, $nom_assure, PDO::PARAM_STR);
  $sql2->bindValue(15, $prenom_assure, PDO::PARAM_STR);
  $sql2->bindValue(16, $Profession, PDO::PARAM_STR);
  $sql2->bindValue(17, $titre, PDO::PARAM_STR);
  $sql2->bindValue(18, $id, PDO::PARAM_STR);
  $sql2->bindValue(19, $gem, PDO::PARAM_STR);
  $sql2->execute();
  $sql2->closeCursor();  
  
//on insere un terrain vierge
//d'abord le contenu
  $sql_inserer_note=$pdo->prepare('INSERT INTO RubriquesBlobs (RbDate_DataRub,RbDate_IDDos) VALUES (?,?)');
  $sql_inserer_note->bindValue(1, $terrain_modele, PDO::PARAM_STR);
  $sql_inserer_note->bindValue(2, $GUID, PDO::PARAM_STR);
  $sql_inserer_note->execute();
  $sql_inserer_note->closeCursor();  

 //on recupere l'ID pour la refiler au Head*/
  $id_blob_terrain=$pdo->lastInsertId();

  $date=date('Y-m-d H:i:s');
  //puis le titre
  $sql_inserer_titre_terrain=$pdo->prepare('INSERT INTO RubriquesHead ( RbDate_IDDos, RbDate_TypeRub, RbDate_NomDate, RbDate_Date, RbDate_CreateUser,RbDate_CreateSignUser, RbDate_Ref_NumDoss, RbDate_RefBlobs_PrimKey) VALUES (? ,"20060000", "Terrain",?,?,?,?,? )');
  $sql_inserer_titre_terrain->bindValue(1, $GUID, PDO::PARAM_STR);
  $sql_inserer_titre_terrain->bindValue(2, $date, PDO::PARAM_STR);
  $sql_inserer_titre_terrain->bindValue(3, $user, PDO::PARAM_STR);
  $sql_inserer_titre_terrain->bindValue(4, $signuser, PDO::PARAM_STR);
  $sql_inserer_titre_terrain->bindValue(5, $id, PDO::PARAM_STR);
  $sql_inserer_titre_terrain->bindValue(6, $id_blob_terrain, PDO::PARAM_STR);
  $sql_inserer_titre_terrain->execute();
  $sql_inserer_titre_terrain->closeCursor();  
  
  if ($Note) //Pas besoin de creer un enregistrement de notes s'il n'y en a pas 
  {
	/*  $sql3=sprintf("INSERT INTO fchpat_Note VALUES (NULL,'$GUID','$id','%s')",mysqli_real_escape_string($db,$Note)); //insertion des notes
	  $resultat3=mysqli_query($db,$sql3);*/
    $sql_inserer_note=$pdo->prepare('INSERT INTO fchpat_Note VALUES (NULL,?,?,?)');
    $sql_inserer_note->bindValue(1, $GUID, PDO::PARAM_STR);
    $sql_inserer_note->bindValue(2, $id, PDO::PARAM_STR);
    $sql_inserer_note->bindValue(3, $Note, PDO::PARAM_STR);
    $sql_inserer_note->execute();
    $sql_inserer_note->closeCursor();  
  }

  header('location:frame_patient.php?GUID='.$GUID); //aller directement sur la page du patient
  exit;
}
elseif ($Envoyer=="Modifier")//Execution de la modification de dossier
{
//On met a jour indexnomprenom
  $sql_index=$pdo->prepare('UPDATE IndexNomPrenom SET FchGnrl_NomDos=?,FchGnrl_Prenom=? WHERE ID_PrimKey=?');
  $sql_index->bindValue(1, $Nom, PDO::PARAM_STR);
  $sql_index->bindValue(2, $Prenom, PDO::PARAM_STR);
  $sql_index->bindValue(3, $ID_PrimKey, PDO::PARAM_STR);
  $sql_index->execute();
  $sql_index->closeCursor();  
   
//on met a jour fchpat
  $sql_fchpat=$pdo->prepare('UPDATE fchpat SET FchPat_NomFille=?,FchPat_Nee=?,FchPat_Sexe=?,FchPat_Adresse=?,FchPat_CP=?,FchPat_Ville=?,FchPat_Tel1=?,FchPat_Tel2=?,FchPat_Tel3=?,FchPat_Email=?,FchPat_NumSS=?,FchPat_PatientAss=?,FchPat_NomAss=?,FchPat_PrenomAss=?,FchPat_Profession=?,FchPat_Titre=?,FchPat_Geme=? WHERE FchPat_RefPk=?');
  $sql_fchpat->bindValue(1, $NomJF, PDO::PARAM_STR);
  $sql_fchpat->bindValue(2, $DN_longue, PDO::PARAM_STR);
  $sql_fchpat->bindValue(3, $sexe, PDO::PARAM_STR);
  $sql_fchpat->bindValue(4, $Adresse, PDO::PARAM_STR);
  $sql_fchpat->bindValue(5, $CP, PDO::PARAM_STR);
  $sql_fchpat->bindValue(6, $Ville, PDO::PARAM_STR);
  $sql_fchpat->bindValue(7, $Tel1, PDO::PARAM_STR);
  $sql_fchpat->bindValue(8, $Tel2, PDO::PARAM_STR);
  $sql_fchpat->bindValue(9, $Tel3, PDO::PARAM_STR);
  $sql_fchpat->bindValue(10, $email, PDO::PARAM_STR);
  $sql_fchpat->bindValue(11, $Secu, PDO::PARAM_STR);
  $sql_fchpat->bindValue(12, $pas_patient, PDO::PARAM_STR);
  $sql_fchpat->bindValue(13, $nom_assure, PDO::PARAM_STR);
  $sql_fchpat->bindValue(14, $prenom_assure, PDO::PARAM_STR);
  $sql_fchpat->bindValue(15, $Profession, PDO::PARAM_STR);
  $sql_fchpat->bindValue(16, $titre, PDO::PARAM_STR);
  $sql_fchpat->bindValue(17, $gem, PDO::PARAM_STR);
  $sql_fchpat->bindValue(18, $ID_PrimKey, PDO::PARAM_STR);
  $sql_fchpat->execute();
  $sql_fchpat->closeCursor();  
  
  $UUID=$_REQUEST['UUID'];
//on verifie que des notes existent pour pouvoir les mettre a jour

  $sql_verif=$pdo->prepare('SELECT * FROM fchpat_Note WHERE fchpat_Note_PatPK=?');
  $sql_verif->bindValue(1, $ID_PrimKey, PDO::PARAM_STR);
  $sql_verif->execute();
  $ligne_verif=$sql_verif->fetch(PDO::FETCH_ASSOC);
  $sql_verif->closeCursor();  

  if ($ligne_verif) //si note existe
  {
    $sql_note=$pdo->prepare('UPDATE fchpat_Note SET fchpat_Note_Html=? WHERE fchpat_Note_PatPK=?');
    $sql_note->bindValue(1, $Note, PDO::PARAM_STR);
    $sql_note->bindValue(2, $ID_PrimKey, PDO::PARAM_STR);
    $sql_note->execute();
    $sql_note->closeCursor();  
  }
  else //sinon, on cree la note
  {
    if ($Note) //seulement si une note a ete envoyee par l'URL
    {
      $sql_update_note=$pdo->prepare('INSERT INTO fchpat_Note values (NULL,?,?,?)');
      $sql_update_note->bindValue(1, $UUID, PDO::PARAM_STR);
      $sql_update_note->bindValue(2, $ID_PrimKey, PDO::PARAM_STR);
      $sql_update_note->bindValue(3, $Note, PDO::PARAM_STR);
      $sql_update_note->execute();
      $sql_update_note->closeCursor();  
    }
  }
  
//enlever_verrou apres modification fiche
  $sql_enlever_verrou=$pdo->prepare('DELETE FROM Verrous WHERE DossGUID=?');
  $sql_enlever_verrou->bindValue(1, $UUID, PDO::PARAM_STR);
  $sql_enlever_verrou->execute();
  $sql_enlever_verrou->closeCursor();  

  header('location:frame_patient.php?GUID='.$UUID);
  exit;
}
if (isset($_REQUEST['confirmer'])) //confirmation d'effacement
  $confirmer=$_REQUEST['confirmer'];
if ($confirmer=="Supprimer")
{
  $UUID=$_REQUEST['UUID'];

  $sql_delete_fchpat=$pdo->prepare('DELETE FROM fchpat WHERE FchPat_RefPk=?');
  $sql_delete_fchpat->bindValue(1, $ID_PrimKey, PDO::PARAM_STR);
  $sql_delete_fchpat->execute();
  $sql_delete_fchpat->closeCursor(); 
  
  $sql_delete_note=$pdo->prepare('DELETE FROM fchpat_Note WHERE fchpat_Note_PatGUID=?');
  $sql_delete_note->bindValue(1, $UUID, PDO::PARAM_STR);
  $sql_delete_note->execute();
  $sql_delete_note->closeCursor(); 
  
  $sql_delete_head=$pdo->prepare('DELETE FROM RubriquesHead WHERE RbDate_IDDos=?');
  $sql_delete_head->bindValue(1, $UUID, PDO::PARAM_STR);
  $sql_delete_head->execute();
  $sql_delete_head->closeCursor(); 
 
  $sql_delete_blob=$pdo->prepare('DELETE FROM RubriquesBlobs WHERE RbDate_IDDos=?');
  $sql_delete_blob->bindValue(1, $UUID, PDO::PARAM_STR);
  $sql_delete_blob->execute();
  $sql_delete_blob->closeCursor(); 

  $sql_delete_corresp=$pdo->prepare('DELETE FROM fchpat_Intervenants WHERE fchpat_Intervenants_PatPK=?');
  $sql_delete_corresp->bindValue(1, $ID_PrimKey, PDO::PARAM_STR);
  $sql_delete_corresp->execute();
  $sql_delete_corresp->closeCursor(); 
  
  $sql_delete_index=$pdo->prepare('DELETE FROM IndexNomPrenom WHERE ID_PrimKey=?');
  $sql_delete_index->bindValue(1, $ID_PrimKey, PDO::PARAM_STR);
  $sql_delete_index->execute();
  $sql_delete_index->closeCursor(); 
  
  header('location: liste.php' );
  exit;
}
elseif ($confirmer=="Conserver")
{
  header('location: liste.php?critere_recherche=FchGnrl_NomDos&cle='.$Nom.'&envoyer=Chercher' ); //renvoie sur la liste avec le nom du patient
  exit;
}
?>