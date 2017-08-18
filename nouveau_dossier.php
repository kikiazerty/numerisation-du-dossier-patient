<?php
session_start();
$loginOK = false;  // cf Astuce

if ( !isset( $_SESSION['login'] ) )
{
//On part sur la page de login si pas logue
  header('location: index.php?page=nouveau_dossier' );
  exit;
}

$tab_login=explode("::",$_SESSION['login']);
$user=$tab_login[0];

include("config.php");

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

if (isset($_REQUEST['critere_recherche']))
  $critere_recherche=$_REQUEST['critere_recherche'];
else
  $critere_recherche="";

$Tel="";
$Prenom="";
$Nom="";
$titre="";

if (isset($_REQUEST['cle']))
  $cle=str_replace('%','',$_REQUEST['cle']);

if (isset($_REQUEST['envoyer']))
  $envoyer=$_REQUEST['envoyer'];

if (isset($_REQUEST['ID']))
  $ID=$_REQUEST['ID'];

//$Nee=date('Y-m-d', date('U'));
$Nee='0000-00-00';
if (isset($_REQUEST['Nee']))
  $Nee=$_REQUEST['Nee'];
if ($envoyer=='Nouveau')
  $Nee=iso_to_local($Nee,$date_format);
if (isset($_REQUEST['Tel']))
  $Tel=$_REQUEST['Tel'];

if (isset($_REQUEST['Prenom']))
  $Prenom=$_REQUEST['Prenom'];

if (isset($_REQUEST['Nom']))
  $Nom=$_REQUEST['Nom'];

if (isset($_REQUEST['titre']))
  $titre=$_REQUEST['titre'];

$enlever_verrou="";

//verification des droits
//Pas le droit de lire cette page si pas le droit idc

$sql_verif_droits=$pdo->prepare('SELECT Droits FROM Personnes WHERE Login= ?');
$sql_verif_droits->bindValue(1, $user, PDO::PARAM_STR);
$sql_verif_droits->execute();
$ligne_verif_droits=$sql_verif_droits->fetch(PDO::FETCH_ASSOC);
$sql_verif_droits->closeCursor();
$droits=$ligne_verif_droits['Droits'];

if (stripos($droits,"idc"))
  $ecritureOK=1;
else
  $ecritureOK="";

 if (!$envoyer)
{	
  header('location: liste.php' );
  exit;
}

if ($envoyer=='Modifier' OR $envoyer=='Supprimer' OR $envoyer=='Dupliquer')
{
  //recherche des notes
  $sql_note=$pdo->prepare('SELECT fchpat_Note_Html FROM fchpat_Note WHERE fchpat_Note_PatPK= ?');
  $sql_note->bindValue(1, $ID, PDO::PARAM_STR);
  $sql_note->execute();
  $ligne_note=$sql_note->fetch(PDO::FETCH_ASSOC);
  $sql_note->closeCursor();

  //recherche du nom et du prenom

  $sql=$pdo->prepare('SELECT * FROM IndexNomPrenom WHERE ID_PrimKey= ?');
  $sql->bindValue(1, $ID, PDO::PARAM_STR);
  $sql->execute();
  $ligne=$sql->fetch(PDO::FETCH_ASSOC);
  $sql->closeCursor();

//recherche des autres caracteristiques de l'identite

  $sql2=$pdo->prepare('SELECT * FROM fchpat WHERE FchPat_RefPk= ?');
  $sql2->bindValue(1, $ID, PDO::PARAM_STR);
  $sql2->execute();
  $ligne2=$sql2->fetch(PDO::FETCH_ASSOC);
  $sql2->closeCursor();
}

function detectUTF8($string) //pour les notes dont l'encodage est toujours imprévisible
{
  return preg_match('%(?:
  [\\xC2-\\xDF][\\x80-\\xBF] # non-overlong 2-byte
  |\\xE0[\\xA0-\\xBF][\\x80-\\xBF] # excluding overlongs
  |[\\xE1-\\xEC\\xEE\\xEF][\\x80-\\xBF]{2} # straight 3-byte
  |\\xED[\\x80-\\x9F][\\x80-\\xBF] # excluding surrogates
  |\\xF0[\\x90-\\xBF][\\x80-\\xBF]{2} # planes 1-3
  |[\\xF1-\\xF3][\\x80-\\xBF]{3} # planes 4-15
  |\\xF4[\\x80-\\x8F][\\x80-\\xBF]{2} # plane 16
  )+%xs', $string);
}

include("inc/header.php");
?>
    <title>
      Fiche identité - Utilisateur <?php echo $_SESSION['login'] ?>
    </title>
<?php
if ($envoyer!="Supprimer" AND $ecritureOK) //Pas besoin de javascript pour supprimer un dossier
{
?>

  <script type="text/javascript">
//<![CDATA[
function  fillFields()
{
  var nomprenom=document.getElementById("select_patient").value.split('_');
  var GUID=nomprenom[0];
//  var nom=nomprenom[1];
//  var prenom=nomprenom[2];
//  document.forms['form_choix_patient'].elements['Nom'].value=nom;
//  document.forms['form_choix_patient'].elements['Prenom'].value=prenom;
  document.forms['form_general'].elements['UUID'].value=GUID;
}
//]]>
  </script>

       <script type="text/javascript">
//<![CDATA[
function dossierPatient() //lancer un dossier patient avec le deroulant AJAX
{
  var guid=document.forms['form_general'].elements['UUID'].value;
  location.href="frame_patient.php?GUID="+guid;
}
//]]>
  </script>
    
<!--   fonction AJAX pour la recherche de patient -->
  <script type="text/javascript">
//<![CDATA[
var xhr = null; //initialisation 
function request(callback) 
{
  var nom=document.getElementById("Nom").value;
  var prenom=document.getElementById("Prenom").value; 
  var select_critere=''; 
  var text_select_critere=''; 
//  var exact_match='';
//alert (nom);

/*  if (document.forms['form_choix_patient'].elements["exact"].checked == true)
  {
    exact_match="yes";
  }*/
  if (!xhr) //si pas de requete en cours, on cree
  {
    xhr = new XMLHttpRequest();
  }
  else //si requete en cours, on la tue pour en faire une plus rapide (plus de lettres donc moins de reponses)
  {
    xhr.abort();
  }
  //lancer la nouvelle requete

  xhr.onreadystatechange = function() 
  {
    if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
    {
      callback(xhr.responseText);
    }
  };

//on envoie a la page de recherche toutes les valeurs
//  xhr.open("GET", "recherche_patient.php?nom="+nom+"&prenom="+prenom+"&check_exact="+exact_match+"&select_critere="+select_critere+"&text_select_critere="+text_select_critere, true);
  xhr.open("GET", "recherche_patient.php?nom="+nom+"&prenom="+prenom+"&check_exact=no&select_critere=0", true);
  xhr.send(null);
}

function readData(sData) 
{
   //effacer l'ancien menu deroulant
  document.forms['form_general'].select_patient.length=0;
  if (sData.indexOf("|") !== -1) //pas d'affichage si pas de reponse renvoyee par ajax
  {
    identities=sData.split('|'); 
    for (var i in identities) //creation d'une option de menu pour toutes les identites
    {
      var detail=identities[i].split('_');
      if (detail[0]!='') //supprimer les entrees vides
      {
        selectValue=detail[0]+'_'+detail[1]+'_'+detail[2]; //guid nom prenom 
        optionDisplay=detail[1]+' '+detail[2]+' '+detail[7]; //nom prenom date de naissance pour 
	document.forms['form_general'].select_patient.options[document.forms['form_general'].select_patient.options.length] = new Option(optionDisplay,selectValue); 
      }
    }
    document.getElementById("button_access").style.display = "inline";
  }
  else //on n'a pas de resultats, alors on efface les boutons 
  {
    document.getElementById("button_access").style.display = "none";
  }
}
//]]>
  </script>

    <script type="text/javascript">
//<![CDATA[
function donner_focus(chp)
{
  document.getElementById(chp).focus();
}
//]]>
    </script>
    <script type="text/javascript">
//<![CDATA[
function addHyphen() //pour ajouter des tirets dans la date de naissance
{
  text=document.getElementById('naissance').value;
<?php
  if ($date_format=='fr' OR $date_format=='en')
  {
?>
    if ((text.length==2) || (text.length==5))
<?php
  }
  else //iso
  {
?>
    if ((text.length==4) || (text.length==7))
<?php
  }
?>
    text=text+'-';
    document.getElementById('naissance').value =text;
}
//]]>
    </script>

    <script type="text/javascript">
//<![CDATA[
function compute_birthdate(year,month)
{
  var today = new Date();
  birth_day=today.getDate();
  if (eval(birth_day)<10)
    birth_day='0'+birth_day;
  var birth_year ;
  var date_format="<?php echo $date_format ?>";
  var birth_date;

  if (eval("today.getMonth()+1")< eval("month"))
  {
    birth_month=eval("13+today.getMonth()-month");
    birth_year=eval("today.getFullYear()-year-1");
  }
  else
  {
    birth_month=eval("today.getMonth()-month+1");
    if (eval(birth_month)<10)
      birth_month='0'+birth_month;
    birth_year=eval("today.getFullYear()-year");
  }
  if (date_format=="fr")
    birth_date=birth_day+'-'+birth_month+'-'+birth_year;
  else if (date_format=="en")
    birth_date=birth_month+'-'+birth_day+'-'+birth_year;
  else
    birth_date=birth_year+'-'+birth_month+'-'+birth_day;
  var x=confirm('La date de '+birth_date+' a été calculée automatiquement. Confirmer ?');
  if (x)
    document.getElementById('datepicker').value = birth_date ;
}
//]]>
    </script>

    <script type="text/javascript">
//<![CDATA[
function zip_code(value)
// on ouvre dans une fenêtre le fichier passé en paramètre.
{
  window.open('zipcode.php?code='+value,'Codepostal','width=450,height=250,top=50,left=50,toolbar=yes, scrollbars=yes, location=no');
}
//]]>
    </script>

    <script type="text/javascript">
//<![CDATA[
function city(value)
// on ouvre dans une fenêtre le fichier passé en paramètre.
{
  window.open('city.php?city='+value,'Ville','width=450,height=250,top=50,left=50,toolbar=yes, scrollbars=yes, location=no');
}
//]]>
    </script>

    <script type="text/javascript">
//<![CDATA[
function calcul_cle(divident)
{
//calcul de la cle du numero de secu
  divident=divident.split(' ').join(''); //suppression des espaces
  if (divident.length!=13)
    alert ('Le nombre de chiffres donnés est '+divident.length+'. Il devrait être 13.\nLa clé trouvée n\'aura pas de valeur.');
  cle=(97-(divident%97))
  document.getElementById('cle_secu').value = cle;
}
//]]>
    </script>

    <script type="text/javascript">
//<![CDATA[
function change_sexe(value)
// on change dynamiquement le deroulant de sexe selon la politesse
{
  if (value=='Madame' || value=='Mademoiselle')
  {
    document.getElementById('sexe').selectedIndex = 0;
  }
  else if (value=='Monsieur')
  {
    document.getElementById('sexe').selectedIndex = 1;
  }
}
//]]>
    </script>

<?php
include 'calendar_javascript.php';
} //fin de l'exclusion pour le mode supprimer
?>
    <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen" />
  </head>
  <body style="font-size:<?php echo $fontsize; ?>pt" onload="donner_focus('Nom')" >
    <div class="conteneur">
<?php	
// insertion du menu d'en-tete	
$anchor='Écriture_dans_le_fichier';
include("inc/menu-horiz.php");

if ($ecritureOK) //acces seulement si droits (idc)
{
  if ($envoyer=='Nouveau' OR $envoyer=='Modifier' OR $envoyer=='Dupliquer')
  {
    if ($envoyer=='Nouveau')
    {
      if ($critere_recherche=='FchGnrl_NomDos')  //on recupere le nom transmis par l'URL
	$Nom=$cle;
      if ($critere_recherche=='FchGnrl_Prenom') 
	$Prenom= $cle; 
      if ($critere_recherche=='FchPat_NomFille') 
	$jeune_fille= $cle;
      else
	$jeune_fille= '';
	$profession='';
	$adresse='';
	$Tel2='';
	$Tel3='';
	$CP=$cp_default;
	$Ville=$ville_default;
	$Email='';
	$num_secu='';
	$cle_secu='';
	$prenom_assure='';
	$Note='';
	$ID_PrimKey='';
	$nom_assure='';
?>
    <div class="groupe">
      <h1>
	CHNP - Nouveau dossier
      </h1>
<?php
    }
    elseif ($envoyer=='Modifier' OR $envoyer=='Dupliquer')
    {
      if ($envoyer=='Modifier')
      {
        $sql_verifier_verrou=$pdo->prepare('SELECT * FROM IndexNomPrenom INNER JOIN Verrous ON IndexNomPrenom.FchGnrl_IDDos=Verrous.DossGUID WHERE IndexNomPrenom.ID_PrimKey= ?');
        $sql_verifier_verrou->bindValue(1, $ID, PDO::PARAM_STR);
        $sql_verifier_verrou->execute();
        $ligne_verifier_verrou=$sql_verifier_verrou->fetch(PDO::FETCH_ASSOC);
        $sql_verifier_verrou->closeCursor();

	if (count($ligne_verifier_verrou))
	{
	?>
	  <div class="notice">
      <b>Attention !</b> Dossier verrouill&eacute; par <?php echo $ligne_verifier_verrou['UserName'];
	  if ($ligne_verifier_verrou['UserName']==$loginbase."@".$host)
	  {
	  ?>
	    (vous-m&ecirc;me)
	  <br />
	  <?php
	  }
	  ?>
      <ul>
	<li>
	  Soit le programme a &eacute;t&eacute; arr&ecirc;t&eacute; brutalement sans avoir referm&eacute; le dossier, et vous pouvez continuer normalement
	</li>
	<li>
	  Soit vous avez ouvert la fiche en mode Modification et vous &ecirc;tes retourn&eacute; sur la page pr&eacute;c&eacute;dente par les fl&egrave;ches du navigateur et vous pouvez continuer normalement
	</li>
	<li>
	<?php
	  if ($ligne_verifier_verrou['UserName']!=$loginbase."@".$host)
	    echo "
	  Soit quelqu'un d'autre est en train de modifier ce dossier. Dans ce cas, c'est le dernier &agrave; enregistrer qui gagnera.";
	  else
	    echo "
	  Soit un autre programme est en train de modifier ce dossier. Dans ce cas, c'est le dernier &agrave; enregistrer qui gagnera.";
	  echo "
	</li>
      </ul>";
	  echo "
    </div>";
	} //fin de si verrou
	else //Toujours le mode modifier, mais dossier non verrouille - on pose un verrou
	{
          $sql_chercher_GUID=$pdo->prepare('SELECT * FROM IndexNomPrenom WHERE ID_PrimKey= ?');
          $sql_chercher_GUID->bindValue(1, $ID, PDO::PARAM_STR);
          $sql_chercher_GUID->execute();
          $ligne_chercher_GUID=$sql_chercher_GUID->fetch(PDO::FETCH_ASSOC);
          $sql_chercher_GUID->closeCursor();
 
          $sql_poser_verrou=$pdo->prepare("INSERT INTO Verrous (Pk,StartTime,RubName,RubPk,DossGUID,UserName,DossPk) VALUES (NULL,?,'All Rubriques','0',?,?,'0'");
          $sql_poser_verrou->bindValue(1, date('YmdHis', date('U'))."000", PDO::PARAM_STR);
          $sql_poser_verrou->bindValue(2, $ligne_chercher_GUID['FchGnrl_IDDos'], PDO::PARAM_STR);
          $sql_poser_verrou->bindValue(3, $loginbase."@".$host, PDO::PARAM_STR);
          $sql_poser_verrou->execute();
          $sql_poser_verrou->closeCursor(); 
	  
	  $enlever_verrou=1;
	} //fin pose verrou
      } //Fin du modifier seul - on revient dans Modifier ou Dupliquer

      $Nom=$ligne["FchGnrl_NomDos"];
      $Prenom=$ligne["FchGnrl_Prenom"];
      $jeune_fille=$ligne2["FchPat_NomFille"];
      $profession=$ligne2["FchPat_Profession"];
      $adresse=$ligne2["FchPat_Adresse"];
      $Tel=$ligne2["FchPat_Tel1"];
      $Tel2=$ligne2["FchPat_Tel2"];
      $Tel3=$ligne2["FchPat_Tel3"];
      $CP=$ligne2["FchPat_CP"];
      $Ville=$ligne2["FchPat_Ville"];
      $Email=$ligne2["FchPat_Email"];
      $num_secu=substr($ligne2["FchPat_NumSS"],0,13);
      $cle_secu=substr($ligne2["FchPat_NumSS"],-2);
      $nom_assure=$ligne2["FchPat_NomAss"];
      $prenom_assure=$ligne2["FchPat_PrenomAss"];

//On reencode en utf8 les notes mal encodees (bug dans ancien MedinTux)
      if (detectUTF8($ligne_note["fchpat_Note_Html"]))
	$Note=($ligne_note["fchpat_Note_Html"]);
      else
	$Note=utf8_encode($ligne_note["fchpat_Note_Html"]);

      if ($envoyer=='Modifier')
      {
	$ID_PrimKey=$ligne["ID_PrimKey"];
?>

    <div class="groupe">
      <h1>
	Modification du dossier de 
      <a href="frame_patient.php?GUID=<?php echo $ligne["FchGnrl_IDDos"] ?>&amp;enlever_verrou=1"><?php echo $ligne["FchGnrl_NomDos"]." ".$ligne["FchGnrl_Prenom"] ?></a>
      </h1>
<?php
      } //fin modifier
      elseif ($envoyer=='Dupliquer')
      {
?>

    <div class="groupe">
      <h1>
	Duplication du dossier de 
      <a href="frame_patient.php?GUID=<?php echo $ligne["FchGnrl_IDDos"] ?>&amp;enlever_verrou=1"><?php echo $ligne["FchGnrl_NomDos"]." ".$ligne["FchGnrl_Prenom"] ?></a>
      </h1>
<?php
      } //fin dupliquer
    } //fin si modifier ou dupliquer
?>
<div class="encadrer">
<!-- Le formulaire pour Nouveau, Dupliquer et modifier -->
      <form action="insert_dossier.php" method="post" id="form_general">
<!-- <div class="encadrer"> -->
	<fieldset>
	<legend>
	  Identit&eacute;
	</legend>
		<label class="questionnaire" for="Nom">
		  Nom&nbsp;:
		</label>
		<input name="ID_PrimKey" type="hidden" value="<?php echo $ID_PrimKey?>" />
                <input name="UUID" type="hidden" value="<?php echo $ligne["FchGnrl_IDDos"] ?>" />
		<input name="Nom" id="Nom" type="text" value ="<?php echo stripslashes($Nom) ?>" size="40" maxlength="50" title="Majuscules automatiques" onkeyup="request(readData);"/>
		<br />
		<label class="questionnaire" for="Prenom">
			Pr&eacute;nom&nbsp;: 
		</label>
		<input name="Prenom" id="Prenom" type="text" value ="<?php echo stripslashes($Prenom) ?>" size="40" maxlength="30"  title="Majuscules automatiques" onkeyup="request(readData);"/>
		<br />
		<label  class="questionnaire" style="float:left;clear:both;">
		Patients déjà connus&nbsp;:
		</label>
                <select name="select_patient" id="select_patient" style="float:left;" onchange="fillFields();dossierPatient();">
                  <option></option>
                </select>
                <input name="button_access" id="button_access" type="button" value="Accéder au dossier" onclick="fillFields();dossierPatient();" style="float:left;display:none;" /><br />

		<label class="questionnaire" for="NomJF" style="float:left;clear:both;">
			Nom de JF&nbsp;: 
		</label>
		<input name="NomJF" id="NomJF" type="text" value ="<?php echo stripslashes($jeune_fille) ?>" size="40" maxlength="50"  title="Majuscules automatiques"/>
		<br />

		<label class="questionnaire" for="titre">
			Titre&nbsp;: 
		</label>
		&nbsp;<select name="titre" id="titre" onchange="change_sexe(this.value)" title="Modifie automatiquement le sexe, sauf pour Enfant"> 
			<option <?php if ($envoyer=="Modifier" OR $envoyer=="Dupliquer") { if ($ligne2["FchPat_Titre"]=="Madame" or $ligne2["FchPat_Titre"]=="Mme") echo "selected='selected'";} ?>  value="Madame">
				Madame
			</option>
			<option <?php if ($envoyer=="Modifier" OR $envoyer=="Dupliquer") { if ($ligne2["FchPat_Titre"]=="Monsieur" or $ligne2["FchPat_Titre"]=="Mr") echo "selected='selected'";} ?> value="Monsieur">
				Monsieur
			</option>
			<option <?php if ($envoyer=="Modifier" OR $envoyer=="Dupliquer") { if ($ligne2["FchPat_Titre"]=="Mademoiselle" or $ligne2["FchPat_Titre"]=="Mlle") echo "selected='selected'"; }?> value="Mademoiselle">
				Mademoiselle
			</option>
			<option  <?php if ($envoyer=="Modifier" OR $envoyer=="Dupliquer") { if ($ligne2["FchPat_Titre"]=="Enft" or $ligne2["FchPat_Titre"]=="Enfant" or stripslashes($ligne2["FchPat_Titre"])=="L'enfant" ) echo "selected='selected'";} ?> value="L'enfant">
				Enfant
			</option>
		</select>
		<br />

		<label class="questionnaire" for="datepicker">
		Date de naissance&nbsp;: 
		</label>
		<input name="naissance" id="datepicker" type="text" value="<?php echo $Nee ?>" size="12" maxlength="10" title="Respecter impérativement le format fourni en exemple ou utilisez le calendrier" onkeyup="addHyphen()"/><br />
                <label class="questionnaire" for="year">Âge&nbsp;:</label> <input type="text" id="year" name="year" size="2" onchange="compute_birthdate(this.value,this.form.elements['month'].value)" /> ans <input type="text" id="month" name="month" size="2" onchange="compute_birthdate(this.form.elements['year'].value,this.value)" /> mois
		<br />

		<label class="questionnaire" for="sexe">
		  Sexe&nbsp;: 
		</label>
		&nbsp;<select name="sexe" id="sexe" title="Changé automatiquement par le déroulant Titre, sauf pour Enfant">
			<?php
			if ($envoyer=="Nouveau" OR $envoyer=="Dupliquer")
			{
			?>
				<option value="F">
					F&eacute;minin
				</option>
				<option <?php if ($titre=="Monsieur") echo "selected='selected'" ?> value="M">
					Masculin
				</option>
			<?php
			}
			elseif ($envoyer=="Modifier")
			{
			?>
			<option <?php if ($ligne2["FchPat_Sexe"]=="F") echo "selected='selected'" ?> value="F">
				F&eacute;minin 
			</option>
			<option <?php if ($ligne2["FchPat_Sexe"]=="M") echo "selected='selected'" ?> value="M">
				Masculin 
			</option>
			<?php
			}
			?>
		</select>
		<br />
		
		<label class="questionnaire" for="gem">
			R.Gem&nbsp;: 
		</label>
		&nbsp;<select name="gem" id="gem"  >
			<option value="01" <?php if ($envoyer=="Modifier" OR $envoyer=="Dupliquer") { if ($ligne2["FchPat_Geme"]==01) echo "selected='selected'";} ?>>
			1
			</option>
			<option value="02" <?php if ($envoyer=="Modifier" OR $envoyer=="Dupliquer") { if ($ligne2["FchPat_Geme"]==02) echo "selected='selected'";} ?>>
			2
			</option>
			<option value="03" <?php if ($envoyer=="Modifier" OR $envoyer=="Dupliquer") { if ($ligne2["FchPat_Geme"]==03) echo "selected='selected'";} ?>>
			3
			</option>
			<option value="04" <?php if ($envoyer=="Modifier" OR $envoyer=="Dupliquer") { if ($ligne2["FchPat_Geme"]==04) echo "selected='selected'";} ?>>
			4
			</option>
			<option value="05" <?php if ($envoyer=="Modifier" OR $envoyer=="Dupliquer") { if ($ligne2["FchPat_Geme"]==05) echo "selected='selected'";} ?>>
			5
			</option>
			<option value="06" <?php if ($envoyer=="Modifier" OR $envoyer=="Dupliquer") { if ($ligne2["FchPat_Geme"]==06) echo "selected='selected'";} ?>>
			6
			</option>
		</select>
		<br />

		<label class="questionnaire" for="Profession">
		  Profession&nbsp;: 
		</label>
		<input name="Profession" id="Profession" type="text" value ="<?php echo stripslashes($profession) ?>" size="40" maxlength="48" />
		<br />
	      </fieldset>
            </div>

            <div class="encadrer"> 
	      <fieldset>
		<legend>Coordonn&eacute;es</legend>
		<label class="questionnaire" for="Adresse">
		  Adresse&nbsp;: 
		</label>
		&nbsp;<textarea name="Adresse" id="Adresse" rows="4" cols="35" ><?php echo stripslashes($adresse) ?></textarea>
		<br />

		<label class="questionnaire" for="CP">
			Code postal&nbsp;: 
		</label>
		<input name="CP"  id="CP" type="text" value="<?php echo $CP ?>" size="11" maxlength="11" onchange="zip_code(this.value)" title="Pas d'espaces SVP. Détermination automatique de la ville au changement de champ. Ne pas appuyer sur Enter (valide la fiche)."/>
		<br />

		<label class="questionnaire" for="Ville">
			Ville&nbsp;: 
		</label>
		<input name="Ville"  id="Ville" type="text" value="<?php echo stripslashes($Ville) ?>" size="40" maxlength="30" onchange="city(this.value)" title="Mettez des espaces au lieu des apostrophes, comme VILLE D AVRAY. Détermination automatique du code postal au changement de champ par clic ou Tabulation, mais pas Enter."/>
		<br />
		
		<label class="questionnaire" for="Tel1">
			Tel1&nbsp;: 
		</label>
		<input name="Tel1" id="Tel1" type="text" value="<?php echo $Tel ?>"  size="40" maxlength="30" title="Suppression automatique des espaces"/>
		<br />

		<label class="questionnaire" for="Tel2">
			Tel2&nbsp;: 
		</label>
		<input name="Tel2" id="Tel2" type="text" value="<?php echo $Tel2 ?>" size="40" maxlength="30" title="Suppression automatique des espaces"/>
		<br />

		<label class="questionnaire" for="Tel3">
			Tel3&nbsp;: 
		</label>
		<input name="Tel3" id="Tel3" type="text" value="<?php echo $Tel3 ?>" size="40" maxlength="30" title="Suppression automatique des espaces"/>
		<br />

		<label class="questionnaire" for="email">
			E-mail&nbsp;: 
		</label>
		<input name="email" id="email"  type="text" value="<?php  echo $Email ?>" size="40" maxlength="50" />
		<br />
	    </fieldset>
          </div>

          <div class="encadrer">	    
	    <fieldset>
		<legend>Assuré</legend>

		<label class="questionnaire" for="Secu">
		Num&eacute;ro&nbsp;: 
		</label>
		<input name="Secu" id="Secu" type="text" value="<?php  echo $num_secu ?>" size="40" maxlength="21" onchange="calcul_cle(this.value)" title="Calcul automatique de la clé au changement de champ"/>
		<br />

		<label class="questionnaire" for="cle_secu"><b>Clé&nbsp;: </b></label>
		<input name="cle_secu" id="cle_secu" type="text" value="<?php  echo $cle_secu ?>" size="2" maxlength="2" />
		<br />	

		<label class="questionnaire" for="pas_patient">
		&nbsp;
		</label>
		<?php
		if ($envoyer=="Nouveau")
		{
		?>
		&nbsp;<input name="pas_patient" id="pas_patient" type="checkbox" value="0" />
		<?php
		}
		elseif ($envoyer=="Modifier" OR $envoyer=="Dupliquer")
		{
		?>
		&nbsp;<input <?php if ($ligne2["FchPat_PatientAss"]=="0") echo "checked='checked'" ?> name="pas_patient" id="pas_patient" type="checkbox" value="0" />
		<?php
		}
		?>
		<i>Cochez si le patient n'est pas l'assuré</i>
		<br />
		
		<label class="questionnaire" for="nom_assure">
			Nom&nbsp;:
		</label>
		<input name="nom_assure" id="nom_assure" type="text" value="<?php  echo $nom_assure ?>" size="40" maxlength="50" />
		<br />

		<label class="questionnaire" for="prenom_assure">
			Pr&eacute;nom&nbsp;:
		</label>
		<input name="prenom_assure"  id="prenom_assure" type="text" value="<?php  echo $prenom_assure ?>" size="40" maxlength="40" />
		</fieldset>
              </div>

              <div class="encadrer">	
		<fieldset>
		<legend>Divers</legend>

		<div class="notice">
			<strong>Avertissement</strong> : les données saisies dans ce cadre ne sont pas exploitées pour le contrôle de sécurité des ordonnances médicamenteuses.
		</div>
		<br />
		
		<label class="questionnaire" for="Note">
		Notes&nbsp;:
		</label>
		&nbsp;<textarea name="Note" id="Note"  rows="6" cols="40" ><?php echo $Note ?></textarea>
		<br />
		<br />
		
		<?php
		if ($envoyer=="Modifier")
		{
		?>
		<input name="Envoyer" type="submit" value="Modifier" title="Enregistre les modifications effectu&eacute;s dans le formulaire" />
		<input name="Reset" type="reset" value="R&eacute;initialiser" title="Remet les champs à leur valeur initiale"/>
		<?php
		}
		else
		{
		?>

		<input name="Envoyer" type="submit" value="Ajouter" title="Cr&eacute;e une nouvelle fiche. Ne pas employer pour modifier une fiche, seulement pour en créer une copie en cas de mode Modification."/>
<?php
		}
?>
		</fieldset>
</div>

      </form>
    </div>
<?php

  } //Fin Nouveau et modifier
  elseif ($envoyer=="Supprimer")//Interface de confirmation de suppression des dossiers
  {
    if (stripos($droits,"adm"))//On verifie si l'utilisateur a les droits admin
    {
?>

 <div class="groupe">
      <h1>
	Mode suppression
      </h1>
      <p style="text-align:center">
	<b>Confirmez-vous la suppression de la fiche de <?php echo $ligne["FchGnrl_NomDos"]." ".$ligne["FchGnrl_Prenom"].", n&eacute; le ".$Nee ?> ?</b>
	<br /><br /><br />
      </p>
      <form action="insert_dossier.php" method="get" style="text-align:center">
	<div>
	  <input name="UUID" type="hidden" value="<?php echo $ligne["FchGnrl_IDDos"] ?>" />
	  <input name="ID_PrimKey" type="hidden" value="<?php echo $ID ?>" />
	  <input name="Nom" type="hidden" value="<?php echo $ligne["FchGnrl_NomDos"] ?>" />
	  <input name="confirmer" type="submit" value="Supprimer" />
	  <input name="confirmer" type="submit" value="Conserver" />
	</div>
      </form>
      <p style="text-align:center">
	<br /><br />
	<b>Attention ! Cette action est irr&eacute;versible et entra&icirc;ne une perte d&eacute;finitive de donn&eacute;es.</b>
	<br /><br />
      </p>
      <p style="text-align:center">
	<a href="frame_patient.php?GUID=<?php echo $ligne["FchGnrl_IDDos"] ?>">
	  Acc&eacute;der au dossier de <?php echo $ligne["FchGnrl_NomDos"]." ".$ligne["FchGnrl_Prenom"]?>
	</a>
      </p>
   </div>		
<?php
    }
    else 
    {//Pas administrateur
?>
      <p style="text-align:center">
	<b>Vous devez avoir les droits d'administration pour supprimer une fiche.</b>
      </p>
<?php
    }
  }
}
else //pas les droits idc
{
?>
      <p style="text-align:center">
	<b>Vous n'avez pas les droits de cr&eacute;ation ou de modification de dossier</b>
      </p>
<?php
}
include("inc/footer.php");
?>
</div>
  </body>
</html>