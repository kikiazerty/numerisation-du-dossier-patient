<?php
session_start() ;
if ( !isset( $_SESSION['login'] ) ) 
{
  header('location: index.php?page=liste' );
  exit;
}
include("config.php");

//connexion a drtux
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

//les dates de recherche selon la langue
if ($date_format=='fr')
  $date=date('d-m-Y', date('U'));
elseif ($date_format=='en')
  $date=date('m-d-Y', date('U'));
else 
 $date=date('Y-m-d', date('U')); // la date du jour

//initialisation des jours de la semaine
$text_jsemaine=array(0=>"Dimanche",1=>"&nbsp;&nbsp;Lundi&nbsp;&nbsp;",2=>"&nbsp;&nbsp;Mardi&nbsp;&nbsp;",3=>"Mercredi",4=>"&nbsp;&nbsp;Jeudi&nbsp;&nbsp;",5=>"Vendredi",6=>"&nbsp;Samedi&nbsp;");

//On recupere les variables dans l'URL
if (isset($_GET['GUID']))
  $GUID=$_GET['GUID'];
else
{
  header('location: index.php?page=liste' );
  exit;
}
$sql_identite=$pdo->prepare('SELECT FchGnrl_NomDos,FchGnrl_Prenom FROM IndexNomPrenom WHERE FchGnrl_IDDos= ?');
$sql_identite->bindValue(1, $GUID, PDO::PARAM_STR);
$sql_identite->execute();
$ligne_identite=$sql_identite->fetch(PDO::FETCH_ASSOC);
$sql_identite->closeCursor();

$Nom=$ligne_identite['FchGnrl_NomDos'];
$Prenom=$ligne_identite['FchGnrl_Prenom'];

//Recherche des couleurs de RdV
$sqlcouleur=$pdo->prepare('SELECT * FROM color_profils GROUP BY Name');
$sqlcouleur->execute();

while ($lignecouleur=$sqlcouleur->fetch(PDO::FETCH_ASSOC))
{
  $couleur[$lignecouleur["Name"]]=$lignecouleur["Color"];
}
$sqlcouleur->closeCursor();

$sql_liste_rdv=$pdo->prepare('SELECT * FROM agenda WHERE GUID= ? ORDER BY Date_Time DESC;');
$sql_liste_rdv->bindValue(1, $GUID, PDO::PARAM_STR);
$sql_liste_rdv->execute();
$ligne_liste_des_rdv=$sql_liste_rdv->fetchAll(PDO::FETCH_ASSOC);
$count_liste_rdv=count($ligne_liste_des_rdv);

include("inc/header.php");
?>
    <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen" />
    <title>
      Agenda - Patient <?php echo $Nom; ?>
    </title>

    <script type="text/javascript" src="oXHR.js">
    </script>

  <script type="text/javascript">
//<![CDATA[
function change_status_this_appointment(string,number)
{
//fonction  pour changer en tâche de fond les statuts des rdv de la liste
  f1.location.href="modif_status.php?id="+number+"&status="+string;
  return false;
}
//]]>
  </script>

<!-- css special pour le tableau des rdv en mode impression -->
    <style type="text/css"  media="print">
	    td:nth-child(12) { display: none; }  
	    td:nth-child(13) { display: none; }  
	    th:nth-child(12) { display: none; }  
	    th:nth-child(13) { display: none; }  
    </style>
  </head>

  <body style="font-size:<?php echo $fontsize; ?>pt" >
<!-- Zone virtuelle invisible pour permettre l'execution d'une page PHP de mise a jour dynamique des statuts de rendez-vous -->
<div style="display:none">
<iframe name="f1" id="f1"> </iframe>
</div>
    <div class="conteneur">
   <div class="groupe">
      <h1>
	Rendez-vous de <?php echo $Nom." ".$Prenom?>
      </h1>
<?php
if ($count_liste_rdv) //afficher les en-tetes seulement si resultat
{
?>
   <div class="tableau">
      <table>
	<col /><col /><col /><col /><col /><col /><col /><col /><col /><col />
	<tr><!--Les en-tetes du tableau d'affichage des RDV -->
	  <th class="fond_th">
	    Date
	  </th>
	  <th class="fond_th">
	    Heure
	  </th>
	  <th class="fond_th">
	    Dur&eacute;e
	  </th>
	  <th class="fond_th">
	    Type
	  </th>
	  <th class="fond_th">
	    Statut
	  </th>
	  <th class="fond_th">
	    Notes
	  </th>
	  <th class="fond_th">
	    Pris par
	  </th>
	  <th class="fond_th">
	    Pris avec
	  </th>
	</tr>
<?php
}
foreach ($ligne_liste_des_rdv AS $ligne_liste_rdv)
{
  $tableau_date_time=explode(" ",$ligne_liste_rdv["Date_Time"]); //on separe la date de l'heure - format iso
  $date=$tableau_date_time[0]; //format iso
  $time=substr($ligne_liste_rdv["Date_Time"],11,5);
  list($annee,$mois,$jour)=explode("-",$date);
  if ($date_format=='fr')
    $date=$jour.'-'.$mois.'-'.$annee;
  elseif ($date_format=='en')
    $date=$mois.'-'.$jour.'-'.$annee;
  else
    $date=$annee.'-'.$mois.'-'.$jour;
//Recuperation du type de rdv pour y affecter une couleur
 $type_RV=$ligne_liste_rdv["Type"];
  if (isset($couleur["$type_RV"]))
    $couleur_ligne=$couleur["$type_RV"];
  else
    $couleur_ligne='#F6C9FF';
  $jd = date("w",mktime(0,0,0,$mois,$jour,$annee));//numero du jour de la semaine
	
//Affichage des rendez-vous
  $color=$ligne_liste_rdv["status"];
  echo "
	<tr>
	  <td class=\"fond_td\">
	    ".$text_jsemaine[$jd]." ".$date."
	  </td>
	  <td class=\"fond_td\">
	    ".$time."
	  </td>
	  <td class=\"fond_td\">
	    ".$ligne_liste_rdv["Duree"]."'
	  </td>
	  <td style=\"background:".$couleur_ligne.";\" class=\"fond_td\">
	    ".$ligne_liste_rdv["Type"]."
	  </td>
	  <td style=\"background:";
  if (isset($color_status[$color]))
    echo $color_status[$color];
  else
    echo "white";
  echo "\" class=\"fond_td\">";
?>
		    <select name="status" id="status_saisie<?php echo $ligne_liste_rdv["PrimKey"] ?>" onchange="change_status_this_appointment(this.value,<?php echo $ligne_liste_rdv["PrimKey"] ?>)" >
<?php
foreach ($status_rdv AS $ce_rdv)
{
?>
			<option value="<?php echo addslashes($ce_rdv) ?>"<?php 
    if ($ce_rdv==stripslashes($ligne_liste_rdv["status"]))
      echo " selected='selected'"; 
?> >
<?php 
    echo $ce_rdv 
?>
			  </option>
<?php
}
?>
			</select><br />
<?php

echo	  "</td>
	  <td class=\"fond_td\">
	    ".$ligne_liste_rdv["Note"]."
	  </td>
	  <td class=\"fond_td\">
	    ".$ligne_liste_rdv["RDV_PrisPar"]."
	  </td>
	  <td class=\"fond_td\">
	    ".$ligne_liste_rdv["RDV_PrisAvec"]."
	  </td>";
?>
	</tr>
<?php
}
$sql_liste_rdv->closeCursor();

if ($count_liste_rdv)
{
?>
      </table>
    </div>
<?php
}
?>
     </div>
<?php
include("inc/footer.php");
?>