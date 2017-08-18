<?php
//page utilisee par AJAX pour verifier si un nouveau login ne doublonne pas avec un existant
session_start() ;
header("Content-Type: text/plain");
if ( !isset( $_SESSION['login'] ) )
{
  header('location: index.php?page=index' );
  exit;
}

$idUser = (isset($_GET["User"])) ? $_GET["User"] : NULL;

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

if ($idUser) 
{
  $sql_user=$pdo->prepare("SELECT * FROM Personnes WHERE Login= ?");
  $sql_user->bindValue(1, $idUser, PDO::PARAM_STR);
  $sql_user->execute();
  $ligne_user=$sql_user->fetch(PDO::FETCH_ASSOC);
  $sql_user->closeCursor();
  
  if ($ligne_user)
  {
    echo "L'utilisateur ".$idUser." existe déjà. Choisissez un autre identifiant"; 
  }
  else
    echo "OK"; 
}
?>