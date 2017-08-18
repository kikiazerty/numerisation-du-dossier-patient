<?php
//Page destinee a modifier en tache de fond les statuts des rendez-vous dynamiquement depuis la liste
session_start() ;
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

$id=$_REQUEST['id'];
$status=$_REQUEST['status'];

$sql_update_appointment_status=$pdo->prepare('UPDATE agenda SET `status`=? WHERE `PrimKey`=?');
$sql_update_appointment_status->bindValue(1, $status, PDO::PARAM_STR);
$sql_update_appointment_status->bindValue(2, $id, PDO::PARAM_STR);
$sql_update_appointment_status->execute();
$sql_update_appointment_status->closeCursor();

?>