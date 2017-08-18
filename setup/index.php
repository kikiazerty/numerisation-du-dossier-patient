<?php
@session_start();
//------------------------------------------------------------------------------------------------------
	error_reporting(E_ALL ^ E_NOTICE);
	@set_time_limit(0);
//------------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------
	$serveur = $_POST["serveur"];
	$login = $_POST["login"];
	$password = $_POST["password"];
//------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <title>
      Installation du logiciel MedWebTux sur le serveur web : <?php echo $_SERVER["HTTP_HOST"] ?>
    </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="fr" />
    <link rel="shortcut icon" type="image/x-icon" href="images/araignee.ico" />
    <link rel="stylesheet" href="../css/style.css" />
  </head>
  <body>
<?php
	include_once("header.php");
?>
<!-- Debut du formulaire connexion -->
  <div class="installation">
    <h3><?php echo "MedWebTux doit se connecter au serveur MySQL ".$_SERVER['HTTP_HOST']."";  ?></h3>
    <div class="tableau_installation">
    <table>
    <tr>
    <th>
      <form action="index.php" method="post">
	<table>
	  <tr>
	    <th class="fond_th">
	      Serveur Mysql
	    </th>
	    <th class="fond_th">
	      Nom d'utilisateur
	    </th>
	     <th class="fond_th">
		Mot de passe
	      </th>
	  </tr>
	  <tr>
	    <td class="inst_td">
	      <input type="text" size="30" name="serveur" value="<?php echo $serveur; ?>" />
	    </td>
	    <td class="inst_td">
	      <input type="text" size="30" name="login" value="<?php echo $login; ?>" />
	    </td>
	    <td class="inst_td">
		<input type = "password" size="30" name="password" value="<?php echo $password; ?>" />
	      </td>
	    </tr>
	    <tr>
	       <td colspan="3">&nbsp;</td>
	    </tr>
	      <tr>
		<th colspan="3">
		  <input type = "submit" value="Connexion" />
		</th>
	      </tr>
	    </table>
	  </form>
	</th>
      </tr>
    </table>
   </div>
  </div>
<!-- Fin du formulaire connexion -->

<?php

if (isset($_POST["serveur"]) && isset($_POST["login"])) { // && isset($_POST["password"])) {
	
		if (!$serveur || !$login) { // || !$password) {
			echo "
		    <div class=\"erreur\">";
			echo "&nbsp;&nbsp; La saisie des champs n'est pas compl&egrave;te !&nbsp;&nbsp;<img src='images/attention.gif' alt='image attention' />";
			echo "
		    </div>
		  </body>
		</html>
		";
			//exit;
	 	}
	 else {
//-----------------------------------------------------------------------------------------------------------

//---------------------------------- debut creation du fichier connexion ------------------------------------
$f = '../connexion_serveur.php';
$text = "<?php
//connexion au serveur mysql
  \$host = '$serveur';
  \$loginbase = '$login';
  \$pwd = '$password';
?>
";	

//connexion au serveur de base de donnee en question
	try
    	{
     		$dbo = new PDO("mysql:host=$serveur", $login, $password, array(PDO::ATTR_PERSISTENT => true));
    	}
	catch (Exception $error)
	{
     		die('Erreur : ' . $error->getMessage());
    	}

if ($dbo) {

	$handle = @fopen("$f","w");
	// regarde si le fichier est accessible en écriture
	if (is_writable($f)) 
	{
	// Ecriture
	    if (fwrite($handle, $text) === FALSE) // 
	   {
	      echo 'Impossible d\'&eacute;crire dans le fichier '.$f.' Faire un chmod 666 sur ce fichier.<br />';
	      exit;
	   }
	    //echo 'Ecriture terminé';
		$_SESSION["serveur"] = $serveur;
		$_SESSION["login"] = $login;
		$_SESSION["password"] = $password;

	    fclose($handle);   
	}
	else 
	{
	      echo "<div class=\"erreur\">";
	      echo "Impossible d'&eacute;crire dans le fichier <b>".$f."</b> Faire un chmod 666 sur ce fichier.&nbsp;&nbsp;<img src='images/attention.gif' alt='image attention' /><br />";
	      exit;
	}
}

//---------------------------------- fin creation du fichier connexion --------------------------------------

//------------------------------------------------------------------------------------------------------------
    if (!$dbo) {
	echo "
    	<div class=\"erreur\">";
	echo "&nbsp;&nbsp; La tentative de connexion au serveur MySQL de ".$_SERVER["HTTP_HOST"]." a &eacute;chou&eacute;.&nbsp;&nbsp;<img src='images/attention.gif' alt='image attention' />
    </div>";
    }
    else {
	echo "
    	<div class=\"notice\">";
	echo "&nbsp;&nbsp; Connexion &eacute;tablie vers le serveur MySQL de ".$_SERVER["HTTP_HOST"].".&nbsp;&nbsp;<img src='images/ok.gif' alt='image ok' />
</div>";
	echo "<meta http-equiv=\"refresh\" content=\"1;url=index2.php\" />";
    }
//------------------------------------------------------------------------------------------------------------
	}
}
	//recupere les valeurs du serveur php
	$memory_limit = (int) ini_get('memory_limit');
	$post_max_size = (int) ini_get('post_max_size');
	$upload_max_filesize = (int) ini_get('upload_max_filesize');

?>
<div class="erreur">
<b>IMPORTANT !</b>
<ul>
<li><b>Connexion par d&eacute;faut de l'utilisateur de l'application MedWebTux : admin / admin </b></li>
<li><b>Sous Linux Ubuntu et &eacute;quivalents, ne pas oublier de r&eacute;aliser la commande suivante : sudo chown -R www-data MedWebTux</b></li>
</ul>
</div>
<div class="installation">
<ul>
<?php
if ($memory_limit < 128) {
?>
<li>
<div class="erreur">

<b>Il est conseill&eacute; de passer la directive memory_limit à 128M</b>

memory_limit	<?php echo $memory_limit; ?>M
</div></li>
<?php
}
if ($post_max_size < 100) {
?>
<li>
<div class="erreur">
<b>Il est conseill&eacute; de passer la directive post_max_size à 100M</b>
post_max_size	<?php echo $post_max_size; ?>M
</div>
</li>
<?php
}
if ($upload_max_filesize < 100) {
?>
<li>
<div class="erreur">
<b>Il est conseill&eacute; de passer la directive upload_max_filesize à 100M</b>
upload_max_filesize	<?php echo $upload_max_filesize; ?>M
</div>
</li>
<?php
}
?>
</ul>
</div>

  </body>
</html>
