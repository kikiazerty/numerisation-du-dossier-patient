<?php
session_start() ;

if ( !isset( $_SESSION['login'] ) )
{
  header('location: index.php?page=CCAM' );
  exit;
}

include("config.php");

$baseCCAM="CCAMTest";
try 
{
    $strConnection = 'mysql:host='.$host.';dbname='.$baseCCAM; 
    $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); 
    $pdo = new PDO($strConnection, $loginbase, $pwd, $arrExtraParam); //Ligne 3; Instancie la connexion
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Ligne 4
}
catch(PDOException $e) 
{
    $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
    die($msg);
}
$request_ajouter_au_thesaurus="INSERT INTO THESAURUS (CODEMEDECIN,CATEGORIE,LIBUSUEL,CODEACTE) VALUES (? ? ? ?)";
$tab_login=explode("::",$_SESSION['login']);
$signuser=$tab_login[1];
if (isset($_REQUEST['bouton_ajouter_au_thesaurus']))
{
  $deroulant_categories=$_REQUEST['deroulant_categories'];
  if ($deroulant_categories=="Autre")
    $categorie=$_REQUEST['categorie'];
  else
    $categorie=$deroulant_categories;
  $libusuel=$_REQUEST['libusuel'];
  $acte=$_REQUEST['acte'];

  $resultat=$pdo->prepare($request_ajouter_au_thesaurus);
  $resultat->bindValue(1, $signuser, PDO::PARAM_STR);
  $resultat->bindValue(2, $categorie, PDO::PARAM_STR);
  $resultat->bindValue(3, $libusuel, PDO::PARAM_STR);
  $resultat->bindValue(4, $acte, PDO::PARAM_STR);
  $resultat->execute();
  $resultat->closeCursor();
  $resultat = NULL;
}

if (isset($_REQUEST['bouton_supprimer_thesaurus']) AND isset($_REQUEST['coche_supprimer']))
{
  $acte_a_supprimer=$_REQUEST['acte_a_supprimer'];
  $request_supprimer_thesaurus="DELETE FROM THESAURUS WHERE SERIE= ?";

  $resultat=$pdo->prepare($request_supprimer_thesaurus);
  $resultat->bindValue(1, $acte_a_supprimer, PDO::PARAM_STR);
  $resultat->execute();
  $resultat->closeCursor();
  $resultat = NULL;

}
$request_TOPOGRAPHIE1="SELECT CODE,LIBELLE FROM TOPOGRAPHIE1";

include("inc/header.php");
?>
    <title>
      MedWebTux - CCAM - Utilisateur <?php echo $_SESSION['login'] ?>
    </title>
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
function calcul_total()
{
  var valeur_acte;
  valeur_acte=parseFloat(document.getElementById('value_acte').value);
  var inputs = document.getElementsByTagName('input');
  var somme=0;
  for(var i = 1; i < inputs.length; i++) 
  {
    //rechercher les checkbox cochés
    if(inputs[i].type == 'checkbox' && inputs[i].checked) 
    {
      var uneCoche = inputs[i].value;
      var elem = uneCoche.split('|');
      operateur = elem[0];
      valeur = elem[1];
      if (operateur=='+')
	somme = somme+parseFloat(valeur);
      else if (operateur=='*')
	somme=somme+(valeur_acte*parseFloat(valeur-1));//les multiplicateurs ne doivent pas se multiplier entre eux
    }
  }		
  valeur_acte=(valeur_acte+somme).toFixed(2);
  document.getElementById('total').value=eval(valeur_acte);
}
//]]>
    </script>
  </head>

  <body style="font-size:<?php echo $fontsize; ?>pt" <?php if (isset($_REQUEST['code_ccam'])) { ?> onload="donner_focus('code_ccam')" <?php } else { ?>  onload="donner_focus('mot1')"  <?php } ?> >
    <div class="conteneur">
<?php	
// insertion du menu d'en-tete	
  $anchor="Mode_CCAM";
  include("inc/menu-horiz.php");

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

$date_jour=date('Ymd', date('U'));
$code_ccam='';

$request_version = "SELECT * FROM VERSION WHERE CHRONOLOGIE=(SELECT MAX(CHRONOLOGIE) FROM VERSION) ";
$resultat=$pdo->prepare($request_version);
$resultat->execute();
$count=$resultat->fetchAll();
$resultat->closeCursor();
$resultat = NULL;

$ccam_chrono = substr($count[0]['CHRONOLOGIE'],3, 2); 
$version_CCAM = $count[0]['DATECREATION'];
$version_CCAM = substr($version_CCAM,0,4).'-'.substr($version_CCAM,4,2).'-'.substr($version_CCAM,6,2); //On met les tirets a la date

$request_date_effet="SELECT MAX(DATE_EFFET) FROM DATES_EFFET";
$resultat_date_effet=$pdo->prepare($request_date_effet);
$resultat_date_effet->execute();
$count_date_effet=$resultat_date_effet->fetchAll();
$resultat_date_effet->closeCursor();
$resultat_date_effet = NULL;

$date_effet = substr($count_date_effet[0]['MAX(DATE_EFFET)'],0,4).'-'.substr($count_date_effet[0]['MAX(DATE_EFFET)'],4,2).'-'.substr($count_date_effet[0]['MAX(DATE_EFFET)'],6,2); //On met les tirets a la date
?>
  <div class="groupe">
    <h1>MedWebTux - CCAM-<?php echo $ccam_chrono; ?> du <?php echo iso_to_local($version_CCAM,$date_format) ?>, effet au <?php echo iso_to_local($date_effet ,$date_format); ?></h1>

    <fieldset>
<!--    <legend> Recherche par classification </legend> -->

      <form action="CCAM.php" method="get">	
	<div class="tableau">
	  <table>
	    <tr>
	      <th>Système</th>
	      <td>
		<select name="TOPOGRAPHIE1" onchange="form.submit()" >
		  <option value="">Recherche par classification</option>
<?php
$TOPOGRAPHIE1="";
if (isset ($_REQUEST['TOPOGRAPHIE1']))
  $TOPOGRAPHIE1=$_REQUEST['TOPOGRAPHIE1'];
if (isset ($_REQUEST['acte']))
  $TOPOGRAPHIE1=substr($_REQUEST['acte'], 0, 1);
$resultat_TOPOGRAPHIE1=$pdo->prepare($request_TOPOGRAPHIE1);
$resultat_TOPOGRAPHIE1->execute();
$count_TOPOGRAPHIE1=$resultat_TOPOGRAPHIE1->fetchAll();
$resultat_TOPOGRAPHIE1->closeCursor();
$resultat_TOPOGRAPHIE1 = NULL;

foreach ($count_TOPOGRAPHIE1 AS $this_TOPOGRAPHIE1)
{
?>
		  <option value="<?php echo $this_TOPOGRAPHIE1['CODE'] ?>" <?php if ($this_TOPOGRAPHIE1['CODE']==$TOPOGRAPHIE1) echo "selected='selected'"?> >
		    <?php echo $this_TOPOGRAPHIE1['CODE']." ".str_replace("&","&amp;",$this_TOPOGRAPHIE1['LIBELLE'])?>
		  </option>
<?php
}
?>
<!-- 	    <option value="Y" <?php if ("Y"==$TOPOGRAPHIE1) echo "selected='selected'"?>>Y Autres</option> -->
		</select>
<!--	  <input name="bouton_valider_topographie" type="submit" value="Envoyer" />  -->

	      </td>
	    </tr>
<?php
$request_actes="SELECT  CODE,LIBELLELONG  FROM ACTES WHERE CODE LIKE ? AND (DATECREATION < ? AND (DATEFIN > ? OR DATEFIN='00000000')) ";
$resultat_actes=$pdo->prepare($request_actes);

if ($TOPOGRAPHIE1)
{
?>
 
	    <tr>
	      <th>
		Topographie
	      </th>
	      <td>
<!-- 	  <input name="TOPOGRAPHIE1" value="<?php echo $TOPOGRAPHIE1 ?>" type="hidden" /> -->
		<select name="topographie2"  onchange="form.submit()">
		  <option>
		  </option>
<?php
  if (isset ($_REQUEST['acte'])) //on vient de l'acte
  {
    $topographie2=substr($_REQUEST['acte'], 0, 2);
  }
  else //on vient du deroulant
  {
    if (isset($_REQUEST['topographie2']))
      $topographie2=$_REQUEST['topographie2'];
    else
      $topographie2='';
  }
  $request_TOPOGRAPHIE2="SELECT CODE,LIBELLE FROM TOPOGRAPHIE2 WHERE PERE=?";
  $resultat=$pdo->prepare($request_TOPOGRAPHIE2);
  $resultat->bindValue(1, $TOPOGRAPHIE1, PDO::PARAM_STR);
  $resultat->execute();
  $count=$resultat->fetchAll();
  $resultat->closeCursor();
  $resultat = NULL;
  foreach ($count AS $this_TOPOGRAPHIE2)
  {
?>
		  <option value="<?php echo $this_TOPOGRAPHIE2['CODE'] ?>" <?php if ($topographie2==$this_TOPOGRAPHIE2['CODE']) echo "selected='selected'" ?> >
		    <?php echo $this_TOPOGRAPHIE2['CODE']." ".$this_TOPOGRAPHIE2['LIBELLE'] ?>
		  </option>
<?php
  }
?>
		</select>
	      </td>
	    </tr>
<?php
if ($topographie2)
{
?>
	    <tr>
	      <td>
		<b>Action</b>
	      </td>
	      <td>
		<select name="action1" onchange="form.submit()" >
		  <option value="">
		  </option>
<?php
  if (isset ($_REQUEST['acte']))
  {
    $action1=substr($_REQUEST['acte'], 2, 1);
  }
  elseif (isset($_REQUEST['action1']))
  {
    $action1=$_REQUEST['action1'];
  }
  else
    $action1='';
  $request_action1="SELECT CODE,VERBE FROM ACTION1";
  $resultat=$pdo->prepare($request_action1);
  $resultat->execute();
  $count=$resultat->fetchAll();
  $resultat->closeCursor();
  $resultat = NULL;

  foreach ($count AS $this_action1)
  {
    $count_actes='';
//on cherche s'il existe au moins un acte avec ces 3 premieres lettres
    $resultat_actes->bindValue(1, $topographie2.$this_action1['CODE'].'%', PDO::PARAM_STR);
    $resultat_actes->bindValue(2, $date_jour, PDO::PARAM_STR);
    $resultat_actes->bindValue(3, $date_jour, PDO::PARAM_STR);
    $resultat_actes->execute();
    $count_actes=$resultat_actes->fetchAll();

    if ($count_actes)
    {
?>
		  <option value="<?php echo $this_action1['CODE'] ?>" <?php if ($action1==$this_action1['CODE']) echo "selected='selected'"?> >
		    <?php echo $this_action1['CODE']." ".$this_action1['VERBE']; ?>
		  </option>
<?php
    }
  }
?>
		</select>
	      </td>
	    </tr>
<?php
  if ($action1)
  {
?>
	    <tr>
	      <th>
		Accès
	      </th>
	      <td>
		<select name="ACCES1">
		  <option value="">
		  </option>
<?php
    if (isset ($_REQUEST['acte'])) //pour afficher l'option dans les deroulants en venant de l'acte
    {
      $ACCES1=substr($_REQUEST['acte'], 3, 1);
    }
    elseif (isset($_REQUEST['ACCES1']))
    {
	$ACCES1=$_REQUEST['ACCES1'];
    }
    else
      $ACCES1='';
    $request_ACCES1="SELECT CODE,ACCES FROM ACCES1";
    $resultat=$pdo->prepare($request_ACCES1);
    $resultat->execute();
    $count=$resultat->fetchAll();
    $resultat->closeCursor();
    $resultat = NULL;

    foreach ($count AS $this_ACCES1)
    {
      $count_actes='';
  //on cherche s'il existe au moins un acte avec ces 4 premieres lettres
      $resultat_actes->bindValue(1, $topographie2.$action1.$this_ACCES1['CODE'].'%', PDO::PARAM_STR);
      $resultat_actes->bindValue(2, $date_jour, PDO::PARAM_STR);
      $resultat_actes->bindValue(3, $date_jour, PDO::PARAM_STR);
      $resultat_actes->execute();
      $count_actes=$resultat_actes->fetchAll();
      if ($count_actes)
	{
?>
		  <option value="<?php echo $this_ACCES1['CODE']?>" <?php if ($ACCES1==$this_ACCES1['CODE'] ) echo "selected='selected'" ?> >
		    <?php echo $this_ACCES1['CODE']." ".$this_ACCES1['ACCES']?>
		  </option>
<?php
      }
    }
?>
		</select>
	      </td>
	    </tr>
<?php
  } //fin if action1
?>
	    <tr>
	      <td>
		<input name="bouton_valider" type="submit" value="Valider" />
	      </td>
	    </tr>
<?php
} //fin if topographie2
?>
	  </table>
	</div>
      </form>
  </fieldset>

 <?php
  if (isset($_REQUEST['bouton_valider']) )
  {
    echo "<div class=\"information\">"; 
    $debut_ccam=$topographie2.$action1.$ACCES1;
    $debut_ccam;
    $code2long=$debut_ccam."%";

    $resultat_actes->bindValue(1, $code2long, PDO::PARAM_STR);
    $resultat_actes->bindValue(2, $date_jour, PDO::PARAM_STR);
    $resultat_actes->bindValue(3, $date_jour, PDO::PARAM_STR);
    $resultat_actes->execute();
    $count_actes=$resultat_actes->fetchAll();
    $resultat_actes->closeCursor();
    $resultat_actes = NULL;

    $resultat_histo_phase=$pdo->prepare("SELECT PU FROM HISTO_PHASE WHERE CODEACTE= ?");
    foreach($count_actes AS $this_actes)
    {
      echo "
	<br /><a href=\"CCAM.php?acte=".$this_actes['CODE']."\">".$this_actes['CODE']."</a> ".$this_actes['LIBELLELONG'];
      $ce_code=$this_actes['CODE'];
      $resultat_histo_phase->bindValue(1,$ce_code, PDO::PARAM_STR);
      $resultat_histo_phase->execute();
      $count_histo_phase=$resultat_histo_phase->fetchAll();
      echo "
  ".$count_histo_phase[0]['PU'];
    }
    $resultat_histo_phase->closeCursor();
    $resultat_histo_phase = NULL;
      echo "</div>";
  }
} //fin si TOPOGRAPHIE1
else //si pas de valeur pour le premier deroulant, on cloture le fieldset
{
?>
	</table>
      </div>
    </form>
  </fieldset>
<?php
}
//acces a l'acte complet

//recuperation variable $option_cle pour session
$_SESSION["option_cle"] ='';
if (isset($_POST["option_cle"])) 
{
  $_SESSION["option_cle"] = $_POST["option_cle"];
}

?>
    <fieldset>
<!--     <legend>Recherche par mot-cl&eacute; </legend> -->
      <table>
	<tr>
	  <td>
	      <form title="Plusieurs mots séparés par des %" action="CCAM.php" method="post">
	    <div>
		<input type="radio" name="option_cle" value="acte" onclick="javascript:submit()" checked="checked" /> <b>Recherche par code d'acte</b>
		<input type="radio" name="option_cle" value="libelle" onclick="javascript:submit()" <?php if ($_SESSION["option_cle"] == "libelle") echo 'checked="checked"' ?>  /> <b> Recherche par libell&eacute;</b><br />
		<input name="mot1" id="mot1" type="text" size="25" value="<?php if (isset($_REQUEST['mot1'])) echo $_REQUEST['mot1'] ?>" /> <!--onkeyup="form.submit()"-->
		<input name="bouton_envoyer_mots" type="submit" value="Envoyer" />
	      </div>
	    </form>
	  </td>
	</tr>
      </table>
    </fieldset>
  </div>

<?php
if (isset($_REQUEST['mot1']))
{
  $mot_joker = '%'.trim($_REQUEST["mot1"].'%');
  if ($_SESSION["option_cle"] == "libelle") 
  {
    $request_mot="SELECT CODE,LIBELLELONG FROM ACTES WHERE LIBELLELONG LIKE ? AND (DATECREATION < ? AND (DATEFIN > ? OR DATEFIN='00000000'))";
//	      	$sql_mot="SELECT * FROM ACTES WHERE LIBELLELONG LIKE '%$mot_joker%' AND (DATECREATION < '$date_jour' AND (DATEFIN > '$date_jour' OR DATEFIN='00000000'))";
  }
  else 
  {
    $request_mot="SELECT CODE,LIBELLELONG FROM ACTES WHERE CODE LIKE ? AND (DATECREATION < ? AND (DATEFIN > ? OR DATEFIN='00000000'))";
//	      	$sql_mot="SELECT * FROM ACTES WHERE CODE LIKE '$mot_joker%' AND (DATECREATION < '$date_jour' AND (DATEFIN > '$date_jour' OR DATEFIN='00000000'))";
  }
  $resultat_mot=$pdo->prepare($request_mot);
  $resultat_mot->bindValue(1, $mot_joker, PDO::PARAM_STR);
  $resultat_mot->bindValue(2, $date_jour, PDO::PARAM_STR);
  $resultat_mot->bindValue(3, $date_jour, PDO::PARAM_STR);
  $resultat_mot->execute();
  $count_mot=$resultat_mot->fetchAll();
  $resultat_mot->closeCursor();
  $resultat_mot = NULL;
?>
<div class="information">
<?php
  echo "
    Recherche sur ".$mot_joker."<br />";

//     while ($ligne_mot=mysqli_fetch_array($resultat_mot))
  foreach ($count_mot AS $this_mot)
  {
    echo "
    <br /><a href=\"CCAM.php?acte=".$this_mot['CODE']."\">".$this_mot['CODE']."</a> ".$this_mot['LIBELLELONG'];	
  }
?>
</div>
<?php
}
//La fiche de l'acte	
if (isset($_REQUEST['acte']))
{      
  $acte=$_REQUEST['acte'];
  $sexe[0]="Tous";
  $sexe[1]="Masculin";
  $sexe[2]="Féminin";
  $request_acte="SELECT * FROM ACTES WHERE CODE=?";
  $resultat_acte=$pdo->prepare($request_acte);
  $resultat_acte->bindValue(1,$_REQUEST['acte'], PDO::PARAM_STR);
  $resultat_acte->execute();
  $count_acte=$resultat_acte->fetchAll();
  $resultat_acte->closeCursor();
  $resultat_acte = NULL;
?>
	<div class="groupe">
	  <div class="tableau">
	    <table>
	      <tr>
		<th class="fond_th">
		  Acte
		</th>
		<th class="fond_th">
		  Thesaurus
		</th>
		<th class="fond_th">
		  Modificateurs
		</th>
		<th class="fond_th">
		  Associabilités
		</th>
		<th class="fond_th">
		  Incompatibilités
		</th>
	      </tr>
	      <tr>
		<td valign="top" class="fond_td">
<?php
//preparation des requetes executees avec les variables situees dans la boucle
    $request_deplacement="SELECT * FROM FRAIS_DEP WHERE CODE= ?";
    $resultat_deplacement=$pdo->prepare($request_deplacement);

    $sql_histo_phase="SELECT * FROM HISTO_PHASE WHERE CODEACTE=?";
    $resultat_histo_phase=$pdo->prepare($sql_histo_phase);

//Chercher les codes d'assurance remboursement
    $request_assurance="SELECT * FROM NAT_ASS";
    $resultat_assurance=$pdo->prepare($request_assurance);

    $sql_activite="SELECT * FROM ACTIVITE INNER JOIN ACTIVITEACTE ON ACTIVITE.CODE=ACTIVITEACTE.ACTIVITE WHERE CODEACTE= ?";
    $resultat_activite=$pdo->prepare($sql_activite);

//chercher les codes de phase
    $request_phase="SELECT * FROM PHASE INNER JOIN PHASEACTE ON PHASE.CODE=PHASEACTE.PHASE WHERE CODEACTE= ? GROUP BY PHASE";
    $resultat_phase=$pdo->prepare($request_phase);

    $request_type="SELECT * FROM TYPE_ACTE WHERE CODE= ?";
    $resultat_type=$pdo->prepare($request_type);
//chercher les notes
    $request_note="SELECT LIBELLE FROM NOTES INNER JOIN TYPENOTE ON NOTES.TYPE=TYPENOTE.CODE WHERE CODEACTE=?";
    $resultat_note=$pdo->prepare($request_note);
//POUR LE DEROULANT DES CATEGORIES	
    $request_chercher_categories="SELECT CATEGORIE FROM THESAURUS WHERE CODEMEDECIN= ? GROUP BY CATEGORIE ORDER BY CATEGORIE";
    $resultat_chercher_categories=$pdo->prepare($request_chercher_categories);

//AFFICHER AJOUTER THESAURUS
    $request_chercher_thesaurus="SELECT * FROM THESAURUS WHERE CODEMEDECIN=? AND CODEACTE=? ORDER BY CATEGORIE,LIBUSUEL";
    $resultat_chercher_thesaurus=$pdo->prepare($request_chercher_thesaurus);

    $request_afficher_modificateurs="SELECT CODEACTE, MODIFICATEURACTE.MODIFICATEUR, MAX( DATEEFFET ), FORFAIT,COEF,LIBELLE
FROM MODIFICATEURACTE
INNER JOIN MODIFICATEUR ON MODIFICATEURACTE.MODIFICATEUR=MODIFICATEUR.CODE 
INNER JOIN TB11         ON MODIFICATEURACTE.MODIFICATEUR=TB11.MODIFICATEUR 
WHERE DATEEFFET < NOW( )
AND `CODEACTE` = ?
GROUP BY MODIFICATEURACTE.MODIFICATEUR";

    $resultat_afficher_modificateurs=$pdo->prepare($request_afficher_modificateurs);

    $request_afficher_associabilite="SELECT * FROM ASSOCIABILITE WHERE CODEACTE= ? GROUP BY ACTEASSO"; 
    $resultat_afficher_associabilite=$pdo->prepare($request_afficher_associabilite);

    $request_coefficient_asso="SELECT COEFFICIENT FROM ASSOCIATION WHERE CODE= ?";
    $resultat_coefficient_asso=$pdo->prepare($request_coefficient_asso);

    $request_coefficient_asso_libelle="SELECT LIBELLE FROM ASS_NPREV WHERE CODE=?";
    $resultat_coefficient_asso_libelle=$pdo->prepare($request_coefficient_asso_libelle);

    $request_incompatibilite="SELECT * FROM INCOMPATIBILITE WHERE CODEACTE= ?";
    $resultat_incompatibilite=$pdo->prepare($request_incompatibilite);

    foreach ($count_acte AS $this_acte)
    {
      $dep=$this_acte['DEPLACEMENT'];
      $codeacte=$this_acte['CODE'];
      $ce_sexe=$this_acte['SEXE'];

      $resultat_deplacement->bindValue(1,$dep, PDO::PARAM_STR);
      $resultat_deplacement->execute();
      $count_deplacement=$resultat_deplacement->fetchAll();
 
      echo "
      <strong>".$count_acte[0]['CODE']."</strong><br />".$count_acte[0]['LIBELLELONG']."
      <br /><b>Sexe :</b> ".$sexe[$ce_sexe]."
      <br /><b>Déplacement : </b>"." ".$count_deplacement[0]['LIBELLE']; //$ligne_acte['DEPLACEMENT']." : "
      $ce_code=$this_acte['CODE'];
      $resultat_histo_phase->bindValue(1,$ce_code, PDO::PARAM_STR);
      $resultat_histo_phase->execute();
      $count_histo_phase=$resultat_histo_phase->fetchAll();

      $resultat_assurance->bindValue(1,$dep, PDO::PARAM_STR);
      $resultat_assurance->execute();
      $count_assurance=$resultat_assurance->fetchAll();

      $tableau_assurance=array();
      foreach ($count_assurance AS $this_assurance)
      {
	$tableau_assurance[$this_assurance['CODE']]=$this_assurance['LIBELLE'];
      }
      $resultat_activite->bindValue(1,$codeacte, PDO::PARAM_STR);
      $resultat_activite->execute();
      $count_activite=$resultat_activite->fetchAll();


      echo '<br /><b>Activité </b>:<br />';
      foreach ($count_activite AS $this_activite)
	echo $this_activite['CODE'].'-'.$this_activite['LIBELLE'].'<br />';

      $resultat_phase->bindValue(1,$codeacte, PDO::PARAM_STR);
      $resultat_phase->execute();
      $count_phase=$resultat_phase->fetchAll();

      echo '<br /><b>Phase </b>:<br />';

      foreach ($count_phase AS $this_phase)
	echo $this_phase['CODE'].'-'.$this_phase['LIBELLE'].'<br />';
//Les types d'actes
      $code_type=$this_acte['TYPE'];
      $resultat_type->bindValue(1,$code_type, PDO::PARAM_STR);
      $resultat_type->execute();
      $count_type=$resultat_type->fetchAll();

      echo '<br /><b>Type </b>:<br />
'.$count_type[0]['LIBELLE'].'<br />';
?>
      <br /><b>Note </b>:<br />
<?php
      $resultat_note->bindValue(1,$codeacte, PDO::PARAM_STR);
      $resultat_note->execute();
      $count_note=$resultat_note->fetchAll();

      foreach ($count_note AS $this_note)
	echo $this_note['LIBELLE'].'<br />';
?>
      <b>Assurance </b>:<ul> <?php $assurance=$this_acte['ASSURANCE1']; if (array_key_exists($assurance,$tableau_assurance)) echo "<li>{$tableau_assurance[$assurance]}</li> ";  $assurance=$this_acte['ASSURANCE2'];if (array_key_exists($assurance,$tableau_assurance))  echo "<li>{$tableau_assurance[$assurance]} </li>";  $assurance=$this_acte['ASSURANCE3']; if (array_key_exists($assurance,$tableau_assurance)) echo "<li>{$tableau_assurance[$assurance]} </li>";  $assurance=$this_acte['ASSURANCE4']; if (array_key_exists($assurance,$tableau_assurance)) echo "<li>{$tableau_assurance[$assurance]} </li>";  $assurance=$this_acte['ASSURANCE5']; if (array_key_exists($assurance,$tableau_assurance)) echo "<li>{$tableau_assurance[$assurance]} </li>"; ?></ul>
      <b>Valeur </b>: <input name='value_acte' id='value_acte' type='hidden' value='<?php echo $count_histo_phase[0]['PU'] ?>'/> <?php echo $count_histo_phase[0]['PU'] ?> €
      <br /><b>Total </b>: <input name='total' type='text' value='<?php echo $count_histo_phase[0]['PU'] ?>' id='total' />
<?php

//AFFICHER AJOUTER THESAURUS
      $resultat_chercher_thesaurus->bindValue(1,$signuser, PDO::PARAM_STR);
      $resultat_chercher_thesaurus->bindValue(2,$ce_code, PDO::PARAM_STR);
      $resultat_chercher_thesaurus->execute();
      $count_chercher_thesaurus=$resultat_chercher_thesaurus->fetchAll();
?>
		  </td>
		  <td valign="top" class="fond_td">
<?php
      if ($count_chercher_thesaurus)
      {
	echo "
      <br /><b>Existe dans le thesaurus de $signuser</b>";
//Ajouter ici un bouton supprimer ?
      }
      else //le mot n'est pas dans le thesaurus : on propose de l'ajouter
      {
//POUR LE DEROULANT DES CATEGORIES	
?>
      <form action="CCAM.php" method="get">
	<table>
	  <tr>
	    <td valign="top">
	      <b>Catégorie&nbsp;: </b>
	    </td>
	    <td>
	      <input name="acte" type="hidden" value="<?php echo $acte ?>" />
	      <select name="deroulant_categories">
		<option value="Autre">
		  Autre
		</option>
<?php
	foreach ($count_chercher_categories AS $this_categories)
	{
	  echo "
		<option value=\"".$this_categories['CATEGORIE']."\">
		  ".$this_categories['CATEGORIE']."
		</option>";
	}
?>
	      </select>
	      Si Autre, préciser&nbsp;: <input name="categorie" type="text" value="" />
	    </td>
	  </tr>
	  <tr>
	    <td>
	      <b>Libellé&nbsp;: </b>
	    </td>
	    <td>
	      <input name="libusuel" type="text" value="" />
	    </td>
	  </tr>
	  <tr>
	    <td>
	    </td>
	    <td>
	      <input name="bouton_ajouter_au_thesaurus" type="submit" value="Ajouter au thesaurus" />
	    </td>
	  </tr>
	</table>
      </form>
<?php
		}
//les modificateurs
?>
		  </td>
		  <td valign="top" class="fond_td">
<?php
	  $resultat_afficher_modificateurs->bindValue(1,$acte, PDO::PARAM_STR);
	  $resultat_afficher_modificateurs->execute();
	  $count_afficher_modificateurs=$resultat_afficher_modificateurs->fetchAll();

	  $i=0;

	  $modificateurs=array ();
	  foreach ($count_afficher_modificateurs AS $this_modificateurs)
	  {
	    $value='';
	    if ($this_modificateurs['FORFAIT'] != '0.00')
	      $value= "+"."|".$this_modificateurs['FORFAIT'];
	    if ($this_modificateurs['COEF']!='1.000')
	      $value= "*"."|".$this_modificateurs['COEF'];

	     if ($this_modificateurs['MODIFICATEUR'] != "9" && $this_modificateurs['MODIFICATEUR'] != "X" && $this_modificateurs['MODIFICATEUR'] != "O" && $this_modificateurs['MODIFICATEUR'] != "I") {
		echo "<div class=\"modificateur\"><input type='checkbox' value='$value' name='check_modif_".$i."' id='check_modif_".$i."' onclick='calcul_total()' /> ".$this_modificateurs['MODIFICATEUR']." ".$this_modificateurs['LIBELLE'];
	    } 
	    else {
	    	echo "<div class=\"modificator\"><input type='checkbox' value='$value' name='check_modif_".$i."' id='check_modif_".$i."' onclick='calcul_total()' />".$this_modificateurs['MODIFICATEUR']." ".$this_modificateurs['LIBELLE'];
	    }
	    echo '  '.str_replace("|"," ",$value);
	    echo "</div>";
	    $i++;
	  }
?>
</td>
<!-- Associabilités -->
		  <td valign="top" class="fond_td">    
		  <?php
	  $resultat_afficher_associabilite->bindValue(1,$acte, PDO::PARAM_STR);
	  $resultat_afficher_associabilite->execute();
	  $count_afficher_associabilite=$resultat_afficher_associabilite->fetchAll();

	  foreach ($count_afficher_associabilite AS $this_associabilite)
	  {
	    $asso=$this_associabilite['ACTIVITEASSO'];
	    $resultat_coefficient_asso->bindValue(1,$asso, PDO::PARAM_STR);// non-objet

	    $resultat_coefficient_asso->execute();
	    $count_coefficient_asso=$resultat_coefficient_asso->fetchAll();

	    $resultat_coefficient_asso_libelle->bindValue(1,$asso, PDO::PARAM_STR);
	    $resultat_coefficient_asso_libelle->execute();
	    $count_coefficient_asso_libelle=$resultat_coefficient_asso_libelle->fetchAll();

	    echo "
      <a href=\"CCAM.php?acte=".$this_associabilite['ACTEASSO']."\" title=\"".$count_coefficient_asso_libelle[0]['LIBELLE']." \">".$this_associabilite['ACTEASSO']."</a> x ".$count_coefficient_asso[0]['COEFFICIENT']."<br />";
	  }

?>
		  </td>
		  <td valign="top" class="fond_td">
<!-- les incompatibilites -->
<?php
	  $resultat_incompatibilite->bindValue(1,$acte, PDO::PARAM_STR);
	  $resultat_incompatibilite->execute();
	  $count_incompatibilite=$resultat_incompatibilite->fetchAll();
	  foreach($count_incompatibilite AS $this_incompatibilite)
	{
	  echo "
      <a href=\"CCAM.php?acte=".$this_incompatibilite['INCOMPATIBLE']." \">".$this_incompatibilite['INCOMPATIBLE']."</a><br />";
	}
?>
</td>
</tr>
</table>
<?php     		
    } //fin foreach
//on clot toutes les requetes qui ont ete effectuees dans la boucle
    $resultat_assurance->closeCursor();
    $resultat_assurance = NULL;
    $resultat_incompatibilite->closeCursor();
    $resultat_incompatibilite = NULL;
    $resultat_coefficient_asso->closeCursor();
    $resultat_coefficient_asso = NULL;

    $resultat_coefficient_asso_libelle->closeCursor();
    $resultat_coefficient_asso_libelle = NULL;
    $resultat_afficher_associabilite->closeCursor();
    $resultat_afficher_associabilite = NULL;
    $resultat_afficher_modificateurs->closeCursor();
    $resultat_afficher_modificateurs = NULL;
    $resultat_chercher_thesaurus->closeCursor();
    $resultat_chercher_thesaurus = NULL;
    $resultat_note->closeCursor();
    $resultat_note = NULL;
    $resultat_type->closeCursor();
    $resultat_type = NULL;
    $resultat_phase->closeCursor();
    $resultat_phase = NULL;
    $resultat_activite->closeCursor();
    $resultat_activite = NULL;
    $resultat_deplacement->closeCursor();
    $resultat_deplacement = NULL;
    $resultat_histo_phase->closeCursor();
    $resultat_histo_phase = NULL;


 echo "</div></div>";
  } //fin de si acte

//Thesaurus
    if (isset($_REQUEST['bouton_afficher_thesaurus']))
    {
	$resultat_chercher_thesaurus=$pdo->prepare("SELECT * FROM THESAURUS WHERE CODEMEDECIN= ? ORDER BY CATEGORIE,LIBUSUEL");
	$resultat_chercher_thesaurus->bindValue(1,$signuser, PDO::PARAM_STR);
	$resultat_chercher_thesaurus->execute();
	$count_chercher_thesaurus=$resultat_chercher_thesaurus->fetchAll();
	$resultat_chercher_thesaurus->closeCursor();
	$resultat_chercher_thesaurus = NULL;


     echo "<div class=\"groupe\">
      <h1>
	Thesaurus de $signuser
      </h1>";

      echo "<div class=\"tableau\">
      <table>";
      foreach ($count_chercher_thesaurus AS $this_thesaurus)
      {
	echo "
	<tr>
	  <td class=\"fond_td\">
	    <a href=\"CCAM.php?acte=".$this_thesaurus['CODEACTE']."\">".$this_thesaurus['CODEACTE']."</a>
	  </td>
	  <td class=\"fond_td\">
	    ".$this_thesaurus['CATEGORIE']."
	  </td>
	  <td class=\"fond_td\"> 
	    ".$this_thesaurus['LIBUSUEL']."
	  </td>";
?>
	  <td class="fond_td">
	    <form action="CCAM.php" method="get">
	      <div>
		<input name="bouton_afficher_thesaurus" type="hidden" value="xx" />
		<input name="acte_a_supprimer" type="hidden" value="<?php echo $this_thesaurus['SERIE'] ?>" />
		<b>Supprimer</b> <input name="coche_supprimer" type="checkbox" />
		<input name="bouton_supprimer_thesaurus" type="submit" value="Confirmer" />
	      </div>
	    </form>
	  </td>
	</tr>
<?php
      }
?>
      </table>
      </div>
    </div>
<?php
    }
    else //on affiche le bouton du thesaurus
    {
?>
     <div class="groupe">
      <form action="CCAM.php" method="get">
	<fieldset>
<!--
	<legend>
	  Thesaurus
	</legend>
-->
	  <input name="bouton_afficher_thesaurus" type="submit" value="Afficher le thesaurus de <?php echo $signuser; ?>" />
	</fieldset>
      </form>  
    </div>
<?php
    } //fin thesaurus

include("inc/footer.php");
?>