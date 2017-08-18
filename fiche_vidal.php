<?php
session_start() ;
include("config.php");
$basemed="DatasempTest";

if ( !isset( $_SESSION['login'] ) )
{
  header('location: index.php?page=medocs' );
  exit;
}

//connexion a datasemp
try {
    $strConnection = 'mysql:host='.$host.';dbname='.$basemed; 
    $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); 
    $pdodatasemp = new PDO($strConnection, $loginbase, $pwd, $arrExtraParam); 
    $pdodatasemp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdodatasemp->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 
}
catch(PDOException $e) {
    $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
    die($msg);
}

/*
t5C
  f0 identifiant ->
    t48.fO -> t48.f1 = code image ->t46.f0 t46.f1=nom image
      t5D.f1 ->f0
	t00.f1
    t5D.f1 
    t5E.f1
  f1 titre
  f3 monographe Vidal
t5D : relations documents-Unités de vente
t5D.f0=t00.f1
t5E Relation documents-produits
*/

$cuv_medoc=$_GET['cuv_medoc'];

$sql_fiche_vidal=$pdodatasemp->prepare("SELECT t5C.f3 AS fiche FROM t5C INNER JOIN t5D ON t5C.f0=t5D.f1 WHERE t5D.f0=?");
$sql_fiche_vidal->bindValue(1, $cuv_medoc, PDO::PARAM_STR);
$sql_fiche_vidal->execute();
$ligne_fiche_vidal=$sql_fiche_vidal->fetch(PDO::FETCH_ASSOC);
$sql_fiche_vidal->closeCursor();

$rcp=$ligne_fiche_vidal['fiche'];
//enlever l'en-ete xml
$rcp= str_replace ('<?xml version="1.0" encoding="windows-1252"?>','',$rcp);
//passer en utf-8
echo str_replace ('windows-1252','utf-8',$rcp);
?>

