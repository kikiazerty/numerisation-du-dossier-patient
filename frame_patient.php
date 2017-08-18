<?php
session_start() ;
include("config.php");

if ( !isset( $_SESSION['login'] ) )
{
  header('location: index.php?page=liste' );
  exit;
}
$tab_login=explode("::",$_SESSION['login']);
$user=$tab_login[0];

//redirige sur la liste si le choix d'un patient n'est pas renseigne
if (!$_GET['GUID'])
{
  header('location: liste.php' );
  exit;
}
else //On recupere l'identifiant du patient envoye par l'URL
  $patient=$_GET['GUID'];

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

$sql_patient=$pdo->prepare("SELECT * FROM IndexNomPrenom WHERE FchGnrl_IDDos= ?");
$sql_patient->bindValue(1, $patient, PDO::PARAM_STR);
$sql_patient->execute();
$ligne_patient=$sql_patient->fetch(PDO::FETCH_ASSOC);
$sql_patient->closeCursor();

$nom=$ligne_patient["FchGnrl_NomDos"];
$prenom=$ligne_patient["FchGnrl_Prenom"];
$date=date('Y-m-d'); //la date courante 
include("inc/header.php");
?>
    <title>
       <?php echo $nom.' '.$prenom ?> Utilisateur <?php echo $_SESSION['login'] ?>
    </title>

  <frameset rows="40,*" border="0">
    <frame src="topframe.php" name="top" id="top" title="menu horizontal">
    <frameset cols="20%, 80%">
      <frame src="patient.php?GUID=<?php echo $patient?>" name="patient" title="partie constante">
      <frame src="consultation.php?numeroID=<?php echo $patient?>&amp;date=<?php echo $date?>&amp;edition=Aujourd'hui" name="droit_bas" title="partie variable">
    </frameset> 
  </frameset>
  <noframes>
  <body>
  Votre navigateur doit accepter les frames.
  </body>
  </noframes>
</html>