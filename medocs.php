<?php
session_start() ;
include("config.php");
$basemed="DatasempTest";
//mode recherche medoc par specialite
//http://localhost/MedWebTux/medocs.php?radio_dispo=yes&radio_distri=4&radio_classe=1&nom_medoc=doliprane+1&envoyer_nom_medoc=Nom+commercial
try {
    $strConnection = 'mysql:host='.$host.';dbname='.$basemed; 
    $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); 
    $pdo = new PDO($strConnection, $loginbase, $pwd, $arrExtraParam); // Instancie la connexion
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 
}
catch(PDOException $e) {
    $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
    die($msg);
}

if ( !isset( $_SESSION['login'] ) )
{
  header('location: index.php?page=medocs' );
  exit;
}
include("inc/header.php");
?>
    <title>
      MedWebTux - M&eacute;dicaments - Utilisateur <?php echo $_SESSION['login'] ?>
    </title>
    <script type="text/javascript">
//<![CDATA[
function donner_focus(chp)
{
  document.getElementById(chp).focus();
}
//]]>
    </script>
    <style type="text/css" media="all">

    #fixe-haut
    {
        background      : white ;
        height          : 30px;
        position        : fixed;
        top             : 0;
        width           : 100%;
        left            : 0;
    }
        #conteneur
        {
/*                 padding                 : 30px 0 42px 0; */
                margin                  : auto;
        }
    </style>
  </head>
  <body style="font-size:<?php echo $fontsize ?>pt" onload="donner_focus('nom_medoc')" >
    <div class="conteneur">
<?php	
// insertion du menu d'en-tete	
$anchor='Mode_Vidal';
include("inc/menu-horiz.php");

function detectUTF8($string) //pour les lignes dont l'encodage est toujours imprévisible
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

function fix_utf8($string)
{
  if (detectUTF8($string))
  {
    return utf8_decode($string);
  }
  else
  {
    return $string;
  }
}
function stripAccents($string)
{
  return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}

$nom_medoc="";
$radio_classe='1'; //on selectionne par defaut le bouton qui choisit les medicaments
$radio_distri='4'; //medicaments de ville par defaut
$dispo='';
if (isset ($_GET['radio_classe']))
{
  $radio_classe=$_GET['radio_classe'];
}

if (isset ($_GET['radio_distri']))
{
  $radio_distri=$_GET['radio_distri'];
}


if (isset($_GET['id_medoc'])) //si on a l'ID du produit
{
  $id_medoc=$_GET['id_medoc'];
  $resultat_chercher_medoc=$pdo->prepare("SELECT t4B.f0 AS cip,t4B.f2 AS nom,f5, t42.f2 AS classe_nom FROM t4B INNER JOIN t42 ON t4B.fA=t42.f0 WHERE t4B.f0 =? ");
  $resultat_chercher_medoc->bindValue(1, $id_medoc, PDO::PARAM_STR);
  $resultat_chercher_medoc->execute();

  $count_chercher_medoc=$resultat_chercher_medoc->fetchAll();
  $resultat_chercher_medoc->closeCursor();
  $resultat_chercher_medoc = NULL;
}

if (isset($_GET['cuv_medoc'])) //code d'unite de vente (on a clique sur un medoc selectionne, par ex. ou on vient des interactions) Entraine l'affichage de la fiche.
{
?>
    <p id="fixe-haut">
      <a href="#composants">Composants</a>  <a href="#Propriétés_thérapeutiques">Propriétés thérapeutiques</a>  <a href="#Précautions_d_emploi">Précautions d'emploi</a>  <a href="#Indications">Indications</a>  <a href="#Contre-indications">Contre-indications</a>  <a href="#Effets_indésirables">Effets indésirables</a>  <a href="#Surdosage">Surdosage</a>  <a href="#Posologie">Posologie</a>  <a href="#Perturbation_des_examens_cliniques">Perturbation des examens cliniques</a>  <a href="#Composition_texte_structuré">Composition</a>  <a href="#Precautions_delivrance">Précautions de délivrance</a>  <a href="#Prix">Prix</a>  <a href="#duree">Durée maxi de prescription</a> <a href="#event">Évènements</a><a href="#tfr">TFR</a>  <a href="#labo">Laboratoire</a>
    </p>
<p>
<i>Données brutes du Vidal Data Semp. Pour les Références du Produit (RCP), cliquez sur le lien "Fiche Vidal"</i>
</p>
<?php
    $resultat_chercher_medoc=$pdo->prepare('SELECT  f0 AS cpg, f1 AS cuv, f2 AS cip, f3 AS nom,fJ FROM t00 WHERE f1 = ? ');
    $resultat_chercher_medoc->bindValue(1, $_GET['cuv_medoc'], PDO::PARAM_STR);
    $resultat_chercher_medoc->execute();

    $count_chercher_medoc=$resultat_chercher_medoc->fetchAll();
    $resultat_chercher_medoc->closeCursor();
    $resultat_chercher_medoc = NULL;
}
elseif (isset($_GET['nom_medoc'])) //si on fait la recherche par nom ou DCI ou indication
{
  if ($_GET['nom_medoc'] !="") //pas de joker si nom vide
  {
    $nom_medoc="%".stripAccents($_GET['nom_medoc'])."%";
  }
//URL en mode DCI
//MedWebTux/medocs.php?radio_dispo=yes&radio_distri=4&radio_classe=1&nom_medoc=allopurinol&envoyer_composition_medoc=DCI

//URL en mode classe
//MedWebTux/medocs.php?classe=M&classe1=M04&classe2=M04A&classe3=M04AA&groupe=M04AA01&button_classification=Chercher

/*t1A = modes de commercialisation
    f0 = code Lie t02.f2 et t63
    f1 = libelle. 
      2 hopitaux
      3 Sommeil
      4 Officines
      5 Toxico
      6 Dialyse
      8 Ville hopital
      9 SMUR
*/

  $fK=$radio_classe."%";
  $resultat_chercher_medoc=$pdo->prepare('SELECT t00.f0 AS cpg, t00.f1 AS cuv, t00.fG AS nom, t00.fJ FROM t00 INNER JOIN t02 ON t00.f1=t02.f0  WHERE CAST(t00.f3 AS CHAR) LIKE ? AND t00.fK LIKE ? AND t02.f2 = ? ORDER BY t00.f3');
  $resultat_chercher_medoc->bindValue(1, $nom_medoc, PDO::PARAM_STR);
  $resultat_chercher_medoc->bindValue(2, $fK, PDO::PARAM_STR);
  $resultat_chercher_medoc->bindValue(3, $radio_distri, PDO::PARAM_STR);
  $resultat_chercher_medoc->execute();
  $count_chercher_medoc=$resultat_chercher_medoc->fetchAll();

  $resultat_chercher_medoc->closeCursor();
  $resultat_chercher_medoc = NULL;

//recherche par DCI
  $resultat_chercher_composition=$pdo->prepare('SELECT f0, ANY_VALUE(f2) FROM t54 WHERE CAST(f2 AS CHAR) LIKE ? GROUP BY f0 ORDER BY ANY_VALUE(f2)'); // ORDER BY f2
  $resultat_chercher_composition->bindValue(1, $nom_medoc, PDO::PARAM_STR);
  $resultat_chercher_composition->execute();

  $count_chercher_composition=$resultat_chercher_composition->fetchAll();
  $resultat_chercher_composition->closeCursor();
  $resultat_chercher_composition = NULL;

  $nom_medoc=$_GET['nom_medoc']; // on reintialise le nom sans joker ni bidouille sur les accents pour retourner en zone de saisie
}

$resultat_chercher_version=$pdo->prepare('SELECT * FROM tFB');
$resultat_chercher_version->execute();

$count_chercher_version=$resultat_chercher_version->fetchAll();
//Clore la requête préparée
$resultat_chercher_version->closeCursor();
$resultat_chercher_version = NULL;

foreach ($count_chercher_version AS $this_chercher_version)
{
  if ($this_chercher_version['f0']=='EDITION NUMERO')
  {
    $num_version=$this_chercher_version['f1'];
  }
  elseif ($this_chercher_version['f0']=='ARRETE PACK IR')
  {
    $dateIR=$this_chercher_version['f1'];
  }
}

$checked_yes='checked="checked"';
$checked_no='';
$radio_dispo='';

if (isset($_GET['radio_dispo']))
{
  if ($_GET['radio_dispo']=="no")
  {
    $checked_yes='' ;
    $checked_no='checked="checked"' ;
  }
  $radio_dispo=$_GET['radio_dispo'];
}

?>
   <div class="groupe">
      <h1>
	Vidal Data Semp version <?php echo $num_version ?> du <?php echo $dateIR ?>
      </h1>

<!-- <form action="medocs.php" method="get" title="Tout ou partie du nom" onsubmit="return verif_champ(this.composition_medoc.value);"> -->
      <form action="medocs.php" method="get"  id="form_general">
	<fieldset>
	  <legend>
	    Options
	  </legend>
	  <label for="radio_dispo_yes">
	    Seulement les commercialis&eacute;s
	  </label>
	  <input type="radio" value="yes" name="radio_dispo" id="radio_dispo_yes" <?php echo $checked_yes ?>/>
          <label for="radio_dispo_no">
            Tous
          </label>
          <input type="radio" value="no" name="radio_dispo" id="radio_dispo_no" <?php echo $checked_no ?> />
          <br />
          <label for="radio_distri_2">
            Hôpitaux
          </label>
          <input type="radio" value="2" name="radio_distri" id="radio_distri_2" <?php if ($radio_distri=="2") echo 'checked="checked"' ?> />
<!-- Seuls les types 2 et 4 existent en vrai -->
<!--           <label for="radio_distri_3"> -->
<!--             Sommeil -->
<!--           </label> -->
<!--           <input type="radio" value="3" name="radio_distri" id="radio_distri_3" <?php if ($radio_distri=="3") echo 'checked="checked"' ?> /> -->
          <label for="radio_distri_4">
            Officines
          </label>
          <input type="radio" value="4" name="radio_distri" id="radio_distri_4" <?php if ($radio_distri=="4") echo 'checked="checked"' ?>/>
<!--           <label for="radio_distri_5"> -->
<!--             Centres de toxicomanie -->
<!--           </label> -->
<!--           <input type="radio" value="5" name="radio_distri" id="radio_distri_5" <?php if ($radio_distri=="5") echo 'checked="checked"' ?>/> -->
<!--           <label for="radio_distri_6"> -->
<!--             Centres de dialyse -->
<!--           </label> -->
<!--           <input type="radio" value="6" name="radio_distri" id="radio_distri_6" <?php if ($radio_distri=="6") echo 'checked="checked"' ?>/> -->
<!--           <label for="radio_distri_8"> -->
<!--             Ville hôpital -->
<!--           </label> -->
<!--           <input type="radio" value="8" name="radio_distri" id="radio_distri_8" <?php if ($radio_distri=="8") echo 'checked="checked"' ?>/> -->
<!--           <label for="radio_distri_9"> -->
<!--             SMUR -->
<!--           </label> -->
<!--           <input type="radio" value="9" name="radio_distri" id="radio_distri_9" <?php if ($radio_distri=="9") echo 'checked="checked"' ?> /> -->
          <br />
          <label for="radio_classe_1">
            Spécialité
          </label>
          <input type="radio" value="1" name="radio_classe" id="radio_classe_1" <?php if ($radio_classe=="1") echo 'checked="checked"' ?> />
          <label for="radio_classe_2">
            Diététique
          </label>
          <input type="radio" value="2" name="radio_classe" id="radio_classe_2" <?php if ($radio_classe=="2") echo 'checked="checked"' ?> />
          <label for="radio_classe_3">
            Vétérinaire
          </label>
          <input type="radio" value="3" name="radio_classe" id="radio_classe_3" <?php if ($radio_classe=="3") echo 'checked="checked"' ?> />
          <label for="radio_classe_4">
            Parapharmacie
          </label>
          <input type="radio" value="4" name="radio_classe" id="radio_classe_4" <?php if ($radio_classe=="4") echo 'checked="checked"' ?> />
          <label for="radio_classe_5">
            Accessoires
          </label>
          <input type="radio" value="5" name="radio_classe" id="radio_classe_5" <?php if ($radio_classe=="5") echo 'checked="checked"' ?> />
          <label for="radio_classe_6">
            Divers
          </label>
          <input type="radio" value="6" name="radio_classe" id="radio_classe_6" <?php if ($radio_classe=="6") echo 'checked="checked"' ?> />
<!--           <label for="radio_classe_7"> 
            Homéopathie
          </label>
          <input type="radio" value="7" name="radio_classe" id="radio_classe_7" <?php if ($radio_classe=="7") echo 'checked="checked"' ?> />-->
          <label for="radio_classe_8">
            Thermalisme
          </label>
          <input type="radio" value="8" name="radio_classe" id="radio_classe_8" <?php if ($radio_classe=="8") echo 'checked="checked"' ?> />

	</fieldset>
	<fieldset title="Tout ou partie du nom">
	  <legend>
	    Recherche par nom du m&eacute;dicament, composition ou indication :
	  </legend> 
	  <input name="nom_medoc" id="nom_medoc" type="text" value="<?php echo str_replace ("%","",$nom_medoc)?>" title="Nom commercial ou scientifique ou indication" />
	  <input name="envoyer_nom_medoc" type="submit" value="Nom commercial" />
	  <input name="envoyer_composition_medoc" type="submit" value="DCI"/>
	  <input name="envoyer_indication_medoc" type="submit" value="Indication" />
	</fieldset>
<?php
if (isset($_GET['num_substance'])) //on vient du tableau de resultat des DCI
{
  echo "<div class=\"information\">";
/*t07.
f2 = num substance 
f0=code produit cip */
  $num_substance=$_GET['num_substance'];
  $resultat_recherche_par_substance=$pdo->prepare('SELECT t27.f0 AS num_subst,t00.f3 AS nom_medoc,t00.f0 AS num_medoc ,t00.f1 AS cuv_medoc FROM t27 INNER JOIN t07 ON t27.f0=t07.f2 INNER JOIN t00 ON t07.f0=t00.f0 WHERE t27.f0= ?');
  $resultat_recherche_par_substance->bindValue(1, $num_substance, PDO::PARAM_STR);
  $resultat_recherche_par_substance->execute();

  $count_recherche_par_substance=$resultat_recherche_par_substance->fetchAll();
  $resultat_recherche_par_substance->closeCursor();
  $resultat_recherche_par_substance = NULL;

  $resultat_dispo=$pdo->prepare('SELECT * FROM t02 WHERE f0= ?');
  foreach ($count_recherche_par_substance AS $this_recherche_par_substance)
  {
    if ($radio_dispo=='yes')
    {
  //on cherche si le medoc est commercialise
      $cuv=$this_recherche_par_substance['cuv_medoc'];//code unite de vente
  //On cherche le code de dispo selon le CUV
      $resultat_dispo->bindValue(1, $cuv, PDO::PARAM_STR);
      $resultat_dispo->execute();

      $count_dispo=$resultat_dispo->fetchAll();

      if ($count_dispo[0]['f3']==0) // medicament commercialise seulement
      {
	$display_list=1;
      }
      else
      {
	$display_list=0;
      }
    }
    else
      $display_list=1;

    if ($display_list)
    {
	echo "
	  &nbsp;&nbsp;<a href=\"medocs.php?cuv_medoc=".$this_recherche_par_substance['cuv_medoc']."&amp;id_medoc=".$this_recherche_par_substance['num_medoc']."&amp;nom_medoc=".urlencode($this_recherche_par_substance['nom_medoc']).$dispo."\">".$this_recherche_par_substance['nom_medoc']."</a><br />";
     }
  }
$resultat_dispo->closeCursor();
$resultat_dispo = NULL;

echo "</div>";
}
/*
Recherche d'un medicament par indication
t1C.f0=2994 pour f1=indication
t52.f2=t1C.f0
t52.f1 = CIP ->t00.f0
t52.f0=t50.f0 
t50.f1= texte de l'indication en clair (en binaire, donc sensible à la casse et aux accents)
*/
 if (isset($_GET['envoyer_indication_medoc'])) //recherche par indication
{
  if ($_GET['nom_medoc']!='')
  {
    $text_indication='%'.addslashes(($_GET['nom_medoc'])).'%';
  }
  $resultat_cip_indication=$pdo->prepare('SELECT t52.f1 AS cip, t00.f3 AS libelle, t00.f1 AS cuv FROM t52 , t50 , t00  WHERE t52.f2="2994" AND Lower(t50.f1) LIKE ? AND t52.f1=t00.f0 AND t52.f0=t50.f0');
  $resultat_cip_indication->bindValue(1, strtolower($text_indication), PDO::PARAM_STR);
  $resultat_cip_indication->execute();

  $count_cip_indication=$resultat_cip_indication->fetchAll();
  $resultat_cip_indication->closeCursor();
  $resultat_cip_indication = NULL;

  foreach ($count_cip_indication AS $this_cip_indication)
  {
?>
<a href="medocs.php?cuv_medoc=<?php echo $this_cip_indication['cuv'] ?>&amp;radio_dispo=yes&amp;radio_distri=4&amp;radio_classe=1">
  <?php if (detectUTF8($this_cip_indication['libelle'])) echo $this_cip_indication['libelle']; else echo utf8_encode($this_cip_indication['libelle']); ?>
<br /></a>
<?php
  }
}

if ((isset($_GET['nom_medoc']) AND isset($_GET['envoyer_nom_medoc'])) OR isset($_GET['cuv_medoc']) OR isset($_GET['id_medoc']))
{ 
  $resultat_dispo=$pdo->prepare('SELECT * FROM t02 WHERE f0=?');
  foreach ($count_chercher_medoc AS $this_chercher_medoc)
  {
//on verifie si coche dispo
    if (isset($_GET['radio_dispo']))
    {
      if ($radio_dispo=='yes')
      {
    //on cherche si le medoc est commercialise
	$cuv=$this_chercher_medoc['cuv'];//code unite de vente
    //On cherche le code de dispo selon le CUV
	$resultat_dispo->bindValue(1, $cuv, PDO::PARAM_STR);
	$resultat_dispo->execute();
	$count_dispo=$resultat_dispo->fetchAll();

	foreach ($count_dispo AS $this_dispo)
	if ($this_dispo['f3']==0) // medicament commercialise seulement
	{
	  $display_list=1;
	}
	else
	{
	  $display_list=0;
	}
      }
      else
	$display_list=1;
    }
    else
      $display_list=1;

    if ($display_list)
    {
      if (detectUTF8($this_chercher_medoc['nom']))
	echo "
	<p>
	  <a href=\"medocs.php?cuv_medoc=".$this_chercher_medoc['cuv']."&amp;radio_dispo=".$radio_dispo."&amp;radio_distri=".$radio_distri."&amp;radio_classe=".$radio_classe."\">".$this_chercher_medoc['nom']."</a> 	<a href=\"fiche_vidal.php?cuv_medoc=".$this_chercher_medoc['cuv']."\" >Fiche Vidal</a>
	</p>";
      else
	echo "
	<p>
	  <a href=\"medocs.php?cuv_medoc=".$this_chercher_medoc['cuv']."&amp;radio_dispo=".$radio_dispo."&amp;radio_distri=".$radio_distri."&amp;radio_classe=".$radio_classe."\">".utf8_encode($this_chercher_medoc['nom'])."</a> 	<a href=\"fiche_vidal.php?cuv_medoc=".$this_chercher_medoc['cuv']."\" >Fiche Vidal</a>
	</p>";
    }

    if (isset($_GET['cuv_medoc']))
    {
      if ($_GET['cuv_medoc']==$this_chercher_medoc['cuv'])
      {
?>
        <p>
          <a href="interactions.php?select_medoc=<?php echo $this_chercher_medoc['cuv'] ?>&amp;nom_medoc=&amp;button_select_medoc=Ajouter+à+la+liste">Chercher les interactions</a>
	</p>
<?php
      }
    }
  }
  $resultat_dispo->closeCursor();
  $resultat_dispo = NULL;
}
?>

<?php
if (isset($_GET['nom_medoc']) AND isset($_GET['envoyer_composition_medoc']))
{
?>
	<table>
	  <tr>
	    <th>
	      Num&eacute;ro
	    </th>
	    <th>
	      Nom
	    </th>
	    <th>
	      DCI
	    </th>
	  </tr>
<?php
  $resultat_dispo=$pdo->prepare("SELECT f3 FROM t02 WHERE f0=?");
  
  foreach($count_chercher_composition AS $ligne_chercher_composition)
  {
?>
	  <tr>
	    <td valign="top">
	      <?php echo $ligne_chercher_composition['f0'] ?><!-- 	      marche -->
	    </td>
	    <td>
	      <p>
		<a href="medocs.php?num_substance=<?php echo $ligne_chercher_composition['f0'] ?>&amp;envoyer_composition_medoc=envoyer&amp;nom_medoc=<?php echo urlencode($_GET['nom_medoc']);
    if ($radio_dispo=="yes")
      echo '&amp;radio_dispo=yes';
    else
      echo '&amp;radio_dispo=no';
    ?>"><?php if (detectUTF8($ligne_chercher_composition['ANY_VALUE(f2)'])) echo $ligne_chercher_composition['ANY_VALUE(f2)']; else echo utf8_encode($ligne_chercher_composition['ANY_VALUE(f2)']).'</a>';
    if (isset($_GET['num_substance']))
    {
      echo '
	      <br />';
      if ($ligne_chercher_composition['f0']==$_GET['num_substance'])
      {
	if ($_GET['radio_dispo'])
	{
	  $dispo='&amp;radio_dispo='.$radio_dispo;
	}
	foreach($count_recherche_par_substance AS $ligne_recherche_par_substance)
	{
	  if ($radio_dispo=='yes')
	  {
	//on cherche si le medoc est commercialise
	    $cuv=$ligne_recherche_par_substance['cuv_medoc'];//code unite de vente
	//On cherche le code de dispo selon le CUV
	    $resultat_dispo->bindValue(1, $cuv, PDO::PARAM_STR);
	    $resultat_dispo->execute();
	    $count_dispo=$resultat_dispo->fetchAll();

	    if ($count_dispo[0]['f3']==0) // medicament commercialise seulement
	    {
	      $display_list=1;
	    }
	    else
	    {
	      $display_list=0;
	    }
	  }
	  else
	    $display_list=1;

	  if ($display_list)
	  {
	    if (detectUTF8($ligne_recherche_par_substance['nom_medoc']))
	      echo "
		&nbsp;&nbsp;<a href=\"medocs.php?cuv_medoc=".$ligne_recherche_par_substance['cuv_medoc']."&amp;id_medoc=".$ligne_recherche_par_substance['num_medoc']."&amp;nom_medoc=".urlencode($ligne_recherche_par_substance['nom_medoc']).$dispo."\">".$ligne_recherche_par_substance['nom_medoc']."</a><br />";
	    else
	      echo "
		&nbsp;&nbsp;<a href=\"medocs.php?cuv_medoc=".$ligne_recherche_par_substance['cuv_medoc']."&amp;id_medoc=".$ligne_recherche_par_substance['num_medoc']."&amp;nom_medoc=".urlencode(utf8_encode($ligne_recherche_par_substance['nom_medoc'])).$dispo."\">".utf8_encode($ligne_recherche_par_substance['nom_medoc'])."</a><br />";
	  }
	}
      }
    }
?>
	      </p>
	    </td>
	    <td valign="top">
	      <?php   if (detectUTF8($ligne_chercher_composition['ANY_VALUE(f2)'])) echo $ligne_chercher_composition['ANY_VALUE(f2)']; else echo utf8_encode($ligne_chercher_composition['ANY_VALUE(f2)']); ?>
	    </td>
	  </tr>
<?php
  } //fin foreach ligne composition
  $resultat_dispo->closeCursor();
  $resultat_dispo = NULL;

?>
	</table>
<?php
} //fin if composition medoc
?>
	<fieldset>
	  <legend>
	    Classification des m&eacute;dicaments
	  </legend>
	  <input type="submit" value="Chercher" name="button_classification" />
<?php
if (isset($_GET['button_classification']))
{
  $ajout_sql_dispo='';
  if ($_GET['radio_dispo'])
  {
    $dispo='&amp;radio_dispo='.$radio_dispo;
    if ($radio_dispo=='yes')
      $ajout_sql_dispo=" AND t02.f3='0'";
  }

//classification par ATC
/*
t42 : classes therapeutiques
f0= code ->t43.f0, t4B.fa, t70.f4
premier chiffre= classe racine, etc.
f1=libelle anglais
f2:libelle francais
*/
  $classe="";
  $classe1="";
  $classe2="";
  $classe3="";
  $groupe="";
  $resultat_chercher_classe=$pdo->prepare("SELECT * FROM t42 WHERE f0 LIKE ?");
  if (isset($_GET['classe']))
  {
    $classe=$_GET['classe'];
    $classe_joker=$_GET['classe']."%";

    $resultat_chercher_classe->bindValue(1, $classe_joker, PDO::PARAM_STR);
    $resultat_chercher_classe->execute();
    $count_chercher_classe1=$resultat_chercher_classe->fetchAll();
  }
  if (isset($_GET['classe1']))
  {
    $classe1=$_GET['classe1'];
    $classe1_joker=$_GET['classe1']."%";
    $resultat_chercher_classe->bindValue(1, $classe1_joker, PDO::PARAM_STR);
    $resultat_chercher_classe->execute();
    $count_chercher_classe2=$resultat_chercher_classe->fetchAll();
  }
  if (isset($_GET['classe2']))
  {
    $classe2=$_GET['classe2'];
    $classe2_joker=$_GET['classe2']."%";
    $resultat_chercher_classe->bindValue(1, $classe2_joker, PDO::PARAM_STR);
    $resultat_chercher_classe->execute();
    $count_chercher_classe3=$resultat_chercher_classe->fetchAll();
  }

  if (isset($_GET['classe3']))
  {
    $classe3=$_GET['classe3'];
    $classe3_joker=$_GET['classe3']."%";
    $resultat_chercher_classe->bindValue(1, $classe3_joker, PDO::PARAM_STR);
    $resultat_chercher_classe->execute();
    $count_chercher_groupe=$resultat_chercher_classe->fetchAll();
  }

  $resultat_chercher_medoc=$pdo->prepare("SELECT f0 FROM t4B WHERE fA = ?");
  if (isset($_GET['groupe']))
  {
    $groupe=$_GET['groupe'];
    $groupe_joker=$_GET['groupe']."%";

    $resultat_chercher_medoc->bindValue(1, $groupe, PDO::PARAM_STR);
    $resultat_chercher_medoc->execute();
    $count_chercher_medoc=$resultat_chercher_medoc->fetchAll();
 //   $resultat_chercher_medoc->closeCursor();
 //   $resultat_chercher_medoc = NULL;
  }
  echo '
	  <ul>';
  $resultat_chercher_classe->bindValue(1, '%', PDO::PARAM_STR);
  $resultat_chercher_classe->execute();
  $count_chercher_racine=$resultat_chercher_classe->fetchAll();

  $resultat_chercher_classe->closeCursor();
  $resultat_chercher_classe = NULL;

  $resultat_chercher_specialite_dispo =$pdo->prepare("SELECT t00.f1 AS cuv,t00.f3 AS nom FROM t00  WHERE t00.f0=?  AND t00.fK LIKE ? ");

  $resultat_complement=$pdo->prepare("SELECT * FROM  t02 WHERE f0= ? ".$ajout_sql_dispo);
  foreach ($count_chercher_racine AS $ligne_chercher_racine)
  {
    if (strlen($ligne_chercher_racine['f0'])==1) //classe racine
    {
//radio_dispo=yes&radio_distri=4&radio_classe=1
?>
	    <li>
	      <a href="medocs.php?classe=<?php echo $ligne_chercher_racine['f0'] ?>&amp;button_classification=Chercher&amp;radio_dispo=<?php echo $radio_dispo?>&amp;radio_distri=<?php echo $radio_distri ?>&amp;radio_classe=<?php echo $radio_classe ?>"><?php echo $ligne_chercher_racine['f2'] ?></a>
	    </li>
<?php
      if ($ligne_chercher_racine['f0']==$classe)
      {
	echo "
	    <li>
	      <ul>";
	foreach ($count_chercher_classe1 AS $ligne_chercher_classe1)
	{
	  if (strlen($ligne_chercher_classe1['f0'])==3)
	  {
?>
		<li>
		  <a href="medocs.php?classe=<?php echo $classe ?>&amp;classe1=<?php echo $ligne_chercher_classe1['f0'] ?>&amp;button_classification=Chercher&amp;radio_dispo=<?php echo $radio_dispo?>&amp;radio_distri=<?php echo $_GET['radio_distri'] ?>&amp;radio_classe=<?php echo $radio_classe ?>"><?php   if (detectUTF8($ligne_chercher_classe1['f2'])) echo $ligne_chercher_classe1['f2']; else echo utf8_encode($ligne_chercher_classe1['f2']); ?></a>
		</li>
<?php
	    if ($ligne_chercher_classe1['f0']==$classe1)
	    {
	      echo "
		<li>
		  <ul>";
	      foreach ($count_chercher_classe2 AS $ligne_chercher_classe2)
	      {
		if (strlen($ligne_chercher_classe2['f0'])==4)
		{
?>
		    <li>
		      <a href="medocs.php?classe=<?php echo $classe ?>&amp;classe1=<?php echo $classe1 ?>&amp;classe2=<?php echo $ligne_chercher_classe2['f0'] ?>&amp;button_classification=Chercher&amp;radio_dispo=<?php echo $radio_dispo?>&amp;radio_distri=<?php echo $_GET['radio_distri'] ?>&amp;radio_classe=<?php echo $radio_classe ?>"><?php echo utf8_encode($ligne_chercher_classe2['f2']) ?></a>
		    </li>
<?php
		  if ($ligne_chercher_classe2['f0']==$classe2)
		  {
		    echo "
		    <li>
		      <ul>";
		    foreach ($count_chercher_classe3 AS $ligne_chercher_classe3)
		    {
		      if (strlen($ligne_chercher_classe3['f0'])==5)
		      {
?>
			<li>
			  <a href="medocs.php?classe=<?php echo $classe ?>&amp;classe1=<?php echo $classe1 ?>&amp;classe2=<?php echo $classe2 ?>&amp;classe3=<?php echo $ligne_chercher_classe3['f0'] ?>&amp;button_classification=Chercher&amp;radio_dispo=<?php echo $radio_dispo?>&amp;radio_distri=<?php echo $_GET['radio_distri'] ?>&amp;radio_classe=<?php echo $radio_classe ?>"><?php echo utf8_encode($ligne_chercher_classe3['f2']) ?></a>
			</li>
<?php
			if ($ligne_chercher_classe3['f0']==$classe3)
			{
			  echo "
			<li>
			  <ul>";
			  foreach ($count_chercher_groupe AS $ligne_chercher_groupe)
			  {
			    if (strlen($ligne_chercher_groupe['f0'])==7)
			    { // condition pour que les groupes ne s'affichent pas en lien si groupe vide
			      $resultat_chercher_medoc->bindValue(1, $ligne_chercher_groupe['f0'], PDO::PARAM_STR);
			      $resultat_chercher_medoc->execute();
			      $count_chercher_medoc=$resultat_chercher_medoc->fetchAll();
			  //    $resultat_chercher_medoc->closeCursor();
			  //    $resultat_chercher_medoc = NULL;
			      if ($count_chercher_medoc)
			      {
?>
			    <li>
			      <a href="medocs.php?classe=<?php echo $classe ?>&amp;classe1=<?php echo $classe1 ?>&amp;classe2=<?php echo $classe2 ?>&amp;classe3=<?php echo $classe3 ?>&amp;groupe=<?php echo $ligne_chercher_groupe['f0'] ?>&amp;button_classification=Chercher&amp;radio_dispo=<?php echo $radio_dispo?>&amp;radio_distri=<?php echo $_GET['radio_distri'] ?>&amp;radio_classe=<?php echo $radio_classe ?>"><?php echo $ligne_chercher_groupe['f2'] ?></a>
			    </li>
<?php
			      }
			      else
			      {
?>
			    <li>
			      <?php echo $ligne_chercher_groupe['f2'] ?>
			    </li>
<?php
			      }
                              if ($ligne_chercher_groupe['f0']==$groupe)
                              {
                                echo "
			    <li>
			      <ul>";
                                $fK=$radio_classe.'%';
                                $compteur=0;
				foreach ($count_chercher_medoc AS $ligne_chercher_medoc)
                                {
                                  $this_cip=$ligne_chercher_medoc['f0'];

				  $resultat_chercher_specialite_dispo->bindValue(1, $this_cip, PDO::PARAM_STR);
				  $resultat_chercher_specialite_dispo->bindValue(2, $fK, PDO::PARAM_STR);
				  $resultat_chercher_specialite_dispo->execute();
				  $count_chercher_specialite_dispo=$resultat_chercher_specialite_dispo->fetchAll();

				  foreach ($count_chercher_specialite_dispo AS $ligne_chercher_specialite_dispo)
                                  {
                                    $compteur++;
				    $cuv=$ligne_chercher_specialite_dispo['cuv'];

				    $resultat_complement->bindValue(1, $cuv, PDO::PARAM_STR);
				    $resultat_complement->execute();
				    $count_complement=$resultat_complement->fetchAll();

				    if ($count_complement)
				    {
?>
				<li>
				  <a href="medocs.php?classe=<?php echo $classe ?>&amp;classe1=<?php echo $classe1 ?>&amp;classe2=<?php echo $classe2 ?>&amp;classe3=<?php echo $classe3 ?>&amp;groupe=<?php echo $groupe ?>&amp;cuv_medoc=<?php echo $ligne_chercher_specialite_dispo['cuv'].$dispo ?>&amp;button_classification=Chercher&amp;radio_dispo=<?php echo $radio_dispo?>&amp;radio_distri=<?php echo $_GET['radio_distri'] ?>&amp;radio_classe=<?php echo $radio_classe ?>"><?php if (detectUTF8($ligne_chercher_specialite_dispo['nom'])) echo $ligne_chercher_specialite_dispo['nom']; else  echo utf8_encode($ligne_chercher_specialite_dispo['nom']);  ?></a>
				</li>
<?php
				    }
				  }
/*
$timeend=microtime(true);
$time=$timeend-$timestart;

//Afficher le temps d'éxecution
$page_load_time = number_format($time, 3);
echo "Debut du script: ".date("H:i:s", $timestart);
echo "<br>Fin du script: ".date("H:i:s", $timeend);
echo "<br>Script execute en " . $page_load_time . " sec";
*/

                                }

                                if ($compteur==0)
                                {
?>
                                <li>
                                  Aucune réponse correspondant aux critères de recherche définis dans le cadre <i>Options</i>.
                                </li>
<?php
                                }
                                echo "
			      </ul>
			    </li>";
                              }
                            }
                          }
                          echo "
			  </ul>
			</li>";
                        }
                      }
                    }
                  echo "
		      </ul>
		    </li>";
                  }
                }
              }
              echo "
		  </ul>
		</li>";
            }
          }
        }
        echo "
	      </ul>
	    </li>";
      }
    }
  }
  $resultat_chercher_specialite_dispo->closeCursor();
  $resultat_chercher_specialite_dispo = NULL;

  $resultat_complement->closeCursor();
  $resultat_complement = NULL;
  $resultat_chercher_medoc->closeCursor();
  $resultat_chercher_medoc = NULL;

  echo '
	  </ul>';
} //fin if button_classification

echo '
	</fieldset>';
if (isset ($_GET['cuv_medoc']))
{
  $cuv_medoc=$_GET['cuv_medoc'];

  $resultat_chercher_medoc=$pdo->prepare("SELECT f0 AS cpg, f1 AS cuv,f2 AS cip, fG AS nom, f7 AS unite, f8 AS nombre,fJ FROM t00 WHERE f1 = ?");
  $resultat_chercher_medoc->bindValue(1, $cuv_medoc, PDO::PARAM_STR);
  $resultat_chercher_medoc->execute();
  $count_chercher_medoc=$resultat_chercher_medoc->fetchAll();
  $resultat_chercher_medoc->closeCursor();
  $resultat_chercher_medoc = NULL;

  $id_medoc=$count_chercher_medoc[0]['cpg'];

//Images associees
/*
t48.fO -> t48.f1 = code image ->t46.f0 t46.f1=nom image
      t5D.f1 ->f0
	t00.f1
*/
  $resultat_chercher_image=$pdo->prepare("SELECT t46.f1 AS nom_image FROM t5D INNER JOIN t48 ON t5D.f1=t48.f0 INNER JOIN t46 ON t48.f1=t46.f0 WHERE t5D.f0= ?");
  $resultat_chercher_image->bindValue(1, $cuv_medoc, PDO::PARAM_STR);
  $resultat_chercher_image->execute();
  $count_chercher_image=$resultat_chercher_image->fetchAll();
  $resultat_chercher_image->closeCursor();
  $resultat_chercher_image = NULL;

  $image='';
  $ligne_chercher_image=array();
  if ($count_chercher_image)
  {
    $ligne_chercher_image=$count_chercher_image[0]['nom_image'];
    $image='vidal_goodies/IMAGES/'.substr($image,0,3).'/'.substr($image,3,3).'/'.substr($image,6);
  }
  //chercher le code voie administration dans t0A
    $resultat_t0A=$pdo->prepare("SELECT * FROM t0A WHERE f0= ?");
    $resultat_t0A->bindValue(1, $id_medoc, PDO::PARAM_STR);
    $resultat_t0A->execute();
    $count_t0A=$resultat_t0A->fetchAll();
    $resultat_t0A->closeCursor();
    $resultat_t0A = NULL;
    $ligne_t0A='';
    if ($count_t0A)
      $ligne_t0A=  $count_t0A[0]['f1'];

    $resultat_ce_medoc=$pdo->prepare("SELECT t4B.f0 AS numero,CAST(t4B.fF AS unsigned integer) AS psy, CAST(t4B.fG AS unsigned integer) AS dopant, t4B.fE AS vigi,t42.f0 AS classe_num,t4B.f2 AS nom,f5, t42.f2 AS classe_nom  FROM t4B INNER JOIN t42 ON t4B.fA=t42.f0 WHERE t4B.f0 = ?");
    $resultat_ce_medoc->bindValue(1, $id_medoc, PDO::PARAM_STR);
    $resultat_ce_medoc->execute();
    $count_ce_medoc=$resultat_ce_medoc->fetchAll();
    $resultat_ce_medoc->closeCursor();
    $resultat_ce_medoc = NULL;

//interference etats pathologiques
/*
t52
  f0 = code du terme ->t50.f0
  f1 = CIP ->t00.f0
  f2 = nature du lien (contre-indique...) -> t1C.f0 (liste des codes) ou t51.f2
  f3 = frequence
  f5 = libelle en clair de l'effet
  f6 = code document ->t45.f0
*/
    $resultat_etat_patho=$pdo->prepare("SELECT t50.f1 AS patho,t52.f5 AS commentaire,t1C.f1 AS nature,t50.f0 AS code_unite, t52.f3 AS frequence_effet FROM t52 INNER JOIN t50 on t52.f0=t50.f0 INNER JOIN t1C ON t52.f2=t1C.f0 WHERE t52.f1= ?");
    $resultat_etat_patho->bindValue(1, $id_medoc, PDO::PARAM_STR);
    $resultat_etat_patho->execute();
    $count_etat_patho=$resultat_etat_patho->fetchAll();
    $resultat_etat_patho->closeCursor();
    $resultat_etat_patho = NULL;

  //requete chercher classe racine
  $debut_num_classe='';
  if ($count_ce_medoc)
{
    $debut_num_classe=substr($count_ce_medoc[0]['classe_num'],0,1);

  $resultat_chercher_classe_racine=$pdo->prepare("SELECT * FROM t42 WHERE f0= ?"); //valable pour toute la suite

  $resultat_chercher_classe_racine->bindValue(1, $debut_num_classe, PDO::PARAM_STR);
  $resultat_chercher_classe_racine->execute();
  $count_chercher_classe_racine=$resultat_chercher_classe_racine->fetchAll();

  $num_classe_1=substr($count_ce_medoc[0]['classe_num'],0,3);

  $resultat_chercher_classe_racine->bindValue(1, $num_classe_1, PDO::PARAM_STR);
  $resultat_chercher_classe_racine->execute();
  $count_chercher_classe_1=$resultat_chercher_classe_racine->fetchAll();

  $num_classe_2=substr($count_ce_medoc[0]['classe_num'],0,4);

  $resultat_chercher_classe_racine->bindValue(1, $num_classe_2, PDO::PARAM_STR);
  $resultat_chercher_classe_racine->execute();
  $count_chercher_classe_2=$resultat_chercher_classe_racine->fetchAll();

  $num_classe_3=substr($count_ce_medoc[0]['classe_num'],0,5);

  $resultat_chercher_classe_racine->bindValue(1, $num_classe_3, PDO::PARAM_STR);
  $resultat_chercher_classe_racine->execute();
  $count_chercher_classe_3=$resultat_chercher_classe_racine->fetchAll();

  $resultat_chercher_classe_racine->closeCursor();
  $resultat_chercher_classe_racine = NULL;

  //t63=precautions precription
    //f0=code CUV
    //f2= Code registre d'incription
    //f3 = liste I ou II
    //f4 duree maxi pour une delivrance unique
    //f7 sites de prescription si ordinaire ou renouvellement de restreinte
    //f8 site de prescription initiale
    //fB Portee d'une prescription initiale apres laquelle le renouvellement n'est plus possible
    //fD ordonnance securisee ou chevauchemnet impossible, selon t1C.f0->f1
}

  $resultat_regles_delivrance=$pdo->prepare("SELECT * FROM t63  WHERE f0= ?");
  $resultat_regles_delivrance->bindValue(1, $cuv_medoc, PDO::PARAM_STR);
  $resultat_regles_delivrance->execute();
  $count_regles_delivrance=$resultat_regles_delivrance->fetchAll();
  $resultat_regles_delivrance->closeCursor();
  $resultat_regles_delivrance = NULL;

  $fD='';
  if ($count_regles_delivrance)
    $fD=$count_regles_delivrance[0]['fD'];

  $resultat_delivrance_2=$pdo->prepare("SELECT f1 FROM t1C WHERE f0= ?");
  $resultat_delivrance_2->bindValue(1, $fD, PDO::PARAM_STR);
  $resultat_delivrance_2->execute();
  $count_delivrance_2=$resultat_delivrance_2->fetchAll();
  $resultat_delivrance_2->closeCursor();
  $resultat_delivrance_2 = NULL;

//t01 tarifs
  //f0 = CUV
  //f3 = prix ttc
  //f7= code remboursement
  $resultat_prix=$pdo->prepare("SELECT f3,f7 FROM t01 WHERE f0= ?");
  $resultat_prix->bindValue(1, $cuv_medoc, PDO::PARAM_STR);
  $resultat_prix->execute();
  $count_prix=$resultat_prix->fetchAll();
  $resultat_prix->closeCursor();
  $resultat_prix = NULL;

//Codes remboursement
//t1D = liste des codes
  //f0 = code
  //f1=libelle
  $resultat_rembt=$pdo->prepare("SELECT * FROM t1D WHERE f0= ?");
  $resultat_rembt->bindValue(1, $count_prix[0]['f7'], PDO::PARAM_STR);
  $resultat_rembt->execute();
  $count_rembt=$resultat_rembt->fetchAll();
  $resultat_rembt->closeCursor();
  $resultat_rembt = NULL;
/*
t03 Codes AMM
  f0 = CUV
  f1 = base de remboursement
  f2 = regime SS. Utiliser 01.7
  f3 = restriction de delivrance. Preferer 63.3
  f4 = date AMM
  f5 = code de delivrance, 6 bits utilises
    Bit 0 : "Spécialité pharmaceutique"
    Bit 1 : "Délivré seulement sur ordonnance"
    Bit 2 : "Délivrance soumise au contrôle médical de la Sécurité Sociale
    Bit 3 : "Vente interdite"
    Bit 4 : "Prescription restreinte" 
    Bit 5 : "Médicament d'exception" 
  f6 = agemeents divers-fait double emploi avec les evenements 00L
  f7 = sesam vitale B2 Utiliser 4E.6
  f9 = code commentaire sur la delivrance ->1C. Utiliser t61 et t 62 
  fC = duree maximale
*/

//evenements
//t4C
  //f0 = code CUV
  //f1 = type d'evenement
  //f2 date
//t1C libelles des evenements
  //f0 = num d'evenement
  //f1 = libelle evenement

  $resultat_evnt=$pdo->prepare("SELECT t1C.f1 AS f1,f2 FROM t4C INNER JOIN t1C ON t4C.f1=t1C.f0 WHERE t4C.f0=?");
  $resultat_evnt->bindValue(1, $cuv_medoc, PDO::PARAM_STR);
  $resultat_evnt->execute();
  $count_evnt=$resultat_evnt->fetchAll();
  $resultat_evnt->closeCursor();
  $resultat_evnt = NULL;

//fabricants
//t02 
  //f1 = code fabricant ->t17.f0-t18.f0
  //f3 disponibilite ->code en t34
//t17 liste des fabricants
  //f2 nom
//t18 adresse des fabricants. Meme f0 que t17
  //f3 - f4 adresse
  //f5 code postal
  //f6 ville
  //f7 Tel
  //f9 tel2
  //fC mail
  //fD web

  $resultat_fabricant=$pdo->prepare("SELECT t02.f0 AS f0,t17.f2 AS f2 FROM t02 INNER JOIN t17 ON t02.f1=t17.f0 where t02.f0= ?");
  $resultat_fabricant->bindValue(1, $cuv_medoc, PDO::PARAM_STR);
  $resultat_fabricant->execute();
  $count_fabricant=$resultat_fabricant->fetchAll();
  $resultat_fabricant->closeCursor();
  $resultat_fabricant = NULL;

if ($count_fabricant)
{
  $resultat_adresse=$pdo->prepare("SELECT * FROM t18 WHERE f0= ?");
  $resultat_adresse->bindValue(1, $count_fabricant[0]['f0'], PDO::PARAM_STR);
  $resultat_adresse->execute();
  $count_adresse=$resultat_adresse->fetchAll();
  $resultat_adresse->closeCursor();
  $resultat_adresse = NULL;
}
  //composants t07
    //f0 = code cpg
    //f1 = N° de section, associant les composants d'un même produit en groupes homogènes, décrits dans la table 08. Par exemple : Section 00 = Principes actifs, Section 01 = Excipients du noyau, Section 02 = Excipients de l'enrobage. Attention ! Ne pas confondre le numéro de section avec son qualificatif : les principes actifs ne coïncident pas nécessairement avec la section 00. -lie au f1 de t08
    //f2 = code substance -lie à t27-f0 ->libelle en f2
    //f4 = quantite
//$resultat_substance=$pdo->prepare("SELECT * FROM t07 INNER JOIN t27 ON t07.f2=t27.f0 WHERE t07.f0= ?");
  $resultat_substance=$pdo->prepare("SELECT t27.f2,t07.f1,t07.f4,t07.f5 FROM t07 INNER JOIN t27 ON t07.f2=t27.f0 WHERE t07.f0= ?");
  $resultat_substance->bindValue(1, $id_medoc, PDO::PARAM_STR);
  $resultat_substance->execute();
  $count_substance=$resultat_substance->fetchAll();
  $resultat_substance->closeCursor();
  $resultat_substance = NULL;

//Interactions
//t4B-f4 = classe d'interactions->t10 f0 et f1 
//t10-f2= degre de gravite entre 1 et 4
//t10-f5 = libelle complet XML
if ($count_ce_medoc)


  $find=array('&#x3C;','&#x3E;','<BR/>');
  $replace=array('&amp;lt;','&amp;gt;','\n');
  $ce_medoc_affichable='';
  if ($count_ce_medoc)
    $ce_medoc_affichable=str_replace ($find,$replace,$count_ce_medoc[0]['f5']);

  $xml=simplexml_load_string($ce_medoc_affichable);
//simpleXML remplace les entites < et > en balises
//il bouffe aussi les br
//et il convertit en utf-8

//chercher les documents lies
/*
t45
  f0= cuv
  f1=lien t49.f1
t49
  f0=lien t45
  f1=categorie de document (PGR...)
  f2=type de document (PDF...)
  f5=URL du document
*/

  $resultat_documents_lies=$pdo->prepare("SELECT * FROM t49 INNER JOIN t45 ON t49.f1=t45.f0 WHERE t49.f0= ?");
  $resultat_documents_lies->bindValue(1, $id_medoc, PDO::PARAM_STR);
  $resultat_documents_lies->execute();
  $count_documents_lies=$resultat_documents_lies->fetchAll();
  $resultat_documents_lies->closeCursor();
  $resultat_documents_lies = NULL;

/*DOCUMENT LIE - ALD 	
DOCUMENT LIE - PRG 	
DOCUMENT LIE - FIT
DOCUMENT LIE - BUM
*/
  foreach ($count_documents_lies AS $ligne_documents_lies)
  {
    if ('DOCUMENT LIE - RBU'==$ligne_documents_lies['f1'])
    {
      $link=substr($ligne_documents_lies['f5'],0,7).'/'.substr($ligne_documents_lies['f5'],7,3).'/'.strtolower(substr($ligne_documents_lies['f5'],10,3)).'/0'.substr($ligne_documents_lies['f5'],14,21);
      echo '<a href="vidal_goodies/'.$link.'">'.$ligne_documents_lies['f1'].'</a><br />';
    }
    elseif ('DOCUMENT LIE - ALD'==$ligne_documents_lies['f1'])
    {
 //	DS_DOCSALD76b8016d3696f8d61d786c02fa110bb6.pdf
      $link=substr($ligne_documents_lies['f5'],0,7).'/'.substr($ligne_documents_lies['f5'],7,3).'/'.substr($ligne_documents_lies['f5'],10,36);
echo $ligne_documents_lies['f5'].'<br />';
      echo '<a href="vidal_goodies/'.$link.'">'.$ligne_documents_lies['f1'].'</a><br />';
    }
  }
  $nb_rubr=0;
  /*t24 = codes unite
f0=code
f1=unite en clair
    */
  $resultat_unite=$pdo->prepare("SELECT f1,f5 FROM t24 WHERE f0= ?");//f1 singulier, f5 pluriel

  function unite_t24($unite_code,$value,$pdo,$resultat_unite) //fonction pour trouver l'unite en clair en fonction de son code
  {
    if ($unite_code=="")
      return ;
    else
    {	
      $resultat_unite->bindValue(1, $unite_code, PDO::PARAM_STR);
      $resultat_unite->execute();
      $count_unite=$resultat_unite->fetchAll();
      if ($value>1)
	return $count_unite[0]['f5'];
      else
	return $count_unite[0]['f1'];
    }
  }
?>
	<table>
	  <tr style="vertical-align:top">
	    <th>
	      États pathologiques
	    </th>
	  <td>
<?php
foreach ($count_etat_patho As $ligne_etat_patho)
{
  if (detectUTF8($ligne_etat_patho['commentaire']))
    echo str_replace("?","'",$ligne_etat_patho['commentaire']);
  else
    echo utf8_encode($ligne_etat_patho['commentaire']);
  echo ' <b> ';
  if (detectUTF8($ligne_etat_patho['nature']))
    echo $ligne_etat_patho['nature'];
  else
    echo utf8_encode($ligne_etat_patho['nature']);
  echo ' : </b> ';
  if (detectUTF8($ligne_etat_patho['patho']))
    echo str_replace("?","'",$ligne_etat_patho['patho']); //remplacer les ? du texte
  else 
    echo utf8_encode($ligne_etat_patho['patho']);

  if ($ligne_etat_patho['frequence_effet'])
{
  if (detectUTF8($ligne_etat_patho['frequence_effet']))
    echo ' <b>fréquence</b> : '.$ligne_etat_patho['frequence_effet'];
  else
    echo ' <b>fréquence</b> : '.utf8_encode($ligne_etat_patho['frequence_effet']);
}
$code_unite=$ligne_etat_patho['code_unite'];

$resultat_unite_etat=$pdo->prepare("SELECT t2A.f1 AS type_valeur, t24.f1 AS unite_valeur, t2G.f1 AS valeur_min, t2G.f2 AS valeur_max FROM t2G INNER JOIN t2A ON t2G.f0=t2A.f0 INNER JOIN t24 ON t2G.f3=t24.f0 WHERE t2G.f4= ?");
$resultat_unite_etat->bindValue(1, $code_unite, PDO::PARAM_STR);
$resultat_unite_etat->execute();
$count_unite_etats=$resultat_unite_etat->fetchAll();
$resultat_unite_etat->closeCursor();
$resultat_unite_etat = NULL;

if ($count_unite_etats)
{
  $ligne_unite_etat=$count_unite_etats[0];
  if ($ligne_unite_etat['type_valeur'])
  {
    if (detectUTF8($ligne_unite_etat['type_valeur']))
      echo ' '.$ligne_unite_etat['type_valeur'];
    else
      echo ' '.utf8_encode($ligne_unite_etat['type_valeur']);
  }
  if ($ligne_unite_etat['valeur_min'])
    echo " min ".$ligne_unite_etat['valeur_min'];
  if ($ligne_unite_etat['valeur_max'])
    echo " max : ".$ligne_unite_etat['valeur_max'];
  if ($ligne_unite_etat['unite_valeur'])
  {
    if (detectUTF8($ligne_unite_etat['unite_valeur']))
      echo " ".$ligne_unite_etat['unite_valeur'];
    else
      echo " ".utf8_encode($ligne_unite_etat['unite_valeur']);
  }
}
echo '<br />';
}
?>
	  </td>
	</tr>

<?php
if ($ligne_chercher_image)
{

?>
	  <tr>
	    <th>
	      Image
	    </th>
	    <td>
	      <p class="info">Boîte<span><img src="<?php echo $image; ?>" alt="image du produit" /></span></p>
	    </td>
	  </tr>
<?php

}
?>
	  <tr valign="top">
	    <th>
	      Num&eacute;ro
	    </th>
	    <td>
	      <?php if ($count_ce_medoc) { ?><strong>CPG</strong> : <?php echo $count_ce_medoc[0]['numero']; } ?> <strong>CUV ou CIS</strong> : <?php echo $cuv_medoc ?> <strong>CIP</strong> : <?php echo $count_chercher_medoc[0]['cip'] ?>
	    </td>
	  </tr>
	  <tr valign="top">
	    <th>
	      Nom
	    </th>
	    <td>
	      <?php  if (detectUTF8($count_chercher_medoc[0]['nom'])) echo $count_chercher_medoc[0]['nom']; else echo utf8_encode($count_chercher_medoc[0]['nom']); ?>
	    </td>
	  </tr>
	  <tr valign="top"> 
	    <th>
	      <a name="composants"></a>Composants
	    </th>
	    <td>
<?php 
/*composants t08
f0->t4B.f0 = cpg
f1 = numero d'ordre du groupe
f2->t1C.f0 = descripteur de composant (f1)//libelles dans t1C (nom du produit) - 27=principe actif
f6->t24.f0 = unite (f1)
f8->t24.f0 = code unite
fA->t24.f0 = code unite
fB = 0 principe actif, 1 excipient, 2 excipient a effet notoire
*/

  $cette_ligne="";
  $unite_substance='';
  $quantite_substance='';

  $resultat_section_composant=$pdo->prepare("SELECT f2,f4,f5,fB FROM t08  WHERE f1= ?");
//$resultat_substance="SELECT * FROM t07 INNER JOIN t27 ON t07.f2=t27.f0 WHERE t07.f0= ?"
  foreach ($count_substance AS $ligne_substance)
  {
    $resultat_section_composant->bindValue(1, $ligne_substance['f1'], PDO::PARAM_STR);
    $resultat_section_composant->execute();
    $count_section_composant=$resultat_section_composant->fetchAll();
 
    if ($count_section_composant[0]['fB']=='0') 
    {
      echo '<strong>Principe actif </strong>'; 
      $principe_actif=$ligne_substance['f2'];
      $quantite_substance=$ligne_substance['f4'];
      $unite_substance=$ligne_substance['f5'];
    }
    else 
      echo '
	      <strong>Excipient</strong> ';
    if (detectUTF8($ligne_substance['f2']))
      echo ' <a href="medocs.php?radio_dispo='.$radio_dispo.'&amp;nom_medoc='.urlencode($ligne_substance['f2']).'&amp;envoyer_composition_medoc=DCI">'.$ligne_substance['f2'].'</a> '.$ligne_substance['f4'].' '.unite_t24($ligne_substance['f5'],$ligne_substance['f4'],$pdo,$resultat_unite).'<br />';
    else
      echo ' <a href="medocs.php?radio_dispo='.$radio_dispo.'&amp;nom_medoc='.urlencode(utf8_encode($ligne_substance['f2'])).'&amp;envoyer_composition_medoc=DCI">'.utf8_encode($ligne_substance['f2']).'</a> '.$ligne_substance['f4'].' '.unite_t24($ligne_substance['f5'],$ligne_substance['f4'],$pdo,$resultat_unite).'<br />';
  }
  $resultat_section_composant->closeCursor();
  $resultat_section_composant = NULL;

  if ($xml)
  {
    foreach ($xml->RUBRIQUE as $value)
    {
      //on enleve les espaces et apostrophes et autres trucs pour faire des liens
      $find_link=array(' ',"'","(",")");
      $replace_link=array('_',"_","","");
      $titre_link=str_replace($find_link,$replace_link,$value['TITRE']);
?>
            </td>
          </tr>
          <tr valign="top">
            <th>
              <a name="<?php echo $titre_link ?>"></a>Caractéristiques
            </th>
            <td>
	      <h2>
		<?php echo utf8_decode($value['TITRE']) ?>
	      </h2> 
<!-- titre premier niveau -->
<?php
	      utf8_decode(str_replace('\n','<br />',$value)); //contenu de premier niveau- on remet les retours chariot
      $nb_ssrubr=0;
      foreach ($xml->RUBRIQUE[$nb_rubr] as $value_ssrubr)
      {
        if ($value_ssrubr['TITRE'])
          echo  '
	      <h3>
		'.utf8_decode($value_ssrubr['TITRE']).'
	      </h3>'. //titre deuxieme niveau
	      utf8_decode(str_replace ("?","'",str_replace('\n','<br />',$value_ssrubr))); //contenu de ss rubr
        if ($xml->RUBRIQUE[$nb_rubr]->SSRUBR[$nb_ssrubr])
        {
          foreach ($xml->RUBRIQUE[$nb_rubr]->SSRUBR[$nb_ssrubr] as $value_cas)//warning si vide
          {
            echo '
	      <h4>
		'.utf8_decode($value_cas['TITRE']).'
	      </h4>'. //titre troisieme niveau
	      utf8_decode(str_replace("?","'",(str_replace('\n','<br />',$value_cas)))); //contenu de 3eme niveau
          }
        }
        $nb_ssrubr++;
      }
      $nb_rubr++;
    }
  }
 ?>
	    </td>
	  </tr>
	  <tr valign="top">
	    <th>
	      Posologie journalière
	    </th>
<?php
/*t0A = voie d'adminstration selon code medicament
    f0=code medoc
    f1=code admin (t15.f0)

t15 = codes voie administration
  f0=code  (18...)
  f1=libelle (dentaire...)
*/

  $resultat_voie=$pdo->prepare("SELECT t15.f1,t15.f0 FROM t0A INNER JOIN t15 ON t0A.f1=t15.f0 WHERE t0A.f0= ?");
  $resultat_voie->bindValue(1, $id_medoc, PDO::PARAM_STR);
  $resultat_voie->execute();
  $count_voie=$resultat_voie->fetchAll();
  $resultat_voie->closeCursor();
  $resultat_voie = NULL;

//  $voie_code=$count_voie[0]['f0']; //dans t15
  $voie_clair='';
  if ($count_voie)
    $voie_clair=$count_voie[0]['f1'];
  /*t43=poso
      f0= code medoc
      f1=voie ->t15.f0
      f2=dose quotidienne
      f3=code unite ->t24.f1
      f4=substance ->t27.f0
  */
  $this_group='';
  $class_num='';
  $voie='';
  if ($count_ce_medoc)
  {
    $this_group=$count_ce_medoc[0]['classe_num'];
    $voie=$count_ce_medoc[0]['numero'];
  }
  $resultat_poso=$pdo->prepare("SELECT * FROM t43 WHERE f0= ? AND f1=?");
  $resultat_poso->bindValue(1,$this_group , PDO::PARAM_STR);
  $resultat_poso->bindValue(2, $voie, PDO::PARAM_STR);
  $resultat_poso->execute();
  $count_poso=$resultat_poso->fetchAll();
  $resultat_poso->closeCursor();
  $resultat_poso = NULL;

  $substance='';
  if ($count_poso)
    $substance=$count_poso[0]['f4'];

  $unite_code=$count_chercher_medoc[0]['unite'];
  $ligne_unite_prise=unite_t24($unite_code,'2',$pdo,$resultat_unite); //pluriel d'autorite

/*t27 substances
    f0=code substance ->t07.f2,t11.f1,t28.f0,t28.f1,t3G.f0,t43.f4,t54.f0,t58.f1,t59.f0,t59.f1,t5I.f0 et f1
    f2= nom substance
    f6=interaction ->t26.f0
    f8=code OMS
    fB=dopant MJS

*/
  $resultat_substance_poso=$pdo->prepare("SELECT * FROM t27 WHERE f0= ?");
  $resultat_substance_poso->bindValue(1,$substance , PDO::PARAM_STR);
  $resultat_substance_poso->execute();
  $count_substance_poso=$resultat_substance_poso->fetchAll();
  $resultat_substance_poso->closeCursor();
  $resultat_substance_poso = NULL;

  $ligne_substance_poso=array();
  if ($count_substance_poso)
    $ligne_substance_poso=$count_substance_poso[0];
  /*t25 codes galeniques
    f0= code lie a  ?? (pas t4b.f1)
    f1=libelle
    f4=libelle abrg
  */

  $resultat_galenique=$pdo->prepare("SELECT f1 FROM t25 WHERE f0= ?");
  $resultat_galenique->bindValue(1, $count_chercher_medoc[0]['fJ'], PDO::PARAM_STR);
  $resultat_galenique->execute();
  $count_galenique=$resultat_galenique->fetchAll();
  $resultat_galenique->closeCursor();
  $resultat_galenique = NULL;

/*dosage t07
f0=code produit
f1=code section de la table 8
f2=code substance (t27.0)
f3=ordre dans la section (t8)
f4=quantite
f5=code unite (voir t24.f0)
*/

// pas utilise  $ligne_unite=unite_t24($unite_substance,$pdo,$resultat_unite);
  $unite_jour='';
  if ($count_poso)
    $unite_jour=$count_poso[0]['f3'];

?>
	    <td>
	      <?php if ($count_poso) { ?><a name="poso"></a><?php echo $count_poso[0]['f2'].' '.unite_t24($unite_jour,$count_poso[0]['f2'],$pdo,$resultat_unite).' de '.$ligne_substance_poso['f2'].' (voie '.$count_voie[0]['f1'].' en  '.$ligne_unite_prise.' '; if ($count_galenique) echo $count_galenique[0]['f1']; echo ' '.$quantite_substance.' '.unite_t24($unite_substance,$quantite_substance,$pdo,$resultat_unite).') '?> - boîtes de <?php echo $count_chercher_medoc[0]['nombre']?> (posologie pour un adulte mâle de 75 Kg) <?php } ?>
	    </td>
	  </tr>
          <tr>
            <th>
              Code Dopage MJS
            </th>
            <td>
              <?php if($ligne_substance_poso) echo $ligne_substance_poso['fB']?>
            </td>
          </tr>
<?php
/*poso = t2P
f0=code lie a t2M.f0
f1=code produit = CIP
f2=quantite minimum
f3=unite quantite minimum f2 (t24.f0 ->t24.f1)
f4=duree quantite minimum (en clair = par jour)
f5=frequence des prises minimum
f6=code unite de frequence ->t24.f0 ->f1 en clair
f7=quantite maxi
f8=unite
f9=duree qt maxi
fA=freq prises
fB=unite
fC=dose maximale
fD=code unite dose maxi
fE=duree dose maxi
fF=freq des prises a doses maxi
fG=code unite
fH=qtte min par prise
fI=code unite
fJ=qtt max par prise
fK=code unite
fL=duree min tt
fM=code unite
fN=duree maxi
fO=code unite
fP=poso fixe O/N
fQ=duree min absolue
fR=code unite
fS=duree maxi
fT=code unite
*/

/*
t2L = bornes de prescription
f1= borne inferieure
f2=borne sup
f3=code unite (t24.f0)
f6=descripteur = A (llaitement), F(emme)
*/
  $resultat_posologies=$pdo->prepare("SELECT * FROM t2P WHERE f1= ?");
  $resultat_posologies->bindValue(1, $id_medoc, PDO::PARAM_STR); //000498 pour oflocet unidose
  $resultat_posologies->execute();
  $count_posologies=$resultat_posologies->fetchAll();
  $resultat_posologies->closeCursor();
  $resultat_posologies = NULL;
//echo "id medoc".$id_medoc;  //oflocet unidose 000498
  $resultat_bornes=$pdo->prepare("SELECT * FROM t2L WHERE f4= ?");

  foreach ($count_posologies AS $ligne_posologies)
  {
    $resultat_bornes->bindValue(1, $ligne_posologies['f0'], PDO::PARAM_STR); //f0= 6093 pour oflocet unidose
    $resultat_bornes->execute();
    $count_bornes=$resultat_bornes->fetchAll();
//print_r($count_bornes);
    $unite_max_clair='';
    $unite_min_clair='';
    $limit_min='';
    $limit_max='';
    $limit_unit='';
    $unite_min=$ligne_posologies['f3'];
    $unite_max=$ligne_posologies['f8'];
    $frequence_mini_code=$ligne_posologies['f6'];
    $frequence_maxi_code=$ligne_posologies['fF'];
    $unite_min_clair=unite_t24($unite_min,$ligne_posologies['f2'],$pdo,$resultat_unite);
    $unite_max_clair=unite_t24($unite_max,$ligne_posologies['f7'],$pdo,$resultat_unite);
    $de='';
    $duree_min=$ligne_posologies['fL'];
    $duree_max=$ligne_posologies['fN'];
    $unite_duree_min_clair=unite_t24($ligne_posologies['fM'],$ligne_posologies['fL'],$pdo,$resultat_unite);
    $unite_duree_max_clair=unite_t24($ligne_posologies['fO'],$ligne_posologies['fN'],$pdo,$resultat_unite);
    $a='';
    $de='';
    if (count($count_bornes)>0) //limites d'age et de poids
    {
// pas utilise      $unite_borne=unite_t24($count_bornes[0]['f3'],$pdo,$resultat_unite);
      $de=' de ';
      $a=' à ';
      $limit_min=$count_bornes[0]['f1']; //age
      $limit_max=$count_bornes[0]['f2'];
      $limit_unit=unite_t24($count_bornes[0]['f3'],$count_bornes[0]['f2'],$pdo,$resultat_unite); //pluriel pour tout le monde !

    //unite de la poso mini


    //unite de la poso maxi

  //unite de la frequence mini
/* pas utilise
    $frequence_mini_clair='';
    if ($frequence_mini_code) //ne rien afficher si non renseigne
    {
      $frequence_mini_clair=unite_t24($frequence_mini_code,$pdo,$resultat_unite);
    }
*/
    }
?>
	  <tr valign="top">
	    <th>
	      Fourchette des posologies
	    </th>
	    <td>
	      <a name="poso"></a><?php echo $de.$ligne_posologies['f2'].' '.$unite_min_clair.' '.$ligne_posologies['f4'].$a.$ligne_posologies['f7'].' '.$unite_max_clair.' '.$ligne_posologies['f9'].$de.$limit_min.$a.$limit_max.' '.$limit_unit.' de '.$duree_min.' '.$unite_duree_min_clair.' à '.$duree_max.' '.$unite_duree_max_clair; ?>
	    </td>
	  </tr>
<?php
  }
  $resultat_bornes->closeCursor();
  $resultat_bornes = NULL;
  if ($count_ce_medoc)
{
?>
	  <tr valign="top">
	    <th>
	      Classe racine
	    </th>
	    <td>
	      <a href="medocs.php?classe=<?php echo $count_chercher_classe_racine[0]['f0'] ?>&amp;button_classification=Chercher&amp;radio_dispo=<?php echo $radio_dispo?>&amp;radio_distri=<?php echo $_GET['radio_distri'] ?>&amp;radio_classe=<?php echo $radio_classe ?>"><?php echo $count_chercher_classe_racine[0]['f2'].'</a> ('.$count_chercher_classe_racine[0]['f0'].')' ?>
	    </td>
	  </tr>
	  <tr valign="top">
	    <th>
	      Classe 1
	    </th>
	    <td>
	      <a href="medocs.php?classe=<?php echo $count_chercher_classe_racine[0]['f0'] ?>&amp;classe1=<?php echo $count_chercher_classe_1[0]['f0'] ?>&amp;button_classification=Chercher&amp;radio_dispo=<?php echo $radio_dispo?>&amp;radio_distri=<?php echo $_GET['radio_distri'] ?>&amp;radio_classe=<?php echo $radio_classe ?>"><?php echo $count_chercher_classe_1[0]['f2'] ?></a><?php echo $count_chercher_classe_1[0]['f0'] ?>
	    </td>
	  </tr>
	  <tr valign="top">
	    <th>
	      Classe 2
	    </th>
	    <td>
	      <a href="medocs.php?classe=<?php echo $count_chercher_classe_racine[0]['f0'] ?>&amp;classe1=<?php echo $count_chercher_classe_1[0]['f0'] ?>&amp;classe2=<?php echo $count_chercher_classe_2[0]['f0'] ?>&amp;button_classification=Chercher&amp;radio_dispo=<?php echo $radio_dispo?>&amp;radio_distri=<?php echo $_GET['radio_distri'] ?>&amp;radio_classe=<?php echo $radio_classe ?>"><?php echo $count_chercher_classe_2[0]['f2'] ?></a><?php echo $count_chercher_classe_2[0]['f0'] ?>
	    </td>
	  </tr>
	  <tr valign="top">
	    <th>
	      Classe 3
	    </th>
	    <td>
	      <a href="medocs.php?classe=<?php echo $count_chercher_classe_racine[0]['f0'] ?>&amp;classe1=<?php echo $count_chercher_classe_1[0]['f0'] ?>&amp;classe2=<?php echo $count_chercher_classe_2[0]['f0'] ?>&amp;classe3=<?php echo $count_chercher_classe_3[0]['f0'] ?>&amp;button_classification=Chercher&amp;radio_dispo=<?php echo $radio_dispo?>&amp;radio_distri=<?php echo $_GET['radio_distri'] ?>&amp;radio_classe=<?php echo $radio_classe ?>"><?php echo $count_chercher_classe_3[0]['f2'] ?></a><?php echo $count_chercher_classe_3[0]['f0'] ?>
	    </td>
	  </tr>
	  <tr valign="top">
	    <th>
	      Groupe
	    </th>
	    <td>
	      <a href="medocs.php?classe=<?php echo $count_chercher_classe_racine[0]['f0'] ?>&amp;classe1=<?php echo $count_chercher_classe_1[0]['f0'] ?>&amp;classe2=<?php echo $count_chercher_classe_2[0]['f0'] ?>&amp;classe3=<?php echo $count_chercher_classe_3[0]['f0'] ?>&amp;groupe=<?php echo $count_ce_medoc[0]['classe_num'] ?>&amp;button_classification=Chercher&amp;radio_dispo=<?php echo $radio_dispo?>&amp;radio_distri=<?php echo $_GET['radio_distri'] ?>&amp;radio_classe=<?php echo $radio_classe ?>"><?php echo $count_ce_medoc[0]['classe_nom'].'</a> ('.$count_ce_medoc[0]['classe_num'].')'  ?>
	    </td>
	  </tr>
	  <tr>
<?php
}
?>
<!-- //t63=precautions precription
      //f0=code CUV
      //f2= libelle precaution francais
      //f3 = liste I ou II
      //f4 duree maxi pour une delivrance unique
      //f6 sites de prescription si ordinaire ou renouvellement de restreinte
      //f8 site de prescription initiale
      //fB Portee d'une prescription initiale apres laquelle le renouvellement n'est plus possible
      //fD ordonnance securisee ou chevauchement impossible-->

	    <th>
              Précautions de délivrance
            </th>
	    <td>
<?php
if ($count_regles_delivrance)
{
?>
	      <a name="Precautions_delivrance"></a><?php echo str_replace ('\n','<br />',$count_regles_delivrance[0]['f2']).'<br />'; if ($count_regles_delivrance[0]['f3']) echo "Liste ".$count_regles_delivrance[0]['f3']; 
}
?>
	    </td>
	  </tr>
<?php
  if ($count_ce_medoc)
{
?>

	  <tr valign="top">
	    <th>
	      Précautions
	    </th>
	    <td>
	      <a name="Precautions"></a><?php echo $count_ce_medoc[0]['vigi']; if ($count_ce_medoc[0]['psy']==1) echo ' : <br /><strong>Psychotrope</strong>'; if ($count_ce_medoc[0]['dopant']==1) echo '<br /><strong>Dopant</strong>'; if ( $count_regles_delivrance) { if ( $count_regles_delivrance[0]['f1']) echo '<br />Liste'. $count_regles_delivrance[0]['f1'];  if ( bin2hex($count_regles_delivrance[0]['fB'])=='01') echo '<br /><strong>Ordonnance produit d\'exception</strong>';  if ( bin2hex($count_regles_delivrance[0]['fC'])=='01') echo '<br /><strong>Déconditionnement autorisé</strong>'; echo '<br />'; if ($count_delivrance_2) echo $count_delivrance_2[0]['f1'];} ?>
	    </td>
	  </tr>
<?php
}
if ($count_prix)
{
?>
	  <tr valign="top">
	    <th>
	      Prix
	    </th>
	    <td>
	      <a name="Prix"></a>TTC : <?php echo $count_prix[0]['f3']; ?> € <?php  if ($count_rembt) { ?>taux <?php echo $count_rembt[0]['f1'] ;} ?>
	    </td>
	  </tr>
<?php
}
?>
	  <tr>
	    <th>
	      Durée maxi
	    </th>
	    <td>
	      <a name="duree"></a><?php if ( $count_regles_delivrance){if ( $count_regles_delivrance[0]['fA']) echo '<strong>Durée maximale pour une prescription initiale</strong> '.$count_regles_delivrance[0]['fA'].' jours';if ( $count_regles_delivrance[0]['f4']) echo '<strong>Durée maximale pour une délivrance unique</strong> '.$count_regles_delivrance[0]['f4'].' jours'; }?>
	    </td>
	  </tr>
	  <tr valign="top">
	    <th>
	      Évènements
	    </th>
	    <td>
	      <a name="event"></a>
	      <ul>
<?php 
  foreach ($count_evnt AS $ligne_evnt)
    echo '
		<li>
		  '.$ligne_evnt['f1'].' : '.substr($ligne_evnt['f2'],0,10).'
		</li>';
?>
	      </ul>
	    </td>
	  </tr>
<?php
if ($count_fabricant)
{
?>
	  <tr valign="top">
	    <th>
	      Laboratoire
	    </th>
	    <td>
	      <a name="labo"></a><?php echo $count_fabricant[0]['f2'].'<br />'; if ($count_adresse){ $count_adresse[0]['f3'].'<br />'.$count_adresse[0]['f4'].'<br />'.$count_adresse[0]['f5'].'<br />'.$count_adresse[0]['f6'].'<br />Tél : '.str_replace(" ","&nbsp;",$count_adresse[0]['f7']).'<br />'.$count_adresse[0]['fC']; if ($count_adresse[0]['fD']) echo '<br /><a href="http://'.$count_adresse[0]['fD'].'">'.$count_adresse[0]['fD'].'</a>'; }?>
	    </td>
	  </tr>
<?php
}
/*$sql_t4R="SELECT * FROM t4R WHERE f0=''";
t4R
  f0 = Code de la prescription restreinte réservée à la LPP
  f1= liaison t4E f0 ??
  f2=quantite
  f3=duree maxi
  f4=code unite duree tt -> 22.f0
  f5=delai avant renouvellt
  f6=code unite de f5 -> 22.f0
  f7=montant maxi
  f8=duree montant maxi
  f9=code unite de f8 -> 22.f0
  fA=commentaire
  fB=taille mini en cm
  fC=taille maximale
  fD=poids mini
  fE=poids maximal
  fF=pointure minimum
  fG=pointure maximal
  fH=age mini annees
  fI=age maxi
  fJ=femme enceinte

t4E entites remboursables
  f0=identifiant de classe. -> t4R.f1 ->>t4K.f1
  f1= nom en clair
  f2=noeud =null
  f3=classe mère ->  t4K.f1 ?
  f4=LPPR Liste des Prestations et Produits Remboursables
  f5=base de remboursement (TFR)
  f6=code prestation pour le remboursement
  f7= type de prestation (location)
  f8=code entente prealable O/N
  f8 = code entente préalable
  f9= prix public
  fE = remboursement indication

t4K = relation cuv-lppr
  f0 = CUV
  f1 = classe LPPR -> table codes lppr ->4E.f0
  f2 = nombre de classes par CUV

//Note : pour un meme CUV, il peut y avoir plusieurs classes LPPR, pas toutes liees a t4E.
*/

  $resultat_tfr=$pdo->prepare("SELECT t4E.f0 AS id_class, t4E.f5 AS tfr, t4K.f1 AS lien FROM t4E INNER JOIN t4K ON t4K.f1=t4E.f0 WHERE t4K.f0= ?");
  $resultat_tfr->bindValue(1, $cuv_medoc, PDO::PARAM_STR);
  $resultat_tfr->execute();
  $count_tfr=$resultat_tfr->fetchAll();
  $resultat_tfr->closeCursor();
  $resultat_tfr = NULL;
//echo $cuv_medoc;
if ($count_tfr)
{
?>
          <tr>
            <th>
              Classe LPPR
            </th>
            <td>
 <?php 
  echo 
	      $count_tfr[0]['id_class'];
 ?>
            </td>
          </tr>
          <tr>
            <th>
              <a name="tfr"></a>Base remboursable (TFR)
            </th>
            <td>
              <?php echo $count_tfr[0]['tfr'] ; ?>
            </td>
          </tr>
<?php
}

/*
Le SMR (service medical rendu)
t3C
  f0 identifiant unique SMR ou ASMR  ->3D.f1, 3D.f2
  f1 code produit ->t00.f0 = CIP ->t4Bf0
  f2 indications  S : SMR, A : ASMR 
  f3 relation 0 -> 106 -> t3E.f0
  f4 Indication SMR ASMR = texte en clair
  f5 = date
t3D
  f0 identifiant de la relation
  f1 identifiant SMR de la relation
  f2 identifiant ASMR de la relation 
t3E
  f0 identifiant, lien  sur 3C.f3
  f1 = type de SMR en clair 0->5 = colonne en clair 101 ->106 = code, voir en clair dans f2
  f2 = valeur en clair des types de f1 sup à 100
*/
  $cip_medoc='';
  if ($count_ce_medoc)
    $cip_medoc=$count_ce_medoc[0]['numero'];

  $resultat_chercher_smr=$pdo->prepare("SELECT t3C.f2 AS a_ou_s, t3C.f4 AS texte_smr, t3E.f1 AS code_smr, t3E.f2 AS asmr_en_clair FROM t3C INNER JOIN t3E ON t3C.f3=t3E.f0  WHERE t3C.f1= ?");
  $resultat_chercher_smr->bindValue(1, $cip_medoc, PDO::PARAM_STR);
  $resultat_chercher_smr->execute();
  $count_chercher_smr=$resultat_chercher_smr->fetchAll();
  $resultat_chercher_smr->closeCursor();
  $resultat_chercher_smr = NULL;

?>
	  <tr>
	    <th>
	      <a name="smr"></a>SMR
	    </th>
	    <td>
<?php
foreach ($count_chercher_smr AS $ligne_chercher_smr)
{
  echo $ligne_chercher_smr['a_ou_s'].' '.str_replace("?","'",$ligne_chercher_smr['texte_smr']).'  '.$ligne_chercher_smr['code_smr'].'  '.$ligne_chercher_smr['asmr_en_clair'].'<br />';
}
?>
	    </td>
	  </tr>
<!-- 4D les generiques -->
<?php
/*
t3F
  f0= CIP
  f1= groupe de produits
  f2= groupe de reference (1,6,7 ou 8) ->t1F.f0, avec t1F.f1= libelle
  f3=glossaire ->t1C.f0n t1C.f1= libellé
  f5 excipients a effet notoire en XML
*/
  $resultat_generique=$pdo->prepare("SELECT t3F.f2 AS reference, t1F.f1 AS libelle, t3F.f5 AS excipient, t3F.f1 AS groupe_generique  FROM t3F INNER JOIN t1F ON t3F.f2=t1F.f0 WHERE t3F.f0= ? AND t3F.f2 !='8'");
  $resultat_generique->bindValue(1, $cip_medoc, PDO::PARAM_STR);
  $resultat_generique->execute();
  $count_generique=$resultat_generique->fetchAll();
  $resultat_generique->closeCursor();
  $resultat_generique = NULL;
?>
	  <tr>
	    <th>
	      <a name="generique"></a>Générique
	    </th>
	    <td>
<?php
$groupe_generique='';

foreach ($count_generique AS $ligne_generique)
{
?>
	      <b>Référence</b> <?php echo $ligne_generique['libelle'] ?><br />
	      <b>Excipient à effet notoire</b> <?php echo $ligne_generique['excipient'] ?><br />
<?php
  $groupe_generique=$ligne_generique['groupe_generique'];
}

?>
	    </td>
	  </tr>
	  <tr>
	  <th>
	    Génériques
	  </th>
	  <td>
<?php
//On cherche les medicaments qui sont dans le meme groupe generique (t3F.f1)
  $resultat_les_generiques=$pdo->prepare("SELECT t4B.f2 AS ce_medicament FROM t4B INNER JOIN t3F ON t4B.f0=t3F.f0 WHERE t3F.f1= ? ");
  $resultat_les_generiques->bindValue(1, $groupe_generique, PDO::PARAM_STR);
  $resultat_les_generiques->execute();
  $count_les_generiques=$resultat_les_generiques->fetchAll();
  $resultat_les_generiques->closeCursor();
  $resultat_les_generiques = NULL;

foreach ($count_les_generiques AS $ligne_les_generiques)
{
  echo $ligne_les_generiques['ce_medicament'].'<br />';
}
?>
	    </td>
	  </tr>
	</table>
<?php
//  }
} //fin fiche medicament
?>
      </form>
    </div>

<?php
include("inc/footer.php");
?>
    </div>
    <p>
      <a href="http://validator.w3.org/check?uri=referer"><img
	  src="pics/valid-xhtml10.png" alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a>
    </p>
  </body>
</html>
