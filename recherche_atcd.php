<?php
/*page utilisee par AJAX pour chercher les antecedents pathologiques CIM-X a ajouter au terrain
t4N
f0=identifiant, lie a t55.f1
f2=libelle
f3,f4=codecim 10
Exemple d'URL
http://localhost/MedWebTux/recherche_atcd.php?atcd=Maladies&num=1
*/
header("Content-Type: text/plain");

$atcd = (isset($_GET["atcd"])) ? $_GET["atcd"] : NULL;
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

if (strlen($atcd) > 2) 
{
  $resultat=$pdo->prepare('SELECT f2,f3 FROM t4N WHERE CAST(f2 AS CHAR) LIKE  ? ');
  $resultat->bindValue(1, addslashes($atcd).'%', PDO::PARAM_STR);
  $resultat->execute();
  $count=$resultat->fetchAll();
  $resultat->closeCursor();
  $resultat = NULL;
//f2 = atcd en clair
//f3= code cim10

  $atcds='';

  if ($count)
  {
    foreach ($count AS $my_atcd)
    {
      $atcds=$atcds.'|'.$my_atcd['f2'].' code cim10 '.$my_atcd['f3'];
    }
    echo $atcds."_".$num;
  }
  else
    echo "NOP_".$num;
}
else
  echo "NOP_".$num;
?>