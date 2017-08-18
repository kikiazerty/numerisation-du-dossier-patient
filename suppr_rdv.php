<?php
session_start() ;
//page non affichable pour effectuer les effacements de rendez-vous
if ( !isset( $_SESSION['login'] ) ) 
{
  header('location: index.php?page=agenda' );
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

//récupération des valeurs de l'URL

if (isset($_GET['confirmer']))
  $confirmer=$_GET['confirmer'];
else
  $confirmer="";

//url pour coches suppression en serie
// coche[]=6583&coche[]=6584&confirmer=Supprimer&button_supprimer=Supprimer
if (isset ($_REQUEST['debut'])) //MODE SUPPRESSION MUTIPLE
{
  $debut=$_REQUEST['debut'];
  $fin=$_REQUEST['fin'];
}
//MODE SUPPRESSION MUTIPLE
if ($confirmer=="Supprimer") //coche de confirmation
{
  if (isset ($_REQUEST['coche'])) //coches
  {
    $coche=$_REQUEST['coche'];
    $sqlsuppr=$pdo->prepare('DELETE FROM agenda WHERE PrimKey=?');
    foreach ($coche AS $id_rdv )
    {
      $sqlsuppr->bindValue(1, $id_rdv, PDO::PARAM_STR);
      $sqlsuppr->execute();
    }
    $sqlsuppr->closeCursor();  
  }
  header('location: agenda.php?debut='.$debut.'&fin='.$fin.'&envoyer=Chercher&nom=%');
  exit;
}
else //mode coches sans confirmation
{
  header('location: agenda.php?debut='.$debut.'&fin='.$fin.'&envoyer=Chercher&nom=%');
  exit;
}
?>