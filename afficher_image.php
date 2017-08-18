<?php
/*
Fichier qui est appelé par les modules qui ont beson de décoder des images en base64.
On recoit le numero du document a convertir et le numero de l'image dans le document, on convertit l'image base 64 et on la renvoie en tant qu'image
*/
session_start() ;
if ( !isset( $_SESSION['login'] ) ) 
{
  header('location: index.php?page=nouveau_dossier' );
  exit;
}
include("config.php");
error_reporting(-1);

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

if (isset($_REQUEST['RbDate_PrimKey']))
  $RbDate_PrimKey =$_REQUEST['RbDate_PrimKey'];
if (isset($_REQUEST['compteur_image']))
  $compteur_image =$_REQUEST['compteur_image'];
  
header('Content-type: image/png');

$sql_recherche_doc=$pdo->prepare('SELECT RbDate_DataRub FROM RubriquesBlobs WHERE RbDate_PrimKey  = ?');
$sql_recherche_doc->bindValue(1, $RbDate_PrimKey, PDO::PARAM_STR);
$sql_recherche_doc->execute();
$ligne_recherche_doc=$sql_recherche_doc->fetch(PDO::FETCH_ASSOC);

$ce_doc=$ligne_recherche_doc["RbDate_DataRub"] ; 
$sql_recherche_doc->closeCursor();

$tableau_lignes_texte=explode("\n",$ce_doc);//on explose ce document aux retours chariot
$compteur_base64=0;
$afficher_ligne=0;

foreach ($tableau_lignes_texte AS $cette_ligne)
{
  if ($afficher_ligne==1)
  {
    echo base64_decode($cette_ligne); //l'image brut, renvoyee a la page appelante
  }
  if (preg_match('`<base64>`',$cette_ligne) AND $compteur_image==$compteur_base64++) // on recherche debut balise image dans le document
  {
    $afficher_ligne=1;
    $compteur_base64++;
  }
  if (preg_match('`</base64>`',$cette_ligne)) // on recherche fin balise image dans le document
  {
    $afficher_ligne=0;
  }
}	
?>
