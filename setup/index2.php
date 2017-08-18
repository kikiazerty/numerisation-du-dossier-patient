<?php
@session_start();
//------------------------------------------------------------------------------------------------------
	error_reporting(E_ALL ^ E_NOTICE);
	@set_time_limit(0);
//------------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------
	//if ($_SESSION["serveur"] && $_SESSION["login"] && $_SESSION["password"]) {
		$serveur = $_SESSION["serveur"];
		$login = $_SESSION["login"];
		$password = $_SESSION["password"];
	//}
//------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <title>
      Installation du logiciel MedWebTux sur le serveur web : <?php echo $_SERVER["HTTP_HOST"] ?>
    </title>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <meta http-equiv="Content-Language" content="fr" />
    <link rel="shortcut icon" type="image/x-icon" href="images/araignee.ico" />
    <link rel="stylesheet" href="../css/style.css" />
  </head>
  <body>
<?php
	include_once("header.php");
?>
<!-- Debut du formulaire communication -->
  <div class="installation">
    <h3>Communication avec les bases de donn&eacute;es de Medintux</h3>
<?php
	@include_once("../connexion_serveur.php");

	$dbh = new PDO("mysql:host=$serveur", $login, $password, array(PDO::ATTR_PERSISTENT => true));
	$dbs = $dbh->query( 'SHOW DATABASES' );
/*
	while( ( $db = $dbs->fetchColumn( 0 ) ) !== false )
	{
    		echo $db.'<br>';
	}
*/	

	//databases qui doivent exister
	$database = array("CCAMTest", "DrTuxTest", "comptabilite");
	$cpt_table = array("68", "44 *", "14");
	$temps = array("2mn Environ", "15sec Environ", "5sec Environ");
	$i = 0;
?>
	<table>
	  <tr>
	   <td>

	<table>
	  <tr>
	   <th class="fond_th">Bases de donn&eacute;es</th>
	   <th class="fond_th">Temps</th>
	   <th class="fond_th">Bases</th>
	   <th class="fond_th">Tables</th>
	</tr>

<?php
	while ($i < 3) {
	
			echo "<tr>
		   	<td class=\"inst_td\"><b>".$database[$i]."&nbsp;(".$cpt_table[$i].")</b></td>
			<td class=\"inst_td\"><b>".$temps[$i]."</b></td>
			<td class=\"inst_td\">
			<form method=\"post\" action=\"\">
			<div>
			<input type=\"hidden\" name=\"base\" value=\"$database[$i]\" />
			<input type=\"hidden\" name=\"serveur\" value=\"$serveur\" />
			<input type=\"hidden\" name=\"login\" value=\"$login\" />
			<input type=\"hidden\" name=\"password\" value=\"$password\" />
			<input type=\"submit\" name=\"creer\" value=\"Cr&eacute;er la base ".$database[$i]."\" />
			</div>
			</form>
			</td>

			<td class=\"inst_td\">
			<form method=\"post\" action=\"\">
			<div>
			<input type=\"hidden\" name=\"table_base\" value=\"$database[$i]\" />
			<input type=\"hidden\" name=\"serveur\" value=\"$serveur\" />
			<input type=\"hidden\" name=\"login\" value=\"$login\" />
			<input type=\"hidden\" name=\"password\" value=\"$password\" />
			<input type=\"submit\" name=\"creer\" value=\"Cr&eacute;er les tables ".$database[$i]."\" />
			</div>
			</form>
			</td>
			</tr>";
		
			$i = $i + 1; 
	}
?>
	</table>
	</td>

	<td valign="top">
	  <table>
	  <tr>
	    <th class="fond_th">Bases Medintux</th>
	  </tr>
<?php
	$j = 0;

	while( ( $db = $dbs->fetchColumn( 0 ) ) !== false ) {
	
		if ($db == "ccamtest" || $db == "CCAMTest" || $db == "drtuxtest" || $db == "DrTuxTest" || $db == "comptabilite") {
			
			//calcul le nombre de tables dans chaque base
			$conn = new PDO("mysql:host=$serveur", $login, $password, array(PDO::ATTR_PERSISTENT => true));			
			$result = $conn->prepare("SHOW TABLES FROM $db");
			$result->execute();
			$nbr = $result->rowCount();	
			
				if (!$nbr) {
					echo "<tr>
			   		<td style=\"background:pink; height:25px;\" class=\"inst_td\">".$db."&nbsp;(".$nbr.")</td>
					</tr>";
				}
				else {
					echo "<tr>
				   	<td style=\"background:#99ff69; height:25px;\" class=\"inst_td\">".$db."&nbsp;(".$nbr.")</td>
					</tr>";
				}
			$j = $j + 1;
		 }
	}
?>
	</table>
	</td>
	</tr>
	</table>
    </div>
<!-- Fin du formulaire communication -->

<?php

//------------------------------------ creation des bases de donnees ----------------------------------------
if (isset($_POST["base"])) {
$base = $_POST["base"];

	//connexion a la db et creation de la base.
	 $dbh = new PDO("mysql:host=$serveur", $login, $password, array(PDO::ATTR_PERSISTENT => true));
	 $create_base = $dbh->query("CREATE DATABASE $base"); 

	 echo "<meta http-equiv=\"refresh\" content=\"0;url= $_SERVER[HTTP_REFERER]\" />";
}
//------------------------------------ creation des bases de donnees ----------------------------------------

//------------------------------- creation des tables des bases de donnees ----------------------------------
if (isset($_POST["table_base"])) {
$table_base = $_POST["table_base"];

	// CONNEXION A MYSQL
	$dbh = new PDO("mysql:host=$serveur;dbname=$table_base", $login, $password, array(PDO::ATTR_PERSISTENT => true));

	if ($table_base == "CCAMTest") {
		// ON RECUPERE LE CONTENU DU FICHIER SQL ET ON L'INTEGRE DANS LA BDD
		$fichier_sql = "sql/$table_base.sql";
		$handle = fopen($fichier_sql,"r");
		$contenu = fread($handle, filesize($fichier_sql));
		$contenu = str_replace(");",")@@",$contenu);
		$contenu = explode("@@",$contenu);
		fclose ($handle);
		foreach($contenu as $ligne)		
		{ 
			$dbh->query($ligne); 
		}
	}
	else {
		// ON RECUPERE LE CONTENU DU FICHIER SQL ET ON L'INTEGRE DANS LA BDD
		$fichier_sql = "sql/$table_base.sql";
		$handle = fopen($fichier_sql,"r");
		$contenu = fread($handle, filesize($fichier_sql));
		$contenu = utf8_decode(str_replace(");",")@@",$contenu));
		$contenu = explode("@@",$contenu);
		fclose ($handle);
		foreach($contenu as $ligne)		
		{ 
			$dbh->query($ligne); 
		}
	}

	echo "<meta http-equiv=\"refresh\" content=\"0;url=$_SERVER[HTTP_REFERER]\" />";
}
//------------------------------- creation des tables des bases de donnees -----------------------------------

//------------------------------------------ Precedent Suivant -----------------------------------------------
if ($j >= 3) {
	echo "<div class=\"precsuiv\">";
	echo "<table>";
	echo "<tr><td>";
	echo "<form method=\"post\" action=\"index3.php\">
		<div>
		<input type=\"hidden\" name=\"serveur\" value=\"$serveur\" />
		<input type=\"hidden\" name=\"login\" value=\"$login\" />
		<input type=\"hidden\" name=\"password\" value=\"$password\" />
		<input type=\"submit\" name=\"creer\" value=\"Suivant\" />
		</div>
		</form>";
	echo "</td></tr>";
	echo "</table>";
	echo "</div>";
}
//------------------------------------------ Precedent Suivant -----------------------------------------------


?>
<div class="erreur">
<b>INFORMATIONS</b> <br /><br />
<b>Afin d'&eacute;viter de ralentir l'installation de MebWebTux nous vous conseillons d'utiliser l'outil de gestion des bases de donn&eacute;es inclus dans l'application juste apr&egrave;s cette installation.</b> <br /><br />

<b>T&eacute;l&eacute;charger les fichiers ci-dessous en <a href="http://enterprise.projetym.net/sql/sql_mwt.zip"> cliquant ici </a> </b><br /><br />

<!--<b>(*) Les fichiers sql permettant de cr&eacute;er les tables et d'ins&eacute;rer les donn&eacute;es dans la base de DatasempTest.</b>
<br />
-->

<b> (*) Le fichier sql permettant d'ins&eacute;rer les donn&eacute;es de la table codes postaux dans la base de DrTuxTest.</b>
</div>
  </body>
</html>
