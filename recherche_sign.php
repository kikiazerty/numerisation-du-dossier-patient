<?php
//page utilsée par AJAX pour chercher les utilisateurs signataires
header("Content-Type: text/plain");

$idUser = (isset($_GET["User"])) ? $_GET["User"] : NULL;

include("config.php");

try {
    $strConnection = 'mysql:host='.$host.';dbname='.$base; 
    $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); 
    $pdo = new PDO($strConnection, $loginbase, $pwd, $arrExtraParam); //Ligne 3; Instancie la connexion
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Ligne 4
}
catch(PDOException $e) {
    $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
    die($msg);
}

//rechercher ici si l'utilisateur est signataire pour ne pas le mettre si non signataire =sgn
if ($idUser) 
{
  $resultat=$pdo->prepare('SELECT Droits FROM Personnes WHERE Login = ? ');
  $resultat->bindValue(1, addslashes($idUser), PDO::PARAM_STR);
  $resultat->execute();
  $count=$resultat->fetchAll();
  $resultat->closeCursor();
  $resultat = NULL;
 
  if ($count)
  {
    if (preg_match('/sgn/',$count[0]['Droits']))
    {
      $users=$idUser;
    }
    else
      $users='';
    $resultat=$pdo->prepare('SELECT SignataireGUID FROM Personnes INNER JOIN user_perms ON Personnes.GUID=user_perms.FriendUserGUID WHERE Login= ?');
    $resultat->bindValue(1, addslashes($idUser), PDO::PARAM_STR);
    $resultat->execute();
    $count=$resultat->fetchAll();
    $resultat->closeCursor();
    $resultat = NULL;

    $resultat=$pdo->prepare('SELECT Login FROM Personnes WHERE GUID= ?'); //On prepare la requete avant la boucle pour ne pas la reexecuter.

    foreach ($count AS $this_signataire)
    {
      $signataire=$this_signataire['SignataireGUID'];

      $resultat->bindValue(1, $this_signataire['SignataireGUID'], PDO::PARAM_STR);
      $resultat->execute();
      $count=$resultat->fetchAll();

      if ($users)
      {
	$users=$users.'|'.$count[0]['Login'];
      }
      else
	$users=$count[0]['Login']; //pas de premier tour si utilisateur non signataire
    }
    echo $users;
  }
  else
    echo "NOP"; 
}
?>

