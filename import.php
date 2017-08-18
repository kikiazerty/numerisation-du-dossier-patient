<?php
session_start() ;
//page non affichable fabriquant une fiche à partir d'un fichier xml précédemment exporté, avec conservation du GUID - si le dossier existe deja,on va sur la page de fusion.
include("config.php");
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

if ( !isset( $_SESSION['login'] ) )
{
//On renvoie automatiquement sur la page de login
  header('location: index.php?page=liste' );
  exit;
}
$tab_login=explode("::",$_SESSION['login']);
$user=$tab_login[0];

if (isset($_FILES['selection'])) 
{
  $selection = $_FILES['selection']['tmp_name'];
}
else
{
  header('location: index.php?page=liste' );
  exit;
}
/*
if ($fiche_xml=fopen($selection,r))
*/
  /*Pour info, Les remplacements faits a l'export
  $pattern[0]='/&lt;/';
  $pattern[1]='/&/';
  $pattern[2]='/</';
  $pattern[3]='^@';
  $replacement[0]='&amp;lt;';
  $replacement[1]='&amp;';
  $replacement[2]='&lt;';
  $replacement[2]='';
  */
//Remplacements pour restaurer la fiche comme elle etait avant les transformations de l'export pour eviter les caracteres interdits < et &
$pattern[0]='`/&lt;/`';
$pattern[1]='`/&amp;/`';
$pattern[2]='`/&amp;lt;/`';
$pattern[3]='`/\\0/`';
$replacement[0]='`<`';
$replacement[1]='`&`';
$replacement[2]='`&lt;`';
$replacement[3]='`\0`';

if ($contents=simplexml_load_file($selection))
{
  $FchGnrl_IDDos=$contents->identite->FchGnrl_IDDos;
  $FchGnrl_NomDos= preg_replace($pattern,$replacement,$contents->identite->FchGnrl_NomDos);
  $FchGnrl_Prenom= preg_replace($pattern,$replacement,$contents->identite->FchGnrl_Prenom);


  //On cherche le GUID

  //on verifie que cette fiche n'existe pas deja avant de l'inserer
  $sql_verif_doublon=$pdo->prepare("SELECT FchGnrl_IDDos FROM IndexNomPrenom WHERE FchGnrl_IDDos=?");
  $sql_verif_doublon->bindValue(1,$FchGnrl_IDDos, PDO::PARAM_STR);
  $sql_verif_doublon->execute();

  if ($sql_verif_doublon->fetch()) //si on trouve un resultat = ne pas creer un doublon. Verifier si ca marche
  {
 // $data_patient=load_file($selection);
    include ("merge.php");
 //   header('location: merge.php?selection_patient[]='.$FchGnrl_IDDos.'&contents[][]='.$_FILES['selection'] );
//    header('location: liste.php?message=message_doublon' );
    exit;
  }
  $sql_verif_doublon->closeCursor();

  //On est en mode creation
  $sql_index=$pdo->prepare("INSERT INTO IndexNomPrenom (FchGnrl_IDDos ,FchGnrl_NomDos ,FchGnrl_Prenom) VALUES (?,?,?)");
  $sql_index->bindValue(1,$FchGnrl_IDDos, PDO::PARAM_STR);
  $sql_index->bindValue(2,$FchGnrl_NomDos, PDO::PARAM_STR);
  $sql_index->bindValue(3,$FchGnrl_Prenom, PDO::PARAM_STR);
  $sql_index->execute();
  $sql_index->closeCursor();

  $FchPat_RefPk= $pdo->lastInsertId();

  $sql_fchpat=$pdo->prepare("INSERT INTO fchpat (FchPat_GUID_Doss, FchPat_NomFille, FchPat_Nee, FchPat_Sexe, FchPat_Adresse, FchPat_CP, FchPat_Ville, FchPat_Tel1 ,FchPat_Tel2, FchPat_Tel3, FchPat_Email, FchPat_NumSS, FchPat_PatientAss, FchPat_NomAss, FchPat_PrenomAss ,FchPat_Profession, FchPat_Titre, FchPat_RefPk,FchPat_LoginPatient,FchPat_PasswPatient, FchPat_Geme) VALUES (?,?, ?, ?, ?, ?, ?, ? ,?, ?, ?, ?,?, ?, ? ,?, ?, ?,?,?,?)");
  $sql_fchpat->bindValue(1,$FchGnrl_IDDos, PDO::PARAM_STR);
  $sql_fchpat->bindValue(2,preg_replace($pattern,$replacement,$contents->identite->FchPat_NomFille), PDO::PARAM_STR);
  $sql_fchpat->bindValue(3,$contents->identite->FchPat_Nee, PDO::PARAM_STR);
  $sql_fchpat->bindValue(4,$contents->identite->FchPat_Sexe, PDO::PARAM_STR);
  $sql_fchpat->bindValue(5,preg_replace($pattern,$replacement,$contents->identite->FchPat_Adresse), PDO::PARAM_STR);
  $sql_fchpat->bindValue(6,$contents->identite->FchPat_CP, PDO::PARAM_STR);
  $sql_fchpat->bindValue(7,preg_replace($pattern,$replacement,$contents->identite->FchPat_Ville), PDO::PARAM_STR);
  $sql_fchpat->bindValue(8,$contents->identite->FchPat_Tel1, PDO::PARAM_STR);
  $sql_fchpat->bindValue(9,$contents->identite->FchPat_Tel2, PDO::PARAM_STR);
  $sql_fchpat->bindValue(10,$contents->identite->FchPat_Tel3, PDO::PARAM_STR);
  $sql_fchpat->bindValue(11,$contents->identite->FchPat_Email, PDO::PARAM_STR);
  $sql_fchpat->bindValue(12,$contents->identite->FchPat_NumSS, PDO::PARAM_STR);
  $sql_fchpat->bindValue(13,$contents->identite->FchPat_PatientAss, PDO::PARAM_STR);
  $sql_fchpat->bindValue(14,preg_replace($pattern,$replacement,$contents->identite->FchPat_NomAss), PDO::PARAM_STR);
  $sql_fchpat->bindValue(15,preg_replace($pattern,$replacement,$contents->identite->FchPat_PrenomAss), PDO::PARAM_STR);
  $sql_fchpat->bindValue(16,preg_replace($pattern,$replacement,$contents->identite->FchPat_Profession), PDO::PARAM_STR);
  $sql_fchpat->bindValue(17,preg_replace($pattern,$replacement,$contents->identite->FchPat_Titre), PDO::PARAM_STR);
  $sql_fchpat->bindValue(18,$FchPat_RefPk, PDO::PARAM_STR);
  $sql_fchpat->bindValue(19,$contents->identite->FchPat_LoginPatient, PDO::PARAM_STR);
  $sql_fchpat->bindValue(20,$contents->identite->FchPat_PasswPatient, PDO::PARAM_STR);
  $sql_fchpat->bindValue(21,$contents->identite->FchPat_Geme, PDO::PARAM_STR);
  $sql_fchpat->execute();
  $sql_fchpat->closeCursor();

/*$FchPat_NomFille= preg_replace($pattern,$replacement,$contents->identite->FchPat_NomFille);
  $FchPat_Nee= preg_replace($pattern,$replacement,$contents->identite->FchPat_Nee);
  $FchPat_Sexe= preg_replace($pattern,$replacement,$contents->identite->FchPat_Sexe);
  $FchPat_Adresse= preg_replace($pattern,$replacement,$contents->identite->FchPat_Adresse);
  $FchPat_CP= preg_replace($pattern,$replacement,$contents->identite->FchPat_CP);
  $FchPat_Ville= preg_replace($pattern,$replacement,$contents->identite->FchPat_Ville);
  $FchPat_Tel1= preg_replace($pattern,$replacement,$contents->identite->FchPat_Tel1);
  $FchPat_Tel2= preg_replace($pattern,$replacement,$contents->identite->FchPat_Tel2);
  $FchPat_Tel3= preg_replace($pattern,$replacement,$contents->identite->FchPat_Tel3);
  $FchPat_Email= preg_replace($pattern,$replacement,$contents->identite->FchPat_Email);
  $FchPat_NumSS= $contents->identite->FchPat_NumSS;
  $FchPat_PatientAss= preg_replace($pattern,$replacement,$contents->identite->FchPat_PatientAss);
  $FchPat_NomAss= preg_replace($pattern,$replacement,$contents->identite->FchPat_NomAss);
  $FchPat_PrenomAss= preg_replace($pattern,$replacement,$contents->identite->FchPat_PrenomAss);
  $FchPat_Profession= preg_replace($pattern,$replacement,$contents->identite->FchPat_Profession);
  $FchPat_Titre= preg_replace($pattern,$replacement,$contents->identite->FchPat_Titre);
  $FchPat_LoginPatient= $contents->identite->FchPat_LoginPatient;
  $FchPat_PasswPatient= $contents->identite->FchPat_PasswPatient;
  $FchPat_Geme= $contents->identite->FchPat_Geme; 
  $fchpat_Note_Html= preg_replace($pattern,$replacement,$contents->identite->fchpat_Note_Html);*/

  $sql_notes=$pdo->prepare("INSERT INTO fchpat_Note (fchpat_Note_PatGUID,fchpat_Note_PatPK,fchpat_Note_Html) VALUES (?,?,?)");
  $sql_notes->bindValue(1,$FchGnrl_IDDos[0], PDO::PARAM_STR); //pourquoi [0] ?
  $sql_notes->bindValue(2,$FchPat_RefPk, PDO::PARAM_STR);
  $sql_notes->bindValue(3,preg_replace($pattern,$replacement,$contents->identite->fchpat_Note_Html), PDO::PARAM_STR);
  $sql_notes->execute();
  $sql_notes->closeCursor();

  //boucle pour recuperer les documents
  $doc_cnt = count($contents->documents->document); 
  $sql_blobs=$pdo->prepare("INSERT INTO RubriquesBlobs (RbDate_DataRub,RbDate_IDDos) VALUES (?,?)");
  $sql_head=$pdo->prepare("INSERT INTO RubriquesHead (RbDate_IDDos, RbDate_TypeRub, RbDate_NomDate, RbDate_SubTypeRub, RbDate_Date, RbDate_CreateUser, RbDate_CreateSignUser, RbDate_Fin, RbDate_DureeMod, RbDate_Ref_NumDoss,	RbDate_RefBlobs_PrimKey) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    
  for($i = 0; $i < $doc_cnt; $i++) 
  {
    $RbDate_DataRub=preg_replace($pattern,$replacement,$contents->documents->document[$i]->RbDate_DataRub);
    $RbDate_TypeRub=$contents->documents->document[$i]->RbDate_TypeRub;
    $RbDate_NomDate=preg_replace($pattern,$replacement,$contents->documents->document[$i]->RbDate_NomDate);
    $RbDate_SubTypeRub=preg_replace($pattern,$replacement,$contents->documents->document[$i]->RbDate_SubTypeRub);
    $RbDate_Date=$contents->documents->document[$i]->RbDate_Date;
    $RbDate_CreateUser=preg_replace($pattern,$replacement,$contents->documents->document[$i]->RbDate_CreateUser);
    //$RbDate_CreateSignUser=element_set('RbDate_CreateSignUser',$ce_document);
    $RbDate_Fin=preg_replace($pattern,$replacement,$contents->documents->document[$i]->RbDate_Fin);
    $RbDate_DureeMod=preg_replace($pattern,$replacement,$contents->documents->document[$i]->RbDate_DureeMod);
    
    $sql_blobs->bindValue(1,$RbDate_DataRub,PDO::PARAM_STR);
    $sql_blobs->bindValue(2,$FchGnrl_IDDos,PDO::PARAM_STR);
    $sql_blobs->execute();
      //on recupere l'ID du blob
    $Ref_blob= $pdo->lastInsertId();
    $sql_blobs->closeCursor();

    //On utilise l'utilisateur principal exportateur comme utilisateur delegue de l'import et l'utilisateur qui importe comme utilisateur principal
/*    $sql_head=sprintf("INSERT INTO RubriquesHead (RbDate_IDDos,RbDate_TypeRub,RbDate_NomDate,RbDate_SubTypeRub,RbDate_Date,	RbDate_CreateUser,RbDate_CreateSignUser,RbDate_Fin,RbDate_DureeMod, RbDate_Ref_NumDoss,	RbDate_RefBlobs_PrimKey) VALUES ('$FchGnrl_IDDos','$RbDate_TypeRub','%s','$RbDate_SubTypeRub','$RbDate_Date','$RbDate_CreateUser','$user','$RbDate_Fin','$RbDate_DureeMod','$FchPat_RefPk','$Ref_blob')",mysqli_real_escape_string($db,$RbDate_NomDate));
    $resultat_head=mysqli_query($db,$sql_head);
    */
    $sql_head->bindValue(1,$FchGnrl_IDDos,PDO::PARAM_STR); 
    $sql_head->bindValue(2,$contents->documents->document[$i]->RbDate_TypeRub,PDO::PARAM_STR); 
    $sql_head->bindValue(3,preg_replace($pattern,$replacement,$contents->documents->document[$i]->RbDate_NomDate),PDO::PARAM_STR); 
    $sql_head->bindValue(4,preg_replace($pattern,$replacement,$contents->documents->document[$i]->RbDate_SubTypeRub),PDO::PARAM_STR); 
    $sql_head->bindValue(5,$contents->documents->document[$i]->RbDate_Date,PDO::PARAM_STR); 
    $sql_head->bindValue(6,preg_replace($pattern,$replacement,$contents->documents->document[$i]->RbDate_CreateUser),PDO::PARAM_STR); 
    $sql_head->bindValue(7,$user,PDO::PARAM_STR); 
    $sql_head->bindValue(8,preg_replace($pattern,$replacement,$contents->documents->document[$i]->RbDate_Fin),PDO::PARAM_STR); 
    $sql_head->bindValue(9,preg_replace($pattern,$replacement,$contents->documents->document[$i]->RbDate_DureeMod),PDO::PARAM_STR); 
    $sql_head->bindValue(10,$FchPat_RefPk,PDO::PARAM_STR); 
    $sql_head->bindValue(11,$Ref_blob,PDO::PARAM_STR); 
    $sql_head->execute();
    $sql_head->closeCursor();
  } //fin de la boucle des documents
}//fin de la tentative d'ouverture du fichier xml
else
  echo "Fichier introuvable";

//on retourne sur la fiche qu'on vient de créer
header ('location:frame_patient.php?GUID='.$FchGnrl_IDDos[0]);
exit;
