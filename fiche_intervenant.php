<?php
session_start() ;
include("config.php");
//Si on n'est pas logue
if ( !isset( $_SESSION['login'] ) )
{
//On renvoie automatiquement sur la page de login
  header('location: index.php' );
  exit;
}
if (!$_GET['intervenant'])
{
  header('location: correspondant.php' );
  exit;
}//Si un identifiant de correspondant n'est pas donne par l'URL, on renvoie sur le choix des correspondants
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

$date=date('Y-m-d', date('U'));

$envoyer_date="";
$montrer_patients="";

if (isset($_GET['envoyer_date']))
  $envoyer_date=$_GET['envoyer_date'];

if (isset($_GET['montrer_patients']))
  $montrer_patients=$_GET['montrer_patients'];

//pour convertir les formats de dates
function local_to_iso($date,$date_format)
{
  $list_date=explode ("-",$date);
  if ($date_format=='fr')
  {
    $date=$list_date[2].'-'.$list_date[1].'-'.$list_date[0];
  }
  elseif ($date_format=='en')
    $date=$list_date[2].'-'.$list_date[0].'-'.$list_date[1];
  return $date;
}

function iso_to_local($date,$date_format)
{
  $list_date=explode ("-",$date);
  if ($date_format=='fr')
  {
  //on repasse en iso les dates qui arrivent en francais
    $date=$list_date[2].'-'.$list_date[1].'-'.$list_date[0];
  }
  elseif ($date_format=='en')
    $date=$list_date[1].'-'.$list_date[2].'-'.$list_date[0];
  return $date;
}

include("inc/header.php");

$tab_login=explode("::",$_SESSION['login']);
$user=$tab_login[0];
//$signataire=$tab_login[1];
$intervenant=$_GET['intervenant'];

$sql_intervenant=$pdo->prepare('SELECT * FROM Personnes WHERE ID_PrimKey = ?');
$sql_intervenant->bindValue(1, $intervenant, PDO::PARAM_STR);
$sql_intervenant->execute();
$ligne_intervenant=$sql_intervenant->fetch(PDO::FETCH_ASSOC);
$sql_intervenant->closeCursor();
//recherche droits du user connecte
$sql_user_droits=$pdo->prepare('SELECT * FROM Personnes WHERE Login = ?');
$sql_user_droits->bindValue(1, $user, PDO::PARAM_STR);
$sql_user_droits->execute();
$ligne_user_droits=$sql_user_droits->fetch(PDO::FETCH_ASSOC);
$sql_user_droits->closeCursor();
$guid_user=$ligne_user_droits['GUID'];

?>
    <title>
      Intervenant <?php echo $ligne_intervenant['Nom']." ".$ligne_intervenant['Prenom'] ?> - Utilisateur <?php echo $_SESSION['login']?>
    </title>
    <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen"/>
    <link rel="stylesheet" href="css/print.css" type="text/css" media="print"/>
    <!-- Ne pas afficher les colonnes techniques en mode impression -->
    <style type="text/css"  media="print">
	    td:nth-child(2) { display: none; }  
	    td:nth-child(3) { display: none; }  
	    td:nth-child(4) { display: none; }  
    </style>
    <script type="text/javascript" src="oXHR.js"></script>

    <script type="text/javascript">
//<![CDATA[
function donner_focus(chp)
{
  var valueRecherche = document.getElementById(chp).value; //on recupere la valeur
  document.getElementById(chp).value = ''; //on vide le champ
  document.getElementById(chp).focus(); //on donne le focus
  document.getElementById(chp).value =valueRecherche ; //on remet la chaine avant le focus
}
//]]>
    </script> 

    <script type="text/javascript">
//<![CDATA[
function request(callback) {
//AJAX pour verifier qu'un intervenant n'existe pas deja
  var xhr = getXMLHttpRequest();
  
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
      callback(xhr.responseText);
    }
  };
  var IdUser = encodeURIComponent(document.getElementById("login").value);
//on envoie a la page de verification le nom de login

  xhr.open("GET", "handlingData.php?User=" + IdUser, true);
  xhr.send(null);
}

function readData(sData) 
{
//fonction donnee en argument a request
  if (sData!="OK")
  {
    alert (sData); //on affiche la chaine envoyee par handlingData.php
    donner_focus('login'); //on revient sur la zone de saisie qui a échoué
  }
}
//]]>
    </script>
    
    <script type="text/javascript">
//<![CDATA[
function assistant_droits(guid_sign,guid_friend,mode) 
{
  window.open('assistant_droits.php?guid_sign='+guid_sign+'&guid_friend='+guid_friend+'&mode='+mode,'Déterminer les droits','width=800,height=550,top=50,left=50,toolbar=no,scrollbars=yes,resizable=yes,location=no'); 
}
//]]>
    </script>

<script type="text/javascript">
<!-- 
function verif_vide(pwd)
{
  if (pwd == "")
  { 
    alert("Le champ n'est pas rempli\nMettez un mot de passe");
    return false;
  }
  return true;
}
-->
</script>

<?php
include 'calendar_javascript.php';
?>

  </head>
  <body style="font-size:<?php echo $fontsize; ?>pt" >
    <div class='noScreen'>
<!-- en-tete pour impression -->
<?php	
//On utilise l'en-tete de l'utilisateur autorisant
  $sql_sign=$pdo->prepare('SELECT * FROM Personnes WHERE Login = ?'); //le signataire
  $sql_sign->bindValue(1, $tab_login[1], PDO::PARAM_STR);
  $sql_sign->execute();
  $ligne_sign=$sql_sign->fetch(PDO::FETCH_ASSOC);
  $sql_sign->closeCursor();

echo "
	<strong>".$ligne_sign["Titre"]." ".$ligne_sign["Nom"]." ".$ligne_sign["Prenom"]."</strong><br />";
echo '
	'.$ligne_sign["Adresse"]."<br />";
echo '
	'.$ligne_sign["CodePostal"]." ".$ligne_sign["Ville"]."<br />";
if ($ligne_sign["Convention"])
  echo '
	'.$ligne_sign["Convention"]."<br />";
echo '
	'.$ligne_sign["Qualite"]."<br />";
if ($ligne_sign["NumOrdre"])
  echo "
	  <strong>Num&eacute;ro d'ordre</strong>&nbsp;: " .$ligne_sign["NumOrdre"]."<br />";
if ($ligne_sign["NumRPPS"])
  echo "
	  <strong>Num&eacute;ro RPPS</strong>&nbsp;: " .$ligne_sign["NumRPPS"]."<br />";
if ($ligne_sign["Tel_1"])
  echo "
	  <strong>T&eacute;l</strong>&nbsp;: " .$ligne_sign["Tel_1"]."<br />";
/*if ($ligne_sign["Tel_2"])
	echo "<strong>T&eacute;l&eacute;phone 2</strong> : " .$ligne_sign["Tel_2"]."<br />";
if ($ligne_sign["Tel_3"])
	echo "<strong>T&eacute;l&eacute;phone 3</strong> : " .$ligne_sign["Tel_3"]."<br />";
*/
if ($ligne_sign["EMail"])
  echo "
	  <strong>E-Mail</strong>&nbsp;: " .$ligne_sign["EMail"]."<br />";
 //fin en-tete medecin
?>
	</div><!-- fin noscreen en tete -->
<?php	
  // insertion du menu d'en-tete et du formulaire de recherche	
$anchor='Fiche_d_intervenant';
include("inc/menu-horiz.php");
?>
    <div class="groupe">
	<h1 class="noPrint" >
	  MedWebTux - Intervenant
	</h1>

<?php
//Si une date de debut a ete demandee, on la recupere et on la formate 
if (isset($_GET['debut_court'])) //format local
{
  $debut_court=$_GET['debut_court']; //pour utilisation dans les zones de saisie
  $debut=local_to_iso($debut_court,$date_format)." 00:00:00";
}
else//Sinon, on prend la date du jour
{
  $debut_court=iso_to_local(date('Y-m-d', date('U')),$date_format);
  $debut=date('Y-m-d', date('U'))." 00:00:00"; //pour requetes sql
}
//Si une date de fin a ete demandee, on la recupere et on la formate 
if (isset($_GET['fin_court'])) //local
{
  $fin_court=$_GET['fin_court'];
  $fin=local_to_iso($fin_court,$date_format)." 23:59:59";
}
//Sinon, on prend la date du jour
else 
{
  $fin_court=iso_to_local(date('Y-m-d', date('U')),$date_format);
  $fin=date('Y-m-d', date('U'))." 23:59:59";
}
?>

<?php
$qrcode_string="BEGIN:VCARD\nVERSION:2.1\n";
/*  BEGIN:VCARD
  VERSION:2.1
  N;CHARSET=UTF-8:'.$Nom.';'.$Prenom.';;'.$Titre.'
  FN;CHARSET=UTF-8:'.$Prenom.' '.$Nom.'
  TITLE;CHARSET=UTF-8:'.$Qualite.'
  ADR;WORK;CHARSET=UTF-8:;'.$Adresse.';'.$Ville.';;'.$CodePostal.';
  TEL;WORK:'.$Tel_1.'
  EMAIL;PREF;INTERNET:'.$EMail.'
  NOTE;CHARSET=UTF-8:'.$Note.'
  END:VCARD';
*/
?>
	  <fieldset class="fieldset_intervenant"  style="float:left;">
	    <legend>
	      Fiche intervenant
	    </legend>
<?php
echo "
	    <strong>".$ligne_intervenant['Titre']." ".stripslashes($ligne_intervenant['Nom'])." ".stripslashes($ligne_intervenant['Prenom'])."</strong><br />";
//correspondant.php?Qualite=ALCOOLOGUE&critere_recherche=Nom&cle=&intervenant_user[]=users&intervenant_user[]=no_users&envoyer=Chercher
echo '
	    <a href="correspondant.php?Qualite='.$ligne_intervenant['Qualite'].'&critere_recherche=Nom&cle=&intervenant_user[]=users&intervenant_user[]=no_users&envoyer=Chercher">'.$ligne_intervenant['Qualite']."</a><br />";
$qrcode_string.='N;CHARSET=UTF-8:'.$ligne_intervenant['Nom'].';'.$ligne_intervenant['Prenom'].';;'.$ligne_intervenant['Titre']."\nFN;CHARSET=UTF-8:".$ligne_intervenant['Prenom'].' '.$ligne_intervenant['Nom']."\nTITLE;CHARSET=UTF-8:".$ligne_intervenant['Qualite']."\n";

if ($ligne_intervenant['NumOrdre'])
{
  echo "
	    <strong>Ordre : </strong>".$ligne_intervenant['NumOrdre']."<br />";
}
if ($ligne_intervenant['NumRPPS'])
{
  echo "
	    <strong>RPPS : </strong>".$ligne_intervenant['NumRPPS']."<br />";
}
$adr=preg_replace("`\n`","<br />",$ligne_intervenant['Adresse']);
echo '
	    '.$adr."
	    <br />".$ligne_intervenant['CodePostal']." ".$ligne_intervenant['Ville']."
	    <br />";
$qrcode_string.='ADR;WORK;CHARSET=UTF-8:;'.$adr.';'.$ligne_intervenant['Ville'].';;'.$ligne_intervenant['CodePostal']."\n";

if ($ligne_intervenant['Tel_1'])
{
  echo "
	    <strong>T&eacute;l&eacute;phone</strong>";
  if ($ligne_intervenant['Tel_Type1'])
  {
    echo " (".$ligne_intervenant['Tel_Type1'].")";
  }
  echo " : ".$ligne_intervenant['Tel_1'];
  $qrcode_string.='TEL;WORK:'.$ligne_intervenant['Tel_1']."\n";
  if ($ligne_intervenant['Tel_Abr_1'])
	  echo " (".$ligne_intervenant['Tel_Abr_1'].")";
echo "
	      <br />";
}
if ($ligne_intervenant['Tel_2'])
{
  echo "
	      <strong>T&eacute;l&eacute;phone</strong>";
  if ($ligne_intervenant['Tel_Type2'])
  {
    echo " (".$ligne_intervenant['Tel_Type2'].")";
  }
  echo " : ".$ligne_intervenant['Tel_2'];
  if ($ligne_intervenant['Tel_Abr_2'])
    echo " (".$ligne_intervenant['Tel_Abr_2'].")";
  echo "
	      <br />";
}
if ($ligne_intervenant['Tel_3'])
{
  echo "
	      <strong>T&eacute;l&eacute;phone </strong>";
  if ($ligne_intervenant['Tel_Type3'])
  {
    echo " (".$ligne_intervenant['Tel_Type3'].")";
  }
  echo " : ".$ligne_intervenant['Tel_3'];
  if ($ligne_intervenant['Tel_Abr_3'])
    echo " (".$ligne_intervenant['Tel_Abr_3'].")";
  echo "
	      <br />";
}
if ($ligne_intervenant['EMail'])
{
  echo "
	      <strong>E-Mail</strong>&nbsp;: <a href=\"mailto:".$ligne_intervenant['EMail']."\">".$ligne_intervenant['EMail']."</a>
	      <br />";
  $qrcode_string.='EMAIL;PREF;INTERNET:'.$ligne_intervenant['EMail']."\n";
}
if ($ligne_intervenant['Note'])
{
  echo "
	      <strong>Notes</strong>&nbsp;: <br />".preg_replace("`\n`i","<br />",$ligne_intervenant['Note'])."
	      <br />";
  $qrcode_string.='NOTE;CHARSET=UTF-8:'.$ligne_intervenant['Note']."\n";
}
if ($ligne_intervenant['Sexe'])
{
?>
	      <strong>Sexe&nbsp;:</strong> <?php echo $ligne_intervenant['Sexe'] ?>
	      <br />
<?php
}
if ($ligne_intervenant['Cher'])
{
?>
	      <strong>Politesse&nbsp;: </strong><?php echo $ligne_intervenant['Cher'] ?>
	      <br />
<?php
}
$qrcode_string.='END:VCARD';
$qrcode_string=urlencode($qrcode_string);
?>
	      <img src="phpqrcodeimg.php?qrcode_string=<?php echo $qrcode_string;?>" alt="qrcode" width="125" title="Scannez cette image de QR-Code avec votre Smartphone pour ajouter cette fiche à vos contacts" />
	      <form action="formulaire_correspondant.php"  method="get"> <!--Bouton de modification de fiche--> 

		<div class='noPrint'>
		  <input name="ID_corresp" type="hidden" value="<?php echo $intervenant?>" />
		  <br />
		  <input name="envoyer" type="submit" value="Modifier" />
<?php
//ne pas supprimer une fiche intervenant si on n'est pas admin
if (stripos($ligne_user_droits['Droits'],"-adm-"))
{
?>
		  <input name="envoyer" type="submit" value="Supprimer" />
<?php
}
?>
		</div>
	      </form>
	    </fieldset>
	      <form action="fiche_intervenant.php" method="get" style="float:left;" class="noPrint" >
		<fieldset class="fieldset_intervenant">
		  <legend>
		    Patients li&eacute;s
		  </legend>
		  <input name="intervenant" type="hidden" value="<?php echo $intervenant ?>" />
		  <input name="montrer_patients" type="submit" value="<?php if ($montrer_patients=='Montrer') echo 'Cacher'; else echo 'Montrer' /*bouton a bascule */?>" />
		</fieldset>
	      </form>
			
	 <!-- les dates -->
	      <form action="fiche_intervenant.php" method="get" id="form_recherche_date" style="float:left;"  class="noPrint">
		<fieldset class="fieldset_intervenant" title="Pour rechercher toutes les consultations de patients liés à cet intervenant">
		  <legend>
		    Période de consultations
		  </legend>
		  <input name="intervenant" type="hidden" value="<?php echo $intervenant; ?>" />
		  <table>
		    <tr>
		      <td>
			<label for="debut_court">D&eacute;but :</label> 
		      </td>
		      <td>		  
			<input name="debut_court" id="datepicker" type="text" value="<?php echo $debut_court;?>" size="10" maxlength="10" />
<!--
			<input type="image" src="pics/calendar.png" alt="calendrier" onclick="return getCalendar(document.forms['form_recherche_date'].debut_court);" />
-->
		      </td>
		    </tr>
		    <tr>
		      <td>
			<label for="fin_court">Fin :</label>
		      </td>
		      <td>
			<input name="fin_court" id="datepickeur" type="text" value="<?php echo $fin_court;?>" size="10" maxlength="10" />
<!--
			<input type="image" src="pics/calendar.png" alt="calendrier" onclick="return getCalendar(document.forms['form_recherche_date'].fin_court);" />
-->
		      </td>
		    </tr>
		    <tr>
		      <td colspan="2"><input name="envoyer_date" type="submit" value="Chercher" />
		      </td>
		    </tr>
		  </table>
		</fieldset>
	      </form>
<?php
//affichage des champs de login si administrateur ou utilisateur=fiche affichée

if ($ligne_user_droits['Login']==$ligne_intervenant['Login'] OR stripos($ligne_user_droits['Droits'],"-adm-"))
{
?>
	      <form action="valid_user.php" id="form_user" method="get" style="float:left;" class="noPrint"  >
		<fieldset class="fieldset_intervenant">
		  <legend>
		    Intervenant en tant qu'utilisateur
		  </legend>
		  <div id="user">
		    <div>
		      <input type="hidden" value="<?php echo $intervenant ?>" name="intervenant"  />
		      <table>
			<tr>
			  <td>
			    <label for="login">Login : </label>
			  </td>
			  <td>
			    <input type="text" id="login" name="login" value="<?php echo $ligne_intervenant['Login'] ?>"  <?php if ($ligne_intervenant['Login']) echo 'readonly="readonly"'?> size='10' onchange="request(readData);" />
			    <span id="loader" style="display: none;">
			      <img src="images/loader.gif" alt="loading" />
			    </span>
			  </td>
			</tr>
			<tr>
			  <td>
			    <label for="pwd">Mot de passe : </label>
			  </td>
			  <td>
			    <input type="password" id="pwd" name="pwd" value="" maxlength="8" size="8" title="8 caractères maximum" />
			  </td>
			</tr>
		      </table>
		    </div>
<div <?php if (!stripos($ligne_user_droits['Droits'],"-adm-")) echo  "class='noScreen'" ?>>
		    <strong>Droits</strong>
		    <select name="droits[]" multiple="multiple" size="5" >
		      <option value="nbv" <?php if (preg_match("`nbv`",$ligne_intervenant['Droits'])) echo "selected='selected'"; ?>>Ne peut pas débloquer les bases</option>
		      <option value="idd" <?php if (preg_match("`idd`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Ne peut pas effacer le dossier patient</option>
		      <option value="ata" <?php if (preg_match("`ata`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Accéder aux antécédents</option>
		      <option value="adm" <?php if (preg_match("`adm`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Administrer</option>
		      <option value="bic" <?php if (preg_match("`bic`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Créer biologie</option>
		      <option value="clc" <?php if (preg_match("`clc`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Créer classeur</option>
		      <option value="img" <?php if (preg_match("`img`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Crer imagerie</option>
		      <option value="gln" <?php if (preg_match("`gln`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Créer dossiers dans le glossaire</option>
		      <option value="cec" <?php if (preg_match("`cec`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Créer certificats</option>
		      <option value="coc" <?php if (preg_match("`coc`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Créer courriers</option>
		      <option value="doc" <?php if (preg_match("`doc`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Créer des documents</option>
		      <option value="iec" <?php if (preg_match("`iec`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Créer documents dans l'identification</option>
		      <option value="obc" <?php if (preg_match("`obc`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Créer observations</option>
		      <option value="orc" <?php if (preg_match("`orc`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Créer prescriptions</option>
		      <option value="etc" <?php if (preg_match("`etc`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Créer et effacer les antécédents</option>
		      <option value="idc" <?php if (preg_match("`idc`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Créer et modifier l'identité</option>
		      <option value="plc" <?php if (preg_match("`plc`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Créer un patient</option>
		      <option value="gld" <?php if (preg_match("`gld`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Déplacer le glossaire</option>
		      <option value="nog" <?php if (preg_match("`nog`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Disposer du nomadisme</option>
		      <option value="gls" <?php if (preg_match("`gls`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Effacer des dossiers du glossaire</option>
		      <option value="agm" <?php if (preg_match("`agm`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Gérer un agenda multiple</option>
		      <option value="bip" <?php if (preg_match("`bip`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Imprimer biologie</option>
		      <option value="clp" <?php if (preg_match("`clp`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Imprimer classeurs</option>
		      <option value="imp" <?php if (preg_match("`imp`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Imprimer imagerie</option>
		      <option value="cep" <?php if (preg_match("`cep`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Imprimer certificats</option>
		      <option value="cop" <?php if (preg_match("`cop`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Imprimer courriers</option>
		      <option value="dop" <?php if (preg_match("`dop`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Imprimer documents</option>
		      <option value="iep" <?php if (preg_match("`iep`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Imprimer documents de l'identification</option>
		      <option value="obp" <?php if (preg_match("`obp`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Imprimer observations</option>
		      <option value="orp" <?php if (preg_match("`orp`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Imprimer prescriptions</option>
		      <option value="plp" <?php if (preg_match("`plp`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Imprimer un patient</option>
		      <option value="idg" <?php if (preg_match("`idg`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Lancer un dossier patient</option>
		      <option value="cem" <?php if (preg_match("`cem`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Modifier les certificats</option>
		      <option value="com" <?php if (preg_match("`com`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Modifier les courriers</option>
		      <option value="iem" <?php if (preg_match("`iem`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Modifier documents de l'identification</option>
		      <option value="orm" <?php if (preg_match("`orm`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Modifier les prescriptions</option>
		      <option value="glc" <?php if (preg_match("`glc`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Modifier le glossaire</option>
		      <option value="atc" <?php if (preg_match("`atc`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Modifier les antécédents</option>
		      <option value="tvc" <?php if (preg_match("`tvc`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Modifier les tableaux variables</option>
		      <option value="ttc" <?php if (preg_match("`ttc`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Modifier le traitement de fond</option>
		      <option value="agc" <?php if (preg_match("`agc`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Prendre des rendez-vous</option>
		      <option value="cer" <?php if (preg_match("`cer`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Renouveler un certificat</option>
		      <option value="cor" <?php if (preg_match("`cor`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Renouveler le courrier</option>
		      <option value="orr" <?php if (preg_match("`orr`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Renouveler une prescription</option>
		      <option value="adr" <?php if (preg_match("`adr`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Résoudre les doublons</option>
		      <option value="fse" <?php if (preg_match("`fse`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Saisir une FSE</option>
		      <option value="rgl" <?php if (preg_match("`rgl`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Saisir un règlement</option>
		      <option value="ccc" <?php if (preg_match("`ccc`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Utiliser la base CCAM</option>
		      <option value="bmc" <?php if (preg_match("`bmc`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Utiliser la base médicamenteuse</option>
		      <option value="cic" <?php if (preg_match("`cic`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Utiliser la CIM 10</option>
		      <option value="lac" <?php if (preg_match("`lac`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Utiliser le labo</option>
		      <option value="med" <?php if (preg_match("`med`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Utiliser MedinTux</option>
		      <option value="plv" <?php if (preg_match("`plv`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Voir la liste des patients</option>
		      <option value="biv" <?php if (preg_match("`biv`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Voir la biologie</option>
		      <option value="clv" <?php if (preg_match("`clv`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Voir les classeurs</option>
		      <option value="imv" <?php if (preg_match("`imv`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Voir l'imagerie</option>
		      <option value="atv" <?php if (preg_match("`atv`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Voir les antécédents</option>
		      <option value="cev" <?php if (preg_match("`cev`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Voir les certificats</option>
		      <option value="cov" <?php if (preg_match("`cov`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Voir les courriers</option>
		      <option value="dov" <?php if (preg_match("`dov`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Voir les documents</option>
		      <option value="iev" <?php if (preg_match("`iev`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Voir les documents de l'identification</option>
		      <option value="obv" <?php if (preg_match("`obv`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Voir les observations</option>
		      <option value="orv" <?php if (preg_match("`orv`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Voir les prescriptions</option>
		      <option value="agv" <?php if (preg_match("`agv`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Voir les rendez-vous</option>
		      <option value="tvv" <?php if (preg_match("`tvv`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Voir les tableaux variables</option>
		      <option value="sld" <?php if (preg_match("`sld`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Voir l'état du solde</option>
		      <option value="ttv" <?php if (preg_match("`ttv`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Voir le traitement de fond</option>
		      <option value="idv" <?php if (preg_match("`idv`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Voir l'identité</option>
		      <option value="sgn" <?php if (preg_match("`sgn`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Utilisateur signataire</option>
  <!-- 		    droits pour les rubriques arbitraires -->
<?php
/*$rubrique_perso[] = "Observation|ob|20030000";
$rubrique_perso[] = "Prescription libre|or|20020200";
$rubrique_perso[] = "Ordonnance structurée|or|20020100";
$rubrique_perso[] = "Terrain|tv|20060000";
$rubrique_perso[] = "Document|do|20080000";
$rubrique_perso[] = "Certificat|ce|20020300";
$rubrique_perso[] = "Courrier|co|20020500";
$rubrique_perso[] = "Notes avancées|ie|20090000";
$rubrique_perso[] = "Classeur|cl|20050000";
//les rubriques perso
$rubrique_perso[] = "Alertes|al|90010000";
*/
  foreach ($rubrique_perso AS $this_rubrique_perso)
  {
    if (!preg_match('`20030000`',$this_rubrique_perso) AND !preg_match('`20020200`',$this_rubrique_perso) AND !preg_match('`20020100`',$this_rubrique_perso) AND !preg_match('`20060000`',$this_rubrique_perso) AND !preg_match('`20080000`',$this_rubrique_perso) AND !preg_match('`20020300`',$this_rubrique_perso) AND !preg_match('`20020500`',$this_rubrique_perso) AND !preg_match('`20090000`',$this_rubrique_perso) AND !preg_match('`20050000`',$this_rubrique_perso) )
    {
      $tableau_rubrique=explode('|',$this_rubrique_perso);
      $see=$tableau_rubrique[1].'v';
      $create=$tableau_rubrique[1].'c';
      $print=$tableau_rubrique[1].'p';
?>
		      <option value="<?php echo $see ?>" <?php if (preg_match("`$see`",$ligne_intervenant['Droits'])) echo "selected='selected'" ; ?>>Peut voir la rubrique <?php echo $tableau_rubrique[0] ?></option>
		      <option value="<?php echo $create ?>" <?php if (preg_match("`$create`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Peut écrire dans la rubrique <?php echo $tableau_rubrique[0] ?></option>
		      <option value="<?php echo $print ?>" <?php if (preg_match("`$print`",$ligne_intervenant['Droits'])) echo "selected='selected'";  ?>>Peut imprimer la rubrique <?php echo $tableau_rubrique[0] ?></option>
<?php
    }
  }
?>
		    </select>
</div>
		    <br />
		    <input type="submit" value="Valider" name="submit_user" id="submit_user" onclick="return verif_vide(this.form.pwd.value);"/>
		  </div>
		</fieldset>
	      </form>
<?php
  if ($ligne_intervenant['Login']) //pas besoin de droits délégués si intervenant pas utilisateur
  {
    if (preg_match('/adm/',$ligne_user_droits['Droits']) OR preg_match('/adm/',$droits) ) //l'utilisateur peut etre place comme delegue si la personne connectee est admin.
    {
?>
	      <form action="valid_user.php" id="form_delegue" method="get" style="float:left;"  class="noPrint" >
		<fieldset>
		  <legend>
		    Délégué de
		  </legend>
<?php
    }
    $sql_chercher_users=$pdo->prepare('SELECT * FROM Personnes WHERE Login != ""'); //le signataire
    $sql_chercher_users->execute();
    $ligne_chercher_all_users=$sql_chercher_users->fetchAll(PDO::FETCH_ASSOC);
    $sql_chercher_users->closeCursor();

    $sql_select_delegue=$pdo->prepare("SELECT * FROM Personnes INNER JOIN user_perms ON Personnes.GUID=user_perms.SignataireGUID WHERE user_perms.FriendUserGUID= ? ");
    $sql_select_delegue->bindValue(1, $ligne_intervenant['GUID'], PDO::PARAM_STR);
    $sql_select_delegue->execute();
    $ligne_select_all_delegue=$sql_select_delegue->fetchAll(PDO::FETCH_ASSOC);
    $sql_select_delegue->closeCursor();
  
    if (preg_match('/adm/',$ligne_user_droits['Droits']) OR preg_match('/adm/',$droits) ) //l'utilisateur peut etre place comme delegue de si la personne connectee est admin.
    {
?>
		  <input type="hidden" name="GUID_intervenant" value="<?php echo $ligne_intervenant['GUID'] ?>" />
		  <input type="submit" name="submit_delegue" value="Valider"/>
		  <table>
<?php
    foreach ($ligne_chercher_all_users AS $ligne_chercher_users)
    {
      $coche=0;
      $droits="";
      foreach ($ligne_select_all_delegue AS $ligne_select_delegue)
      {
	if ($ligne_select_delegue['SignataireGUID']==$ligne_chercher_users['GUID'])
	{
	  $coche=1;
	  $droits=$ligne_select_delegue['FriendUserDroits'];
	}
      }
      if ($ligne_chercher_users['Login'] !=$ligne_intervenant['Login'] AND preg_match('/sgn/',$ligne_chercher_users['Droits'])) //utilisateur lui-meme et les utilisateurs non signataires
      {
?>
		    <tr>
		      <td>
			<input type="checkbox" name="check_sign[]" id="check_del_<?php echo $ligne_chercher_users['GUID']?>" value="<?php echo $ligne_chercher_users['GUID']; ?>" <?php if ($coche) echo "checked='checked'"?> />
			<label for="check_del_<?php echo $ligne_chercher_users['GUID']?>">
<?php 
    echo $ligne_chercher_users['Login'] ?>
			</label>
		      </td>
		      <td>
			<input type="text" name="droits_<?php echo $ligne_chercher_users['Login']; ?>" id="droits_<?php echo $ligne_chercher_users['Login']; ?>" value="<?php echo $droits ?>" 
<?php 
    if (!preg_match('/adm/',$ligne_user_droits['Droits']) AND !preg_match('/adm/',$droits) ) //les droits peuvent etre modifies si la personne connectee est admin.
      echo "readonly='readonly'";
?>
  />
		      </td>
		      <td>
			<input type="button" name="assistant" value="Assistant" onclick="assistant_droits('<?php echo $ligne_chercher_users['GUID'] ?>','<?php echo $ligne_intervenant['GUID'] ?>','delegue')" />
		      </td>
		    </tr>
<?php
    }
  }
?>
		</table>
		</fieldset>
	      </form>
<?php
}
?>
	      <form action="valid_user.php" id="form_sign" method="get" style="float:left;"  class="noPrint" >
		<fieldset>
		  <legend>
		    Signataire pour
		  </legend>
<?php
if (preg_match('/sgn/',$ligne_intervenant['Droits']))
{
?>
		  <input type="hidden" name="GUID_intervenant" value="<?php echo $ligne_intervenant['GUID'] ?>" />
		  <input name="submit_sign" type="submit" value="Valider" />
		  <table>
<?php
  $sql_select_signataire=$pdo->prepare("SELECT * FROM Personnes INNER JOIN user_perms ON Personnes.GUID=user_perms.SignataireGUID WHERE user_perms.SignataireGUID= ? ");
  $sql_select_signataire->bindValue(1, $ligne_intervenant['GUID'], PDO::PARAM_STR);
  $sql_select_signataire->execute();
  $ligne_select_all_signataire=$sql_select_signataire->fetchAll(PDO::FETCH_ASSOC);
  $sql_select_signataire->closeCursor();

  foreach ($ligne_chercher_all_users AS $ligne_chercher_users)
  {
    $droits="";
    $coche=0;
    foreach ($ligne_select_all_signataire AS $ligne_select_signataire)
    {
      if ($ligne_select_signataire['FriendUserGUID']==$ligne_chercher_users['GUID'])
      {
	$coche=1;
	$droits=$ligne_select_signataire['FriendUserDroits'];
      }
    }
    if ($ligne_chercher_users['Login'] !=$ligne_intervenant['Login'] ) //utilisateur lui-meme
    {
?>
		    <tr>
		      <td>
			<input type="checkbox" name="check_sign[]" id="check_sign_<?php echo $ligne_chercher_users['GUID'] ?>" value="<?php echo $ligne_chercher_users['GUID']; ?>" <?php if ($coche) echo "checked='checked'"?> />
			<label for="check_sign_<?php echo $ligne_chercher_users['GUID'] ?>">
			  <?php echo $ligne_chercher_users['Login'] ?>
			</label>
		      </td>
		      <td>
			<input type="text" name="droits_sign_<?php echo $ligne_chercher_users['Login'] ?>" id="droits_sign_<?php echo $ligne_chercher_users['Login'] ?>" value="<?php echo $droits ?>" />
			<input type="button" name="button_asssistant" value="Assistant" onclick="assistant_droits('<?php echo $ligne_chercher_users['GUID'] ?>','<?php echo $ligne_intervenant['GUID'] ?>','signataire')"/>
		      </td>
		    </tr>
<?php
    }
  }
?>
		  </table>
<?php
}
else
  echo "Utilisateur non signataire";
?>
		</fieldset>
	      </form>
<?php
  }
}
?>
<?php
if ($envoyer_date=="Chercher")//On cherche les consultations de patients lies a cet intervenant durant cette periode
{
  $sql2=$pdo->prepare("SELECT * FROM fchpat_Intervenants INNER JOIN IndexNomPrenom ON fchpat_Intervenants.fchpat_Intervenants_PatGUID=IndexNomPrenom.FchGnrl_IDDos INNER JOIN RubriquesHead ON IndexNomPrenom.FchGnrl_IDDos=RubriquesHead.RbDate_IDDos WHERE fchpat_Intervenants_IntervPK=? AND RbDate_Date>=? AND RbDate_Date<=? AND RbDate_TypeRub=20030000 ORDER BY RbDate_Date,FchGnrl_NomDos,FchGnrl_Prenom ");
  $sql2->bindValue(1, $intervenant, PDO::PARAM_STR);
  $sql2->bindValue(2, $debut, PDO::PARAM_STR);
  $sql2->bindValue(3, $fin, PDO::PARAM_STR);

}
else
  $sql2=$pdo->prepare("SELECT * FROM fchpat_Intervenants INNER JOIN IndexNomPrenom ON fchpat_Intervenants.fchpat_Intervenants_PatGUID=IndexNomPrenom.FchGnrl_IDDos WHERE fchpat_Intervenants_IntervPK='$intervenant' ORDER BY FchGnrl_NomDos,FchGnrl_Prenom") ;
  $sql2->bindValue(1, $intervenant, PDO::PARAM_STR);
  $sql2->execute();
  $ligne2_all=$sql2->fetchAll(PDO::FETCH_ASSOC);
  $sql2->closeCursor();
  $count2=count($ligne2_all);

if ($envoyer_date=="Chercher")
{
?>
		<div class="information"	title="Un clic sur la date affiche la consultation,
				  Un clic sur un nom affiche le dossier du patient">
		  <table>
<?php
  if ($debut_court==$fin_court)
    echo "
		    <tr>
		      <th>
			Consultations de patients li&eacute;s &agrave; cet intervenant le ".$debut_court." : ".$count2."
		      </th>
		    </tr>";
  else
    echo "
		    <tr>
		      <th>
			Consultations de patients li&eacute;s &agrave; cet intervenant  du ".$debut_court." au ".$fin_court." : ".$count2."
		      </th>
		    </tr>";
  foreach ($ligne2_all AS $ligne2)
  {
    $date_court=local_to_iso(substr($ligne2['RbDate_Date'],0,10),$date_format);
    echo "
		    <tr>
		      <td>"
			.$date_court." <a href=\"frame_patient.php?GUID=".$ligne2['FchGnrl_IDDos']."\">".$ligne2['FchGnrl_NomDos']." ".$ligne2['FchGnrl_Prenom']."</a>
		      </td>
		    </tr>";
  }
?>
		</table>
	      </div>

<?php
}
elseif ($montrer_patients=="Montrer")
{
?>
	      <div class="information">
		  <table>
		    <tr>
		      <th>
			Patients li&eacute;s &agrave; cet intervenant : <?php echo $count2 ?>
		      </th>
		      <td> <!--Nouveau lien -->
			<form action="liaison_correspondant.php" method="get"><!-- bouton Lier -->
			  <div>
			    <input name="ID_corresp" type="hidden" value="<?php echo $intervenant?>" />
			    <input name="envoyer" type="submit" value="Lier un nouveau patient" />
			  </div>
			</form>
		      </td>
		      <td valign="top">
			<br/>
		      </td>
		    </tr>
<?php
  foreach ($ligne2_all AS $ligne2)
//  while ($ligne2=mysqli_fetch_array($resultat2))//liste des patients lies
  {
    echo "
		  <tr>
		    <td>
		      <a href=\"frame_patient.php?GUID=".$ligne2['FchGnrl_IDDos']."\">".$ligne2['FchGnrl_NomDos']." ".$ligne2['FchGnrl_Prenom']."</a>";
?>
		    </td>
		    <td>
		      <form action="liaison_correspondant.php" method="get"><!--bouton de suppression de liaison -->
			<div>
			  <input name="from" type="hidden" value="intervenant" />
			  <input name="ID_corresp" type="hidden" value="<?php echo $intervenant ?>" />
			  <input name="patient_ID" type="hidden" value="<?php echo $ligne2['FchGnrl_IDDos'] ?>" />
			  <input name="ID_fiche_liaison" type="hidden" value="<?php echo $ligne2['fchpat_Intervenants_PK'] ?>" />
			  <input name="envoyer" type="submit" value="Annuler le lien" />
			</div>
		      </form>
		    </td>
		    <td>
		      
		    </td>
		  </tr>
<?php
  } //fin des patients lies
?>
		</table>
	    
	      </div>
<?php
}
?>
	    </div>
	<div class='noPrint'>
<?php
include("inc/footer.php");
?>