<?php
session_start() ;
if ( !isset( $_SESSION['login'] ) )
{
  header('location: index.php?page=index' );
  exit;
}

include("config.php");
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
/*
$db=mysqli_connect($host, $loginbase, $pwd);
$codage=mysqli_query($db,"SET NAMES 'UTF8'");

if(!$db)
{
  print "Erreur de connexion &agrave; $host<br />";
  exit;
}

// on choisit la bonne base

if (!mysqli_select_db($db,$base))
{
  print "Erreur ".mysqli_error($db)."<br />";
  mysqli_close($db);
  exit;
}
*/
if (isset($_GET['city']))
	$city=$_GET['city'];
else
  $city="";
include("inc/header.php");
$sql_select_city=$pdo->prepare('SELECT code_postal FROM codes_postaux WHERE ville= ?');
$sql_select_city->bindValue(1, $city, PDO::PARAM_STR);
$sql_select_city->execute();
/*
$sql_select_city="SELECT code_postal FROM codes_postaux WHERE ville='$city'";
$resultat_select_city=mysqli_query($db,$sql_select_city);*/
?>
    <title>
      MedWebTux - Code postal
    </title>

<script type="text/javascript">
<!--
function choisir(zipcode)
// on affecte la valeur (.value) dans :
// window.opener : la fenêtre appelante (celle qui a fait la demande)
// .document : son contenu
// .forms_x : le formulaire nomme
// .le champ 
// les valeurs attribuees proviennent du formulaire plus bas
//tester si fenêtre deja ouverte

{ 
window.opener.document.getElementById('form_general').CP.value = zipcode;
// on se ferme
self.close(); 
}
-->
    </script>
    <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen" />

  </head>
	
  <body style="font-size:<?php echo $fontsize; ?>pt">
    <div class="conteneur">
   <div class="groupe">
      <h1>
	MedWebTux - Code postal
      </h1>
      <h2>
	<?php echo $city ?>
      </h2>
      <form action="city.php" method="get">
	<div>
<?php	
$i=0;
while ($liste_select_city=$sql_select_city->fetch(PDO::FETCH_ASSOC))
{
  $i++;
  echo "
	  <input type=\"radio\" name=\"city[]\" id=\"city_".$i."\" value=\"".$liste_select_city['code_postal']."\" onclick=\"choisir(this.value)\"/><label for=\"city_".$i."\" >".$liste_select_city['code_postal']."</label><br />";
}
$sql_select_city->closeCursor();

?>
	</div>
      </form>
    </div>
<?php
include("inc/footer.php");
?>
