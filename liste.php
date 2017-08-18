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
$sql_droit_admin=$pdo->prepare('SELECT Droits,GUID FROM Personnes WHERE Login=?');
$sql_droit_admin->bindValue(1, $user, PDO::PARAM_STR);
$sql_droit_admin->execute();
$ligne_droit_admin=$sql_droit_admin->fetch(PDO::FETCH_ASSOC); //un seul suffit
$sql_droit_admin->closeCursor();

$user_GUID=$ligne_droit_admin['GUID'];

$auth=1;
//On verifie que l'utilisateur peut voir la liste
if (!preg_match("`plv`",$ligne_droit_admin["Droits"])) //si l'utilisateur n'a pas les droits directs de voir la liste des patients, on verifie les droits delegues
{
  $auth=0;
  $sql_sign=$pdo->prepare('SELECT Droits,GUID FROM Personnes WHERE Login=?');
  $sql_sign->bindValue(1, $sign, PDO::PARAM_STR);
  $sql_sign->execute();
  $ligne_sign=$sql_sign->fetch(PDO::FETCH_ASSOC); //un seul suffit
  $sql_sign->closeCursor();
  
  $sign_GUID=$ligne_sign['GUID'];
  $sign_droits=$ligne_sign['Droits'];
  //On cherche s'il a des droits delegues par n'importe quel utilisateur signataire
  if (preg_match("`plv`",$sign_droits)) //on verifie d'abord que le signataire a les droits
  {
  //On cherche si le delegue a les droits
    $sql_droits_delegues=$pdo->prepare('SELECT FriendUserDroits FROM user_perms WHERE SignataireGUID=? AND FriendUserGUID=?');
    $sql_droits_delegues->bindValue(1, $sign_GUID, PDO::PARAM_STR);
    $sql_droits_delegues->bindValue(2, $user_GUID, PDO::PARAM_STR);
    $sql_droits_delegues->execute();
    $ligne_droits_delegues=$sql_droits_delegues->fetch(PDO::FETCH_ASSOC); //un seul suffit
    $sql_droits_delegues->closeCursor();
        
    if (preg_match("`plv`",$ligne_droits_delegues['Droits'])) //Si le delegue a le droit 
    {
      $auth=1;
    }
  }
}
if ($auth==0)
{
  header ('location:index.php?message=message4');
  exit;
}
//$date_format='fr';//other possible value =en or nothing
//vient de config.php
$format_date['fr']='JJ-MM-AAAA';
$format_date['en']='MM-JJ-AAAA';
$format_date['iso']='AAAA-MM-JJ';

$text_select_critere="";
$critere="";
$prenom="";
$nom="";
$Tel="";

$exact='';

if (isset($_GET['text_select_critere']))
  $text_select_critere=$_GET['text_select_critere'];
  
if (isset($_GET['select_critere']))
  $critere=$_GET['select_critere'];
  
if (isset($_GET['envoyer']))
  $envoyer=$_GET['envoyer'];
if (isset($_GET['Prenom']))
  $prenom=$_GET['Prenom'];
  
if (isset($_GET['Nom']))
  $nom=$_GET['Nom'];
  
if (isset($_GET['Tel']))
  $Tel=$_GET['Tel'];
  
if (isset($_GET['exact']))
  $exact=$_GET['exact'];

$count='';
$i=0;

$tableau_criteres['FchPat_NomFille']="Nom de jeune fille";
$tableau_criteres['FchPat_Adresse']="Adresse";
$tableau_criteres['FchPat_Ville']="Ville";
$tableau_criteres['FchPat_CP']="Code Postal";
$tableau_criteres['FchPat_NumSS']="Num&eacute;ro de s&eacute;cu";
$tableau_criteres['FchPat_Nee']="Date de naissance";
$tableau_criteres['FchPat_Profession']="Profession";
$tableau_criteres['FchPat_Tel1']="T&eacute;l&eacute;phone";

include("inc/header.php");

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
?>
    <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen" />
    
       <script type="text/javascript">
//<![CDATA[
function dossierPatient() //lancer un dossier patient avec le deroulant AJAX
{
  var guid=document.forms['form_choix_patient'].elements['GUID'].value;
  location.href="frame_patient.php?GUID="+guid;
}
//]]>
  </script>
    
  <script type="text/javascript">
//<![CDATA[
function  fillFields()
{
  var nomprenom=document.getElementById("select_patient").value.split('_');
  var GUID=nomprenom[0];
  var nom=nomprenom[1];
  var prenom=nomprenom[2];
  document.forms['form_choix_patient'].elements['Nom'].value=nom;
  document.forms['form_choix_patient'].elements['Prenom'].value=prenom;
  document.forms['form_choix_patient'].elements['GUID'].value=GUID;
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
  var select_critere=document.getElementById("select_critere").value; 
  var text_select_critere=document.getElementById("text_select_critere").value; 
  var exact_match="no";

  if (document.forms['form_choix_patient'].elements["exact"].checked == true)
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
  document.forms['form_choix_patient'].select_patient.length=0;
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
	document.forms['form_choix_patient'].select_patient.options[document.forms['form_choix_patient'].select_patient.options.length] = new Option(optionDisplay,selectValue); 
      }
    }
    document.getElementById("button_access").style.display = "inline";
    document.getElementById("button_fill").style.display = "inline";
  }
  else //on n'a pas de resultats, alors on efface les boutons 
  {
    document.getElementById("button_access").style.display = "none";
    document.getElementById("button_fill").style.display = "none";
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
function showButton(name)
{
  document.getElementById(name).style.display = 'block';
}
//]]>
    </script>

    <script type="text/javascript">
//<![CDATA[
function verif_champ(champ)
{
  if (champ == "")
  { 
    alert("Le champ n'est pas rempli\nMettez le signe % si vous voulez vraiment afficher tout le fichier");
    return false;
  }
  return true;
}
//]]>
    </script>

<?php
if (isset($_REQUEST['critere_recherche'])) //pas besoin de calendrier tant qu'on n'a rien recherche
{
  include 'calendar_javascript.php';
}
?>
    <style type="text/css"  media="print">
	td:nth-child(1) { display: none; }  
	td:nth-child(7) { display: none; }  
	td:nth-child(8) { display: none; }  
	th:nth-child(1) { display: none; }  
	th:nth-child(7) { display: none; }  
	th:nth-child(8) { display: none; }  
    </style>
    <title>
      Dossiers patients - Utilisateur <?php echo $_SESSION['login'] ?>
    </title>
  </head>
	
  <body style="font-size:<?php echo $fontsize; ?>pt"  onload="donner_focus('Nom')">
    <div class="conteneur">
<?php	
// insertion du menu d'en-tete	
$anchor='Fichier_des_patients';
include("inc/menu-horiz.php");	
$message['message_doublon']="Ce dossier ne peut être importé car il existe déjà";

if (isset($_REQUEST['message']))
{
  $ce_message=$_REQUEST['message'];
  echo $message[$ce_message];
}
?>
  <div class="groupe">
      <h1>CHNP - Patients</h1>
	<div class='noPrint'>
	<div class="tableau">
	  <table> 
	    <tr>
	      <td>

		<form id="form_choix_patient" action="" method="get" >
		  <fieldset class="fieldset_formu">
		    <legend>
		      Crit&egrave;res de choix
		    </legend>
<?php 
		if ($critere=='FchPat_Nee') {
?>
<!-- 			<input name="text_select_critere" id="datepicker" type="text" value="<?php echo stripslashes($text_select_critere) ?>" size="17" maxlength="10"/> -->
<?php
		}
		else {
?>
			<!--<input name="text_select_critere" id="text_select_critere" type="text" value="<?php echo stripslashes($text_select_critere) ?>" size="17" title="L'information à chercher. Ne fait pas de différence majuscule/minuscule ni caractère accentué ou non. Refuse la requête si laissé vide. Mettre juste % pour afficher tout le fichier (déconseillé)" />-->
<?php
		}

?>
                    <label for="Nom" style="width:70px;float:left;clear: both;">
                      <strong>Nom&nbsp;: </strong>
                    </label>
                    <input name="Nom" id="Nom" type="text" value="<?php echo str_replace('%','',$nom) ?>" size="20" maxlength="40" onchange="change_status(this.value)" style="float:left" onkeyup="request(readData);" /><br />
                    <label for="exact" title="Ne cochez que si vous voulez restreindre la recherche exactement à ce qui se trouve dans la zone Nom" >
                      Exact
                    </label>
                    <input type="checkbox" name="exact" id="exact" <?php if ($exact == "on") echo "checked=\"true\"" ?> onclick="request(readData)"/> 
                    <input name="GUID" id="GUID" type="hidden" value="<?php echo str_replace('%','',$GUID) ?>"/> <!--readonly="readonly" -->
                    <label for="Prenom" style="width:70px;float:left;clear: both;">
                      <strong>Pr&eacute;nom&nbsp;: </strong>
                    </label>
                    <input name="Prenom" id="Prenom" type="text" value="<?php echo str_replace('%','',$prenom) ?>" size="20" maxlength="40" style="float:left" onkeyup="request(readData);"/><br />
                    <select name="select_critere" id="select_critere" style="float:left;clear: both;" onchange="request(readData)">
                      <option value="0">
                        critère de recherche
                      </option>
                      <option value="FchPat_NomFille" <?php if ($critere=='FchPat_NomFille') echo 'selected="selected"' ?>>
                        Nom de jeune fille
                      </option>
                      <option value="FchPat_Adresse" <?php if ($critere=='FchPat_Adresse') echo 'selected="selected"' ?>>
                        Adresse
                      </option>
                      <option value="FchPat_Ville" <?php if ($critere=='FchPat_Ville') echo 'selected="selected"' ?>>
                        Ville
                      </option>
                      <option value="FchPat_CP" <?php if ($critere=='FchPat_CP') echo 'selected="selected"' ?>>
                        Code Postal
                      </option>
                      <option value="FchPat_NumSS" <?php if ($critere=='FchPat_NumSS') echo 'selected="selected"' ?>>
                        Num&eacute;ro de s&eacute;cu
                      </option>
                      <option value="FchPat_Nee" <?php if ($critere=='FchPat_Nee') echo 'selected="selected"' ?>>
                        Date de naissance
                      </option>
                      <option value="FchPat_Profession" <?php if ($critere=='FchPat_Profession') echo 'selected="selected"' ?>>
                        Profession
                      </option>
                      <option value="FchPat_Tel1" <?php if ($critere=='FchPat_Tel1') echo 'selected="selected"' ?>>
                        T&eacute;l&eacute;phone
                      </option>
                    </select>
                    <input type="text" name="text_select_critere" id="text_select_critere" style="float:left" value="<?php echo $text_select_critere ?>" onkeyup="request(readData)"/><br />

                    <select name="select_patient" id="select_patient" style="float:left;clear: both;" onchange="fillFields();dossierPatient();">
                      <option></option>
		    </select><br />
                    <input type="button" id="button_fill" value="Remplir les champs" onclick="fillFields()" style="float:left;clear: both;display:none;"/>
		    <input type="button" value="Choisir un patient" onclick="request(readData);" style="float:left;"/>
		    <input type="button" value="Vider" onclick="vide_form()"  style="float:left"/>
	
                      <input name="button_access" id="button_access" type="button" value="Accéder au dossier" onclick="fillFields();dossierPatient();" style="float:left;clear: both;display:none;" />
			
			
			
		    </fieldset>

		    <fieldset class="fieldset_formu">
		      <legend >
			Affichage du résultat
		      </legend>			
			  <input name="envoyer" type="submit" value="Chercher" title="Pour chercher les dossiers patients" />
			
<?php
//Recherche si l'utilisateur a des droits de resolution des doublons

if (!preg_match("`adr`",$ligne_droit_admin["Droits"])) //si l'utilisateur n'a pas les droits directs de resolution de doublons, on verifie les droits delegues
{
  $sql_sign=$pdo->prepare('SELECT Droits,GUID FROM Personnes WHERE Login=?');
  $sql_sign->bindValue(1, $sign, PDO::PARAM_STR);
  $sql_sign->execute();
  $ligne_sign=$sql_sign->fetch(PDO::FETCH_ASSOC); //un seul suffit
  $sql_sign->closeCursor();
    
  $sign_GUID=$ligne_sign['GUID'];
  $sign_droits=$ligne_sign['Droits'];
  //On cherche s'il a des droits delegues par n'importe quel utilisateur signataire
  if (preg_match("`adr`",$sign_droits)) //on verifie d'abord que le signataire a les droits
  {
  //On cherche si le delegue a les droits
    $sql_droits_delegues=$pdo->prepare('SELECT FriendUserDroits FROM user_perms WHERE SignataireGUID=? AND FriendUserGUID=?');
    $sql_droits_delegues->bindValue(1, $sign_GUID, PDO::PARAM_STR);
    $sql_droits_delegues->bindValue(2, $user_GUID, PDO::PARAM_STR);
    $sql_droits_delegues->execute();
    $ligne_droits_delegues=$sql_droits_delegues->fetch(PDO::FETCH_ASSOC); //un seul suffit
    $sql_droits_delegues->closeCursor();
    
    if (preg_match("`adr`",$ligne_droits_delegues['Droits'])) //Si le delegue a le droit adr, on affiche le bouton selectionner
    {
?>
			  <input name="envoyer" type="submit" value="Sélectionner" title="Permet d'afficher une coche devant chaque dossier afin d'opérer des opérations concernant plusieurs fiches" />
			
		      </fieldset>
<?php
    }
  }
}
else //si oui, on affiche le bouton de selection multiple
{
?>
			  <input name="envoyer" type="submit" value="Sélectionner" title="Permet d'afficher une coche devant chaque dossier afin d'opérer des opérations concernant plusieurs fiches" />
		      </fieldset>
			</form>
				
<?php
}
?>  
		  </td>
		</tr>
	   </table>
	</div>
    </div>
 </div>
 
  <div class="groupe">
      <h1>Création de dossier</h1>
      <div class='noPrint'>
      <div class="tableau">
        <table> 
          <tr>
            <td>

              <form action="nouveau_dossier.php" method="post">
                <fieldset class="fieldset_formu">
                  <legend >
                    Création à partir d'un formulaire vierge
                  </legend>
                  <input name ="critere_recherche" type="hidden" value="<?php echo $critere ?>" />
                  <input name ="cle" type="hidden" value="<?php echo $text_select_critere ?>" />
                  <input name ="Prenom" type="hidden" value="<?php echo $prenom ?>" />
                  <input name ="Nom" type="hidden" value="<?php echo $nom ?>" />
                  <input name ="Tel" type="hidden" value="<?php echo $Tel ?>" />
                  <input name="envoyer" type="submit" value="Nouveau" title="Attention ! Pas de contrôle de doublons."/>
                </fieldset>
              </form>

              <form action="import.php" method="post" enctype="multipart/form-data" title="Choisissez un fichier xml à importer, puis validez. Sert à créer une fiche patient à partir d'une fiche existant sur un autre ordinateur. Ne peut se faire qu'une seule fois pour un patient donné.">
                <fieldset class="fieldset_formu">
                  <legend>
                    Importation de fiche patient MedinTux
                  </legend>
                  <input type="file" name="selection" size="17"  onchange="showButton('button_place')" />
                  <div id="button_place" style="display:none"> 
                    <input name="envoyer" type="submit" value="Importer" title="Importer un dossier à partir d'un fichier XML de MedinTux"/>
                  </div>
                </fieldset>
              </form>

              </td>
            </tr>
          </table>
          </div>
      </div>
    </div>
<?php
function display_line($envoyer,$guid,$titre,$nom,$prenom,$naissance,$adresse,$tel,$debut,$i,$prmikey)
{
?>
	  <tr>
<?php
  if ($envoyer=="Sélectionner") //on n'affiche pas les coches si mode liste
  {
?>
	    <td class="fond_td">
	      <input name="selection_patient[]" type="checkbox" value="<?php echo $guid ?>" title="Cochez toutes les fiches concernant le même patient, puis validez. Vous pourrez alors les fondre en une seule fiche contenant toutes les informations." />
	    </td>
<?php
  }
  if ($envoyer=="Chercher") //On n'affiche pas l'agenda si mode coches
  {
?>
	    <td class="fond_td" title="Le bouton Rendez-vous permet d'aller en mode de cr&eacute;ation de rendez-vous, avec le fiche pr&eacute;remplie. Il est pr&eacute;f&eacute;rable de donner le date ici, afin de pouvoir contr&ocirc;ler les plages horaires disponibles cette journ&eacute;e-l&agrave;" style="margin:0;padding:0;" >
	      <form action="agenda.php" method="get" id="form_jour<?php echo $i?>"><!--Bouton de prise de rendez-vous -->
		<div>
		  <input name="from_page" type="hidden" value="liste" />
		  <input name="GUID" type="hidden" value="<?php echo $guid?>" />
		  <input name="Nom" type="hidden" value="<?php echo $nom ?>" />
		  <input name="nom" type="hidden" value="%" />
		  <input name="Prenom" type="hidden" value="<?php echo $prenom?>" />
		  <input name="Tel" type="hidden" value="<?php echo $tel?>" />
		</div>	
		<div class='noPrint'>
		    <input name="envoyer" type="submit" value="Rendez-vous" />
		    <input name="debut" type="hidden" value="<?php echo $debut; ?>" />
		    <input name="fin" type="hidden" value="<?php echo $debut; ?>"/>
		</div>
	      </form>
	    </td>
<?php
  } //fin si mode liste
?>	
	    <td class="fond_td">
	      <a href="frame_patient.php?GUID=<?php echo $guid ?>"><?php echo stripslashes($titre)." ". stripslashes($nom) ?></a>
	    </td>
	    <td class="fond_td">
	      <?php echo stripslashes($prenom) ?>
	    </td>
	    <td class="fond_td">
	      <?php echo $naissance?>
	    </td><!--Date de naissance -->
	    <td class="fond_td">
	      <?php echo str_replace ("\n","<br />",stripslashes($adresse))?>
	    </td>
	    <td class="fond_td">
	      <?php echo $tel ?>
	    </td>
<?php
  if ($envoyer=="Chercher") //On n'affiche pas les boutons si mode coches
  {
?>
	    <td class="fond_td"  style="margin:0;padding:0;">
	      <form action="nouveau_dossier.php" method="get" ><!--Les boutons de modification et suppression -->
		<div>
		  <input name="ID" type="hidden" value="<?php echo $prmikey ?>" />
		  <input name="Nee" type="hidden" value="<?php echo $naissance ?>" />
		</div>
		<div class='noPrint'>
		 <input name="envoyer" type="submit" value="Modifier" title="permet non seulement de modifier les donn&eacute;es administratives du patient, mais aussi de cr&eacute;er une nouvelle fiche &agrave; partir de la fiche s&eacute;lectionn&eacute;e (membre de la famille, par exemple)" /> 
		 <input name="envoyer" type="submit" value="Supprimer" />
		</div>
	      </form>
	    </td>
<?php
  }
?>
	  </tr>
<?php
} //fin function display line

if (isset($envoyer))
{
//la requete si nom ou prenom
//  if ($critere=="FchGnrl_NomDos" OR $critere=="FchGnrl_Prenom")
  if ($critere=="0")
  {
    if ($exact!="on" AND strlen($nom)>2)
      $nom='%'.$nom.'%'; //on complete le nom
   
    $sql_index=$pdo->prepare("SELECT * FROM IndexNomPrenom WHERE FchGnrl_NomDos LIKE ? AND FchGnrl_Prenom LIKE ?");
    $sql_index->bindValue(1, $nom, PDO::PARAM_STR);
    $sql_index->bindValue(2, $prenom.'%', PDO::PARAM_STR);
  }
  elseif ($critere=="FchPat_NomFille" OR $critere=="FchPat_Adresse" OR $critere=="FchPat_Ville" OR $critere=="FchPat_CP" OR $critere=="FchPat_NumSS" OR $critere=="FchPat_Nee" OR $critere=="FchPat_Profession" OR $critere=="FchPat_Tel1")
//la requete si autre critere de recherche
  {
  //conversion format de date de naissance si pas iso
    if ($critere=="FchPat_Nee")
    {
      $list_date=explode ("-",$text_select_critere);
      if ($date_format=='fr')
      {
      //on repasse en iso les dates qui arrivent en francais
	$text_select_critere=$list_date[2].'-'.$list_date[1].'-'.$list_date[0];
      }
      elseif ($date_format=='en')
	$text_select_critere=$list_date[2].'-'.$list_date[0].'-'.$list_date[1];
    //si iso,on garde pareil
    }
 //   if ($exact!=1) //correspondance exacte
    if (strlen($text_select_critere)>2) //pas de joker pourles cles courtes
    {
      $text_select_critere='%'.$text_select_critere.'%';
    }
    if ($critere=="FchPat_Nee")
      $text_select_critere=$text_select_critere.' 00:00:00'; //on ajoute les minutes pour corresponcance exacte
    $sql_index=$pdo->prepare("SELECT * FROM fchpat WHERE $critere LIKE ? ");
    $sql_index->bindValue(1, $text_select_critere, PDO::PARAM_STR);

  } //fin autres criteres recherche
  
  $sql_index->execute();
  $ligne_all=$sql_index->fetchAll(PDO::FETCH_ASSOC); 
  $sql_index->closeCursor();
  $count=count($ligne_all);
  
  $pluriel="";
  if ($count>1) //Si on trouve plusieurs resultats pour cette requete
  {
    $pluriel="s";
  }
  if ($count=="")
    $nombre="aucun";
  else
    $nombre=$count;

//  if ($count) //affichage du tableau seulement si résultats
  {
?>

<?php
if ($envoyer=="Sélectionner")
{
?>
	<form action="merge.php" method="get">
<?php
}
?>
   <div class="groupe">
      <a name="resultat"></a><h1>Recherche sur <strong><?php if ($critere=="0" )echo $nom.' '.$prenom; else echo $text_select_critere ?></strong> selon <strong><?php if ($critere=="0") echo "Nom - Prénom"; else echo $tableau_criteres[$critere] ?></strong> : <?php echo $nombre ?> r&eacute;sultat<?php echo $pluriel ?></h1>
	<div class="tableau">
	  <table>
	    <col /><col /><col /><col /><col /><col /><col /><col />
	    <tr>	
<?php
    if ($envoyer=="Sélectionner") //on n'affiche pas les coches si mode liste
    {
?>
	    <th class="fond_th">
	      <input name="bouton_valider_coches" type="submit" value="Valider" />
	    </th>
<?php
    } //fin mode coches
    if ($envoyer=="Chercher") //On n'affiche pas l'agenda si mode coches
    {
?>
	    <th class="fond_th">
	      <div class='noPrint'>
		Agenda
	      </div>
	    </th>
<?php
    } //fin mode boutons
?>
	    <th class="fond_th">
	      Nom
	    </th>
	    <th class="fond_th">
	      Pr&eacute;nom
	    </th>
	    <th class="fond_th">
	      Naissance
	    </th>
	    <th class="fond_th">
	      Adresse
	    </th>
	    <th class="fond_th">
	      Téléphone
	    </th>
<?php
    if ($envoyer=="Chercher") //On n'affiche pas les boutons modifier et supprimer si mode coches
    {
?>
	    <th colspan="2" class="fond_th">
	      <div class='noPrint'>
		Dossier
	      </div>
	    </th>
<?php
    }
?>
	  </tr>
    <?php
    }
//    if ($critere=="FchGnrl_NomDos" OR $critere=="FchGnrl_Prenom")
    if ($critere=="0")
    {
      $i=0;
      $sql2=$pdo->prepare('SELECT * FROM fchpat WHERE FchPat_GUID_Doss=?');
      foreach ($ligne_all AS $ligne)
      {
	$i++;

	$sql2->bindValue(1, $ligne["FchGnrl_IDDos"], PDO::PARAM_STR);
        $sql2->execute();
        $ligne2=$sql2->fetch(PDO::FETCH_ASSOC); //un seul suffit
        
  //formatage de la date de naissance
	$table_naissance_full=explode (" ",$ligne2["FchPat_Nee"]);
	$naissance='';
	if (strpos($table_naissance_full[0],'-')) //eviter de traiter les dates de naissance vides
	  $naissance=iso_to_local($table_naissance_full[0],$date_format);
	$debut=iso_to_local(date('Y-m-d', date('U')),$date_format);

	//affichage des lignes de resultat
	  //tableau
	display_line($envoyer,$ligne["FchGnrl_IDDos"],$ligne2["FchPat_Titre"],$ligne["FchGnrl_NomDos"],$ligne["FchGnrl_Prenom"],$naissance,$ligne2["FchPat_Adresse"],$ligne2["FchPat_Tel1"],$debut,$i,$ligne["ID_PrimKey"]);
      } //fin foreach analyse resultats
      $sql2->closeCursor();
    } //fin if nomprenom
    elseif ($critere=="FchPat_NomFille" OR $critere=="FchPat_Adresse" OR $critere=="FchPat_Ville" OR $critere=="FchPat_CP" OR $critere=="FchPat_NumSS" OR $critere=="FchPat_Nee" OR $critere=="FchPat_Profession" OR $critere=="FchPat_Tel1")
//la requete si autre critere de recherche : on remonte de fchpat vers IndexNomPrenom
    {
      $sql2=$pdo->prepare('SELECT * FROM IndexNomPrenom WHERE FchGnrl_IDDos=?  ORDER BY FchGnrl_NomDos,FchGnrl_Prenom');
      foreach ($ligne_all AS $ligne)
      {
	$table_naissance_full=explode (" ",$ligne["FchPat_Nee"]);
	$naissance='';
	if (strpos($table_naissance_full[0],'-')) //eviter de traiter les dates de  naissance vides
	  $naissance=iso_to_local($table_naissance_full[0],$date_format);
	$debut=iso_to_local(date('Y-m-d', date('U')),$date_format);

	$sql2->bindValue(1, $ligne["FchPat_GUID_Doss"], PDO::PARAM_STR);
        $sql2->execute();
        $ligne2=$sql2->fetch(PDO::FETCH_ASSOC); //un seul suffit

        if ($critere=="FchPat_NomFille") //pas d'affichage si pas de nom de jeune fille trouve. Valable pour recherche sur %.
	{
	  if ($ligne["FchPat_NomFille"])
	    display_line($envoyer,$ligne["FchPat_GUID_Doss"],$ligne["FchPat_Titre"],$ligne2["FchGnrl_NomDos"],$ligne2["FchGnrl_Prenom"],$naissance,$ligne["FchPat_Adresse"],$ligne["FchPat_Tel1"],$debut,$i,$ligne2["ID_PrimKey"]);
	}
	else
	  display_line($envoyer,$ligne["FchPat_GUID_Doss"],$ligne["FchPat_Titre"],$ligne2["FchGnrl_NomDos"],$ligne2["FchGnrl_Prenom"],$naissance,$ligne["FchPat_Adresse"],$ligne["FchPat_Tel1"],$debut,$i,$ligne2["ID_PrimKey"]);
      } //fin foreach
      $sql2->closeCursor();
    }
} //fin envoyer

if ($count)
{
?>
    </table>
   </div>
 </div>
<?php
  if ($envoyer=="Sélectionner") //fin du formulaire des coches
  {
    echo "
      </form>";
  }
}

include("inc/footer.php");
?>