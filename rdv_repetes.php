<?php
session_start() ;
if ( !isset( $_SESSION['login'] ) ) 
{
  header('location: index.php?page=agenda' );
  exit;
}
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
//les dates de recherche selon la langue
if ($date_format=='fr')
  $date=date('d-m-Y', date('U'));
elseif ($date_format=='en')
  $date=date('m-d-Y', date('U'));
else 
 $date=date('Y-m-d', date('U')); // la date du jour

//initialisation des jours de la semaine
//$text_jsemaine=array(0=>"Dimanche",1=>"&nbsp;&nbsp;Lundi&nbsp;&nbsp;",2=>"&nbsp;&nbsp;Mardi&nbsp;&nbsp;",3=>"Mercredi",4=>"&nbsp;&nbsp;Jeudi&nbsp;&nbsp;",5=>"Vendredi",6=>"&nbsp;Samedi&nbsp;");

//URL nom=&prenom=&tel=&type=&status=%25%25&GUID=&duree=15&notes=
//On recupere les variables dans l'URL

$tab_login=explode("::",$_SESSION['login']);
$user=$tab_login[0];
$utilisateur_autorisant=$tab_login[1];

$Type="";
$Status="";
$Nom="";
$Prenom="";
$GUID='';
$Duree='';
$Tel='';
$Adresse='';	
$Notes='';

if (isset($_GET['Type']))
  $Type=$_GET['Type'];

if (isset($_GET['Status']))
  $Status=$_GET['Status'];

if (isset($_GET['Nom']))
  $Nom=$_GET['Nom'];

if (isset($_GET['Prenom']))
  $Prenom=$_GET['Prenom'];

if (isset($_GET['GUID']))
	$GUID=$_GET['GUID'];

if (isset($_GET['Duree']))
	$Duree=$_GET['Duree'];

if (isset($_GET['Tel']))
	$Tel=$_GET['Tel'];
if (isset($_GET['Adresse']))
	$Adresse=$_GET['Adresse'];

if (isset($_GET['Time']))
{
  $Time=$_GET['Time'];
  $Heure=substr($Time,0,2);
  $Minutes=substr($Time,3,2);
}
else
{
  $Heure='00';
  $Minutes='00';
}

if (isset($_GET['Date']))
  $Date=$_GET['Date'];
if (isset($_GET['Notes']))
  $Notes=$_GET['Notes'];

//Recherche des couleurs de RdV
//$sqlcouleur="SELECT * FROM color_profils GROUP BY Name";

$sqlcouleur=$pdo->prepare("SELECT * FROM color_profils");
$sqlcouleur->execute();
$lignecouleur_all=$sqlcouleur->fetchAll(PDO::FETCH_ASSOC);
$sqlcouleur->closeCursor();

include("inc/header.php");
?>
    <title>
      MedWebTux - Rendez-vous répétés -<?php echo $_SESSION['login'] ?>
    </title>
    
      <script type="text/javascript">
//<![CDATA[
function  fillFields() //pour remplir les champs de recherche avec le resultat d'AJAX
{
  var nomprenom=document.getElementById("select_patient").value.split('_');
  var GUID=nomprenom[0];
  var nom=nomprenom[1];
  var prenom=nomprenom[2];
  var tel=nomprenom[3];
  var adresse=nomprenom[4];
  var ville=nomprenom[5];
  var zipcode=nomprenom[6];
  document.forms['form_jour'].elements['Nom'].value=nom;
  document.forms['form_jour'].elements['Prenom'].value=prenom;
  document.forms['form_jour'].elements['GUID'].value=GUID;
  document.forms['form_jour'].elements['Tel'].value=tel;
  document.forms['form_jour'].elements['Adresse'].value=adresse+'  '+ville+' '+zipcode;
}
//]]>
    </script>
  
    <script type="text/javascript">
//<![CDATA[
function vide_form()//pour effacer le patient
{
  document.forms['form_jour'].elements['GUID'].value= '';
  document.forms['form_jour'].elements['Prenom'].value= '';
  document.forms['form_jour'].elements['Nom'].value = '';
  document.forms['form_jour'].elements['Tel'].value = '';
  document.forms['form_jour'].elements['Adresse'].value = '';
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
 //marche alert (nom);

  var prenom=document.getElementById("Prenom").value; 
  var select_critere=document.getElementById("select_critere").value; 
  var text_select_critere=document.getElementById("text_select_critere").value; 
  var exact_match="no";

  if (document.forms['form_jour'].elements["check_exact"].checked == true)
  {
    exact_match="yes";
  }
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
  xhr.open("GET", "recherche_patient.php?nom="+nom+"&prenom="+prenom+"&check_exact="+exact_match+"&select_critere="+select_critere+"&text_select_critere="+text_select_critere, true);
  xhr.send(null);
}

function readData(sData) 
{
   //effacer l'ancien menu deroulant
  document.forms['form_jour'].select_patient.length=0;
  if (sData.indexOf("|") !== -1) //pas d'affichage si pas de reponse renvoyee par ajax
  {
    identities=sData.split('|'); 
    for (var i in identities) //creation d'une option de menu pour toutes les identites
    {
      var detail=identities[i].split('_');
      if (detail[0]!='') //supprimer les entrees vides
      {
        selectValue=detail[0]+'_'+detail[1]+'_'+detail[2]+'_'+detail[3]+'_'+detail[4]+'_'+detail[5]+'_'+detail[6]; //guid nom prenom tel adresse pour remplir les champs
        optionDisplay=detail[1]+' '+detail[2]+' '+detail[7]; //nom prenom date denaissance pour 
	document.forms['form_jour'].select_patient.options[document.forms['form_jour'].select_patient.options.length] = new Option(optionDisplay,selectValue); //marche isolement
      }
    }
  }
}
//]]>
  </script>

    <script type="text/javascript" >
//<![CDATA[
function verif_champ(champ)
{
  if (champ < 10)
  { 
    alert("La durée des rendez-vous répétés doit être de 10 minutes au moins");
    return false;
  }
  return true;
}
//]]>
    </script>
    <script type="text/javascript" >
//<![CDATA[
function donner_focus(chp)
{
	document.getElementById(chp).focus();
}
//]]>
    </script>
    <script type="text/javascript" >
//<![CDATA[
function mettre_valeur(value)
{
  var type=new Array;
<?php
  //creation de variables javascript pour les durees des rdv

//  $resultatcouleur=mysqli_query($db,$sqlcouleur);
foreach ($lignecouleur_all AS $lignecouleur)
//  while ($lignecouleur=mysqli_fetch_array($resultatcouleur))
  {
    $name=$lignecouleur['Name'];
    echo "type[\"".$name."\"]=\"".$lignecouleur['Duree']."\";"; 
  }
?>
  document.forms['form_jour'].Duree.value = type[value]; // on ecrit le resultat dans Duree
}
//]]>
    </script>
<?php
include 'calendar_javascript.php';
?>

    <script type="text/javascript" >
//<![CDATA[
function chercher_patient()  //obsolete
// on ouvre dans une fenêtre le fichier passé en paramètre.
{ 
//  window.open('recherche_patient_agenda.php','Choisir','width=700,height=450,top=50,left=50,toolbar=yes, scrollbars=yes, location=no'); 
}
//]]>
    </script>
  </head>

  <body style="font-size:<?php echo $fontsize; ?>pt" >
    <div class="conteneur">
<?php	
// insertion du menu d'en-tete	
$anchor="rafale";
include("inc/menu-horiz.php");		
?>
      <div class="groupe">	
	<h1>
	  MedWebTux - Rendez-vous répétés de <?php echo $utilisateur_autorisant; ?>
	</h1>
<?php

//formatage de la date
if (isset($_GET['Date']))
{
  $jour=substr($_GET['Date'],8,2);
  $mois=substr($_GET['Date'],5,2);
  $annee=substr($_GET['Date'],0,4);
}
else
{
  if (isset($_GET['jour']))
  {
    $jour=$_GET['jour'];
  }
}

?>
      <form action="validation_rdv.php" method="get" id="form_jour" onsubmit='return verif_champ(this.Duree.value);'>
      <fieldset>
       <legend>Rendez-vous répétés</legend>
	<table>
	  <tr>
	    <td>
	      <table>
		<tr>
		  <th>
		    <label for="Type">
		      Type : 
		    </label>
		  </th>
		  <td>
		    <input name="RDV_PrisAvec" type="hidden" value="<?php echo $user ?>" />
		    <select name="Type" id="Type" onchange="mettre_valeur(this.value)">
<?php
//recuperation des differents types de RDV
//$resultatcouleur=mysqli_query($db,$sqlcouleur);
foreach ($lignecouleur_all AS $lignecouleur)
//while ($lignecouleur=mysqli_fetch_array($resultatcouleur))
{
?>
		      <option value="<?php echo $lignecouleur["Name"]?>"<?php if ($Type==$lignecouleur["Name"])  echo 'selected="selected"' ;?>>
			<?php echo $lignecouleur["Name"];?>
		      </option>
<?php
}
?>
		    </select>
		  </td>
		</tr>
		<tr>
		  <th>
		    <label for="Duree">
		      Dur&eacute;e : 
		    </label>
		  </th>
		  <td>
		    <input name="Duree" id="Duree" type="text" value="<?php echo $Duree; ?>" size="5" maxlength="11" />
		  </td>
		</tr>
		<tr>
		  <th>
		    <label for="status">
		    Statut : 
		    </label>
		  </th>
		  <td>
		    <select name="status" id="status" >
<?php
  foreach ($status_rdv AS $ce_rdv)
  {
?>
		      <option value="<?php echo $ce_rdv ?>" 
<?php 
    if ($ce_rdv==$Status) //si le statut est recupere dans l'URL
	echo "selected='selected'"; 
    else
    {
      if (!$Nom AND $ce_rdv=="Non attribué") //si pas de nom dans l'URL, on selectionne non attribue
	echo "selected='selected'"; 
    }
?>
 >
<?php echo $ce_rdv ?>
		      </option>
<?php
  }
?>
		    </select>
		  </td>
		</tr>
	<!--	<tr>
		  <td>
		    <a href="#" onclick="chercher_patient()">Choisir un patient</a>
		  </td>
		  <td>
		  </td>
		</tr>-->
		<tr>
		  <th>
		    <label for="GUID">
		      ID : 
		    </label>
		  </th>
		  <td>
		    <input name="GUID" id="GUID" type="text" value="<?php echo $GUID ?>" />
		  </td>
		</tr>
		<tr>
		  <th>
		    <label for="Nom">
		      Nom : 
		    </label>
		  </th>
		  <td>
<!-- 		    <input name="Nom" id="Nom" type="text" value="<?php echo $Nom ?>" size="20" maxlength="40" /> -->
                    <input name="Nom" id="Nom" type="text" value="<?php echo $Nom ?>" size="20" maxlength="40" style="float:left" onkeyup="request(readData);" /><br />
		    <label for="check_exact">
		    Exact
		    </label>
		    <input type="checkbox" name="check_exact" id="check_exact" onclick="request(readData)"/> 

		  </td>
		</tr>
		<tr>
		<th>
                  <select name="select_patient" id="select_patient" style="float:left;clear: both;" onchange="fillFields()">
                    <option></option>
                  </select>
		</th>
		<td>
                  <input type="button" value="Remplir les champs" onclick="fillFields()" style="float:left;clear: both;"/>
                  <input type="button" value="Vider" onclick="vide_form()"  style="float:left"/>
		</td>
		</tr>
		<tr>
		  <th>
		    <label for="Prenom">
		      Pr&eacute;nom : 
		    </label>
		  </th>
		  <td>
		    <input name="Prenom" id="Prenom" type="text" value="<?php echo $Prenom ?>" size="20" maxlength="40" onkeyup="request(readData);"/>
		  </td>
		</tr>
		<tr>
		<th>                    
                  <select name="select_critere" id="select_critere" style="float:left" onchange="request(readData)">
                    <option value="0">
                      Autre critère de recherche
                    </option>
                    <option value="FchPat_NomFille">
                      Nom de jeune fille
                    </option>
                    <option value="FchPat_Adresse">
                      Adresse
                    </option>
                    <option value="FchPat_Ville">
                      Ville
                    </option>
                    <option value="FchPat_CP">
                      Code Postal
                    </option>
                    <option value="FchPat_NumSS">
                      Num&eacute;ro de s&eacute;cu
                    </option>
                    <option value="FchPat_Nee">
                      Date de naissance
                    </option>
                    <option value="FchPat_Profession">
                      Profession
                    </option>
                    <option value="FchPat_Tel1">
                      T&eacute;l&eacute;phone
                    </option>
                  </select>
		</th>
		<td>

                    <input type="text" name="text_select_critere" id="text_select_critere" style="float:left" onkeyup="request(readData)"/><br />

		</td>
		</tr>
		<tr>
		  <td>
		    <label for="Tel">
		      <b>T&eacute;l&eacute;phone&nbsp;: </b>
		    </label>
		  </td>
		  <td>
		    <input name="Tel" id="Tel" type="text" value="<?php echo $Tel ?>" size="10" maxlength="10" />
		  </td>
		</tr>
		<tr>
		  <td>
		    <label for="Tel">
		      <b>Adresse&nbsp;: </b>
		    </label>
		  </td>
		  <td>
		    <input name="Adresse" id="Adresse" type="text" value="<?php echo $Adresse ?>" size="30" maxlength="50" />
		  </td>
		</tr>
		<tr>
		  <td>
		    <label for="Note">
		      <b>Notes :</b> 
		    </label>
		  </td>
		  <td>
		    <input name="RDV_PrisPar" type="hidden" value="<?php $user ?>" size="20" maxlength="20" />
		    <input name="Note" id="Note" type="text" value="<?php echo $Notes ?>" size="30" maxlength="100" />
		  </td>
		</tr>
	      </table>
	    </td>
	    <td>
	      <table>
		<tr>
		  <th valign="top" >
		    Répéter tous les
		  </th>
		  <td>
		    <select name="jours[]" title="options multiples possibles (touche Ctrl ou Maj)" multiple="multiple" size="7">
		      <option>
			Lundi
		      </option>
		      <option>
			Mardi
		      </option>
		      <option>
			Mercredi
		      </option>
		      <option>
			Jeudi
		      </option>
		      <option>
			Vendredi
		      </option>
		      <option>
			Samedi
		      </option>
		      <option>
			Dimanche
		      </option>
		    </select>
		  </td>
		</tr>
		<tr>
		  <th colspan="2" align="center">
		    Période
		  </th>
		</tr>
		<tr>
		  <th>
		    D&eacute;but : 
		  </th>
		  <td>
		    <input name="jour_debut_plage" type="text" value="<?php echo $date; ?>" size="10" maxlength="10"/>
		    <input type="image" src="pics/calendar.png" alt="calendrier" onclick="return getCalendar(document.forms['form_jour'].jour_debut_plage);" />
		  </td>
		</tr>
		<tr>
		  <th>
		    Fin : 
		  </th>
		  <td>
		    <input name="jour_fin_plage" type="text" value="<?php echo $date; ?>" size="10" maxlength="10" />
		    <input type="image" src="pics/calendar.png" alt="calendrier" onclick="return getCalendar(document.forms['form_jour'].jour_fin_plage);" />
		  </td>
		</tr>
		<tr>
		  <th colspan="2" align="center" >
		    Plage horaire
		  </th>
		</tr>
		<tr>
		  <th>
		    <label for="Heure_debut_plage">
		      Heure de début : 
		    </label>
		  </th>
		  <td>
		    <input name="Heure_debut_plage" id="Heure_debut_plage" type="text" value="" size="2" maxlength="2" />
		    <input name="Minutes_debut_plage" type="text" value="00" size="2" maxlength="2" />
		  </td>
		</tr>
		<tr>
		  <th>
		    <label for="Heure_fin_plage">
		      Heure de fin : 
		    </label>
		  </th>
		  <td>
		    <input name="Heure_fin_plage" id="Heure_fin_plage" type="text" value="" size="2" maxlength="2" />
		    <input name="Minutes_fin_plage" type="text" value="00" size="2" maxlength="2" />
		  </td>
		</tr>
		<tr>
		  <th>
		  </th>
		  <td>
		    <input name="bouton_envoyer" id="Heure" type="submit" value="Répéter" />
		  </td>
		</tr>
	      </table>
	    </td>
	  </tr>
	</table>
	</fieldset>
      </form>
   </div>
<?php
include("inc/footer.php");
?>