<?php
//page utilisee par AJAX pour chercher les intolerances medicamenteuses a ajouter au terrain
//url de type recherche_intolerances.php?substance=amox&num=1
header("Content-Type: text/plain");

$substance = (isset($_GET["substance"])) ? $_GET["substance"] : NULL;
$num = (isset($_GET["num"])) ? $_GET["num"] : NULL; //Le numero du terrain a modifier
$basemed="DatasempTest";

include("config.php");

try 
{
  $strConnection = 'mysql:host='.$host.';dbname='.$basemed;
  $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
  $pdo = new PDO($strConnection, $loginbase, $pwd,$arrExtraParam); // Instancie la connexion
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) 
{
  $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
  die($msg);
}
/*
$db=mysqli_connect($host, $loginbase, $pwd);
$codage=mysqli_query($db,"SET NAMES 'UTF8'");

if (!mysqli_select_db($db,$basemed))
{
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/style.css\" />";
echo "<div class=\"erreur\">
<b>Erreur de connexion &agrave; la base de donn&eacute;es h&eacute;berg&eacute; sur ".$_SERVER["HTTP_HOST"]."<br /><br /></b>
Veuillez vous assurer que la base de donn&eacute;es est active sur le serveur, que vous avez acc&egrave;s au serveur, que le port qu'utilise MySQL n'est pas bloqu&eacute; par un pare-feu, et que vos identifiants et mot de passe d'administration de la base de donn&eacute;es sont bien renseign&eacute;s. <br /><br />
</div>";
//	exit;
}
*/
$request_substance="SELECT f0,f2 FROM t27 WHERE CAST(f2 AS CHAR) LIKE ?";

if (strlen($substance) > 2) 
{

/*  $sql="SELECT f0,f2 FROM t27 WHERE CAST(f2 AS CHAR) LIKE '".mysqli_real_escape_string($db,$substance)."%'"; //f2 = nom, f0 = numero substance
//echo $sql;
  $resultat=mysqli_query($db,$sql);
*/
  $resultat=$pdo->prepare($request_substance);
  $resultat->bindValue(1,$substance.'%', PDO::PARAM_STR);
  $resultat->execute();
  $count=$resultat->fetchAll();
  $resultat->closeCursor();
  $resultat = NULL;
  $substances='';

  if ($count)
  {
    foreach ($count AS $this_substance)
    {
        $substances=$substances.'|'.$this_substance['f2'].' code substance '.$this_substance['f0'];
    }
    echo $substances."_".$num;
  }
  else
    echo "NOP_".$num; 
}
else
  echo "NOP_".$num; 
?>

