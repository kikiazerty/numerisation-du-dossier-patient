<?php
session_start() ;
//page invisible d'insertion d'image envoyee en tant que fichier dans le document en reference.
//renvoie a la page consultation.php
include("config.php");
if ( !isset( $_SESSION['login'] ) )
{
//On renvoie automatiquement sur la page de login
  header('location: index.php?page=liste' );
  exit;
}
$tab_login=explode("::",$_SESSION['login']);
$user=$tab_login[0];
$signuser=str_replace(" ","",$tab_login[1]); //pour chrome

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

if (isset($_FILES['selection'])) 
{
  if ($selection = $_FILES['selection']['tmp_name'])
  {
    echo '';
  }
  else
    echo "echec de FILES<br />"; 
  if ($fichier=fopen($selection,"r")) //ouverture du fichier renvoye par l'interface
    echo ""; 
  else
    echo "echec fichier<br />";

  $guid=$_REQUEST['guid'];
  $primkey=$_REQUEST['ID_PrimKey'];
  $radio_ratio=$_REQUEST['radio_ratio'];
  $filename_components=explode('.',$_FILES['selection']['name']);
  $contents=fread( $fichier,filesize($selection));
  $img=base64_encode($contents);
  $size=getimagesize($selection);
  $height=(int) $size[1]/$radio_ratio;
  $width=(int)$size[0]/$radio_ratio;

  $obs='<?xml version="1.0" encoding="ISO-8859-1" standalone="yes" ?>
<MedinTux_Multimedia_data>
<HTML_Data>
<html><head><meta name="qrichtext" content="1" /></head><body style="font-size:10pt;font-family:Arial">
<p style="text-align:center"><span style="font-family:Sans">
<img src="image.'.$filename_components[1].'" width="'.$width.'" height="'.$height.'" /></span>
</p></body></html>
</HTML_Data>
<ImageListe>
<name>
image.'.$filename_components[1].' 
</name>
<fileName>'.
$_FILES['selection']['name'].
'</fileName>
<ext_Type>
'.$filename_components[1].'
</ext_Type>
<base64>
'.$img.'
</base64>
</ImageListe>
</MedinTux_Multimedia_data>
\0';
  $sql_insert_obs=$pdo->prepare('INSERT INTO RubriquesBlobs (RbDate_DataRub,RbDate_IDDos) values (?,?)');
  $sql_insert_obs->bindValue(1, $obs, PDO::PARAM_STR);
  $sql_insert_obs->bindValue(2, $guid, PDO::PARAM_STR);
  $sql_insert_obs->execute();
  $sql_insert_obs->closeCursor();

  $heure=date('H:i:s', date('U')); // la date du jour
//pour convertir les formats de dates
  function local_to_iso($date,$date_format)
  {
    $list_date=explode ("-",$date);
    if ($date_format=='fr')
    {
      $date=$list_date[2].'-'.$list_date[1].'-'.$list_date[0];
    }
    elseif ($date_format=='en')
      $date=$list_date[2].'-'.$list_date[0].'-'.$list_date[1];
    return $date;
  }

  $date=local_to_iso($_REQUEST['date'],$date_format);
  $date=$date.' '.$heure;
  $title=$_REQUEST['title'];

  $id_blob= $pdo->lastInsertId();

  //on insere l'en-tete
  $rubrique=$_REQUEST['rubrique'];
  $sql_insert_header=$pdo->prepare('INSERT INTO RubriquesHead (RbDate_IDDos,RbDate_TypeRub,RbDate_NomDate,RbDate_SubTypeRub,RbDate_Date,RbDate_CreateUser,RbDate_CreateSignUser,RbDate_DureeMod,RbDate_Duree,RbDate_Ref_NumDoss,RbDate_RefBlobs_PrimKey) VALUES (?,?,?,"Image loaded",?,?,?,"-1","0",?,?)');
  $sql_insert_header->bindValue(1, $guid, PDO::PARAM_STR);
  $sql_insert_header->bindValue(2, $rubrique, PDO::PARAM_STR);
  $sql_insert_header->bindValue(3, $title, PDO::PARAM_STR);
  $sql_insert_header->bindValue(4, $date, PDO::PARAM_STR);
  $sql_insert_header->bindValue(5, $user, PDO::PARAM_STR);
  $sql_insert_header->bindValue(6, $signuser, PDO::PARAM_STR);
  $sql_insert_header->bindValue(7, $primkey, PDO::PARAM_STR);
  $sql_insert_header->bindValue(8, $id_blob, PDO::PARAM_STR);
  $sql_insert_header->execute();
  $sql_insert_header->closeCursor();
}

header ('location: consultation.php?numeroID='.$guid.'&date='.$date );
exit;
