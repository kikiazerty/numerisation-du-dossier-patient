<?php
session_start() ;
//Page fille de l'agenda destinee a choisir un patient pour attribuer un rendez-vous
include("config.php");
include("inc/header.php");
if ( !isset( $_SESSION['login'] ) )
{
  header('location: index.php?page=liste' );
  exit;
}

try {
    $strConnection = 'mysql:host='.$host.';dbname='.$base; 
    $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); 
    $pdo = new PDO($strConnection, $loginbase, $pwd, $arrExtraParam); //Ligne 3; Instancie la connexion
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Ligne 4
}
catch(PDOException $e) {
    $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
    die($msg);
}

$from=""; //transmis par le bouton reattribuer de consultation.php pour choisir le patient a qui reattribuer un document

if (isset($_GET['from']))
{
  $from=$_GET['from'];
}

$Prenom='';
if (isset($_GET['Prenom']))
  $Prenom='%'.$_GET['Prenom'].'%';
$Nom='';
if (isset($_GET['Nom']))
  $Nom='%'.$_GET['Nom'].'%';
$DNaiss='';
if (isset($_GET['DNaiss'])) //format local
  $DNaiss=$_GET['DNaiss'];

if ($date_format=='fr')
{
//on repasse en iso les dates qui arrivent en francais
  $format='jj-mm-aaaa';
}
elseif ($date_format=='en')
  $format='mm-jj-aaaa';
else
  $format='aaaa-mm-jj';

include 'calendar_javascript.php';

?>
    <title>
      Choix de patient - Utilisateur <?php echo $_SESSION['login'] ?>
    </title>
    <script type="text/javascript">
<!--
function donner_focus(chp)
{
  document.getElementById(chp).focus();
}
-->
    </script>

    <script type="text/javascript">
//<![CDATA[
function choisir(nom,prenom,tel,GUID,adresse)
// on affecte la valeur (.value) dans :
// window.opener : la fenêtre appelante (celle qui a fait la demande)
// .document : son contenu
// .forms_x : le formulaire nomme
// .le champ 
// les valeurs attribuees proviennent du formulaire plus bas
{ 
  window.opener.document.forms['form_jour'].Nom.value = nom;
  window.opener.document.forms['form_jour'].Prenom.value = prenom;
  window.opener.document.forms['form_jour'].Tel.value = tel;
  window.opener.document.forms['form_jour'].Adresse.value = adresse;
  window.opener.document.forms['form_jour'].GUID.value = GUID;
  // on se ferme
  self.close(); 
}
//]]>
    </script>


    <script type="text/javascript">
//<![CDATA[
function choisir_guid(nom,prenom,GUID) //pour la reattribution dans consultation.php
// on affecte la valeur (.value) dans :
// window.opener : la fenêtre appelante (celle qui a fait la demande)
// .document : son contenu
// .forms_x : le formulaire nomme
// .le champ 
// les valeurs attribuees proviennent du formulaire plus bas
{ 
  window.opener.document.forms['form_jour'].numeroID.value = GUID;
  window.opener.document.forms['form_jour'].Nom.value = nom;
  window.opener.document.forms['form_jour'].Prenom.value = prenom;
  window.opener.document.getElementById("submit_confirm").style.display = "inline"; //pour afficher le bouton de confirmation
  // on se ferme
  self.close(); 
}
//]]>
    </script>


  </head>
	
  <body style="font-size:<?php echo $fontsize; ?>pt"  onload="donner_focus('Nom')">
    <div class="conteneur">
    <div class="groupe">
      <h1>MedWebTux - Patients</h1>

<?php

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
  if (count(explode ("-",$date))==3) //ne traiter que dates valides
  {
    $list_date=explode ("-",$date);
    if ($date_format=='fr')
    {
    //on repasse en iso les dates qui arrivent en francais
      $date=$list_date[2].'-'.$list_date[1].'-'.$list_date[0];
    }
    elseif ($date_format=='en')
      $date=$list_date[1].'-'.$list_date[2].'-'.$list_date[0];
  return $date; //pas de changement si iso
  }
else
  return 'Date inconnnue';
}

if (($Nom!="%%" OR $Prenom!="%%") OR isset($_GET['DNaiss']))
{
  if ($DNaiss) //requetes si date de naissance fournie
  {
    $DNaiss=str_replace("/","-",$DNaiss); //si mauvais format fourni
    $list_date=explode ("-",$DNaiss);

    $date_iso=local_to_iso($DNaiss,$date_format);

    $DNaiss=$date_iso." 00:00:00";

    $resultat_naissance=$pdo->prepare('SELECT FchPat_GUID_Doss FROM fchpat WHERE FchPat_Nee=?');
    $resultat_naissance->bindValue(1, $DNaiss, PDO::PARAM_STR);
    $resultat_naissance->execute();
    $liste=$resultat_naissance->fetchAll(PDO::FETCH_ASSOC);

    echo "
      Recherche sur <b> ".$_GET['DNaiss']." </b> : ".count($liste)." r&eacute;sultats";
  }
  else
  {
  //la requete si nom ou prenom
    $resultat=$pdo->prepare('SELECT * FROM IndexNomPrenom WHERE FchGnrl_NomDos LIKE ? AND FchGnrl_Prenom LIKE ? ORDER BY FchGnrl_NomDos,FchGnrl_Prenom');
    $resultat->bindValue(1, addslashes($Nom), PDO::PARAM_STR);
    $resultat->bindValue(2, addslashes($Prenom), PDO::PARAM_STR);
    $resultat->execute();
    $liste=$resultat->fetchAll(PDO::FETCH_ASSOC);

    echo "
      Recherche sur <b>".$Nom." $Prenom </b> : ".count($liste)." r&eacute;sultats";
  }
  if ($liste) //affichage du tableau seulement si résultats
  {
?>
      <br /><br />
      <table>
	<tr>	
	  <th>
	    <form action="">
	      <div>	
		<input  value="" type="text"  size="8"/>
		<input  value="Nom" type="text"  size="10"/>
		<input  value="Pr&eacute;nom" type="text"  size="10"/>
		<input  value="Naissance" type="text"  size="5"/>
		<input  value="Adresse" type="text"  size="15"/>
		<input  value="Téléphone" type="text"  size="6"/>
	      </div>
	    </form>
	  </th>
	</tr>
<?php
  }
  else //Si aucun nom n'est fourni, on affiche un message d'aide
  { 
  ?>
	Veuillez donner un nom, un prénom ou une date de naissance valide<br />
<?php
  }
  if ($DNaiss)//si date de naissance fournie
  {
    $sql_index_nom_prenom=$pdo->prepare('SELECT * FROM IndexNomPrenom WHERE FchGnrl_IDDos=?');
    $sql_fchpat=$pdo->prepare('SELECT * FROM fchpat WHERE FchPat_GUID_Doss=?');
    $i=0;
    foreach ($liste AS $ligne_naissance)
    {
      $i++;
      $GUID=$ligne_naissance['FchPat_GUID_Doss'];
      $sql_index_nom_prenom->bindValue(1, $GUID, PDO::PARAM_STR);
      $sql_index_nom_prenom->execute();
      $ligne_index_nom_prenom=$sql_index_nom_prenom->fetch(PDO::FETCH_ASSOC);

      $sql_fchpat->bindValue(1, $GUID, PDO::PARAM_STR);
      $sql_fchpat->execute();
      $ligne_fchpat=$sql_fchpat->fetch(PDO::FETCH_ASSOC);
?>
	<tr>
	  <td>
	    <form id="form_identite<?php echo $i ?>" action="" >
	      <div>
<?php
      if ($from)//transmis par le bouton reattribuer de consultation.php
      {
?>
		<input type="submit" value="Valider" name="button_valid" onclick="choisir_guid(this.form.nom.value,this.form.prenom.value,this.form.GUID.value)" />
		<input type="hidden" value="consultation" name="from" />
		<input type="hidden" value="<?php echo $ligne_index_nom_prenom['ID_PrimKey']?>" name="ID_PrimKey" />
<?php
      }
      else //Mode agenda
      {
?>
		<input type="submit" value="Rendez-vous" name="rdv" onclick="choisir(this.form.nom.value,this.form.prenom.value,this.form.tel.value,this.form.GUID.value,this.form.Adresse.value)" />
<?php
      }
?>
		<input readonly="readonly" type="text" value="<?php echo $ligne_index_nom_prenom['FchGnrl_NomDos']?>" name="nom" size='10'/> 
		<input readonly="readonly" type="text" value="<?php echo $ligne_index_nom_prenom['FchGnrl_Prenom']?>" name="prenom" size='10' />
		<input readonly="readonly" type="text" value="<?php echo $_GET['DNaiss'] ?>" size='10' />
		<input readonly="readonly" type="text" value="<?php echo str_replace ("\n","<br />",stripslashes($ligne_fchpat["FchPat_Adresse"])); echo '  '.$ligne_fchpat["FchPat_CP"].'  '.$ligne_fchpat["FchPat_Ville"] ?>" name="Adresse" size='30' />
		<input readonly="readonly" type="text" value="<?php echo $ligne_fchpat['FchPat_Tel1']?>" name="tel" size='6' />
		<input type="hidden" value="<?php echo $ligne_index_nom_prenom['FchGnrl_IDDos']?>" name="GUID" />
	      </form>
	    </div>
	  </td>
	</tr>
<?php
    }
    if (count($liste))
    {
?>
      </table>
 
<?php
    }
  }
  else //mode nom-prenom
  {
    $i=0;
    $sql_fchpat=$pdo->prepare('SELECT * FROM fchpat WHERE FchPat_GUID_Doss=?');

    foreach ($liste AS $ligne_index)
    {
      $i++;
      $no_dossier=$ligne_index["FchGnrl_IDDos"];

      $sql_fchpat->bindValue(1, $no_dossier, PDO::PARAM_STR);
      $sql_fchpat->execute();
      $ligne_fchpat=$sql_fchpat->fetch(PDO::FETCH_ASSOC);

      //formatage de la date de naissance
      $date_naissance_courte=explode (" ",$ligne_fchpat["FchPat_Nee"]); //on separe la date des heures minutes
      $date_local='0-0-0'; //au cas ou on ne trouve pas de date valide
      if (count($date_naissance_courte==2))
      {
	$date_local=iso_to_local($date_naissance_courte[0],$date_format); //on met la date restante en format local
      }
?>
	<tr>
	  <td>
	    <form id="form_identite<?php echo $i ?>" action="">
	      <div>
<?php
      if ($from)
      {
?>
		<input type="submit" value="Changer" name="button_valid" onclick="choisir_guid(this.form.nom.value,this.form.prenom.value,this.form.GUID.value)" />
		<input type="hidden" name="from" value="consultation"/>
<?php
      }
      else
      {
?>
		<input type="submit" value="Rendez-vous" name="rdv" onclick="choisir(this.form.nom.value,this.form.prenom.value,this.form.tel.value,this.form.GUID.value,this.form.Adresse.value)" />
<?php
      }
?>
		<input readonly="readonly" type="text" value="<?php echo $ligne_index['FchGnrl_NomDos']?>" name="nom" size='10'/>
		<input readonly="readonly" type="text" value="<?php echo $ligne_index['FchGnrl_Prenom']?>" name="prenom" size='10' />
		<input readonly="readonly" type="text" value="<?php echo $date_local ?>" size='10' />
		<input readonly="readonly" type="text" value="<?php echo str_replace ("\n","<br />",stripslashes($ligne_fchpat["FchPat_Adresse"])); echo '  '.$ligne_fchpat["FchPat_CP"].'  '.$ligne_fchpat["FchPat_Ville"]?>"  name="Adresse" size='25' />
		<input readonly="readonly" type="text" value="<?php echo $ligne_fchpat['FchPat_Tel1']?>" name="tel" size='6' />
		<input type="hidden" value="<?php echo $ligne_index['FchGnrl_IDDos']?>" name="GUID" />
		<input type="hidden" value="<?php echo $ligne_index['ID_PrimKey']?>" name="ID_PrimKey" />
	      </div>
	    </form>
	  </td>
	</tr>
<?php
    } //fin foreach
    if ($liste)
    {
?>
      </table>
    
<?php
    }
  } //fin mode nom
} //fin mode vide
else //Si aucun nom n'est fourni, on affiche un message d'aide
{ 
?>
      <p>Veuillez donner un nom ou un prénom valide</p>
<?php
}

?>
     <fieldset>
      <legend>Choix d'un dossier</legend>
      <table>
	<tr>
	  <td>
	    <form action="recherche_patient_agenda.php" method="get" id="form_recherche_patient">
	      <p>
		<label for="Nom">
		  <b>Nom : &nbsp;&nbsp;&nbsp;&nbsp;</b>
		</label>
		<input name="Nom" id="Nom" type="text" value="<?php echo str_replace('%','',$Nom) ?>" size="30" />
	      </p>
	      <p>
		<label for="Prenom">
		  <b>Prénom : </b>
		</label>
		<input name="Prenom" id="Prenom" type="text" value="<?php echo str_replace('%','',$Prenom) ?>" size="30" />
	      </p>
	      <p>
		Ou<br />
		<label for="DNaiss">
		  <b>Date de naissance (<?php echo $format ?>) : </b>
		</label>
		<input name="DNaiss" id="DNaiss" type="text" value="<?php if (isset($_GET['DNaiss'])) echo $_GET['DNaiss']; ?>" size="10"  onchange="form.submit()" /><input type="image" src="pics/calendar.png" alt="calendrier" onclick="return getCalendar(document.forms['form_recherche_patient'].DNaiss);" />
<?php
if ($from)
{
?>
		<input type="hidden" name="from" value="consultation"/>
<?php
}
?>

	      </p>
	      <p>
		<input name="envoyer" type="submit" value="Chercher un patient" title="La recherche s'effectue sur n'importe quelle partie du nom et du prénom ou sur la date de naissance." />
	      </p>

	    </form>
	  </td>
	</tr>
      </table>
    </fieldset>
    </div>
<?php
include("inc/footer.php");
?>