<?php
/*
Page non affichable destinee a enregistrer les nouvelles donnees du terrain, envoyees par la page tableaux.php. Renvoie a tableaux.
*/
session_start() ;
include("config.php");

if (isset($_REQUEST['tableau_variables']))
  $tableau_variables=$_REQUEST['tableau_variables']; //trois dimensions : le titre, le numero de ligne (date) et le numero de la colonne
$date=$_REQUEST['date']; //deux dimensions : le titre, et le numero de ligne de valeurs
$title=$_REQUEST['title']; //tableau des titres de variables - 1 dimension - sert de cle aux autres.
$column=$_REQUEST['column']; //deux dimensions : titre et num de la colonne pour ce titre
$GUID_patient=$_REQUEST['patient'];
$id_terrain=$_REQUEST['id_terrain'];

$chaine_proprietes='';
if (isset($_REQUEST['proprietes_ordo']))
  $chaine_proprietes=$_REQUEST['proprietes_ordo']; //vide si inchange
$ordo_chronique='';
if (isset($_REQUEST['ordo_chronique']))
  $ordo_chronique=$_REQUEST['ordo_chronique'];//vide si inchange
/*
Exemple de terrain
[Antecedents]
 abcès amibien du cerveau = Médicaux , , ~A06.6~ , , , \r\n[Obstetrique]
 Grossesse = non
 Allaitement = non
[VAR Constantes de Base]
 Modèle = Date, Pouls, 31536000, 100, 50, pps, Tachycardie, Bradycardie
 Modèle = Date, SAO2, 31536000, 0, 92, %, , Hypoxie
 Modèle = Date, Temp, 31536000, 40.5, 36, °, Hyperthermie, Hypothermie
 Modèle = Date, Maxima, 31536000, 14, 9, cmHg, Hypertension, Hypotension
 Modèle = Date, Minima, 31536000, 9.5, 5, cmHg, Hypertension, Hypotension
 2015-01-12T22:44:07 = 70, 98, 37, 14, 9
[VAR Poids]
etc.
[Propriété Ordonnance]
 ASPRO 500mg Cpr B/20 = Renouvelable , 0
[Traitement]
<?xml version="1.0" encoding="ISO-8859-1" standalone="yes" ?>
<ordotext>
<html>
<head>
 <meta name="qrichtext" content="1" /> 
</head>
 <body text="#000000" style="font-size:9pt;font-family:MS Shell Dlg">
 <p align="left">\n<span style="font-weight:600;color:#000000">1) </span>\n\n<span style="font-weight:600;text-decoration:underline;color:#0000ff">ACIDE ACÉTYLSALICYLIQUE 500 milligrammes</span>\n<span style="font-weight:600;color:#464646"> <i><b>(ASPRO 500mg Cpr B/20)</b></i></span>\n<span style="font-weight:600;color:#000000"> <b>comprimé</b></span>\n<span style="font-weight:600;color:#000000"> <b>par voie orale</b> </span>\n<span style="font-weight:400;color:#000000"></span>\n<span style="font-weight:400;color:#000000"> </span>\n<span style="font-weight:400;color:#000000"> , traitement à poursuivre pendant 8 jours</span>\n<span style="font-weight:400;color:#000000"> </span></p>\n\n<p align="left">\n<br/>\n<br/>\n<span style="font-weight:400;font-size:7pt;color:#000000"><i>Ordonnance élaborée à partir de Medicatux version : 2.16.000 Dec 16 2013 14:09:50.<br/>Tous les contrôles sont effectués à partir des données suivantes : Datasemp Version : 216A date d'extraction : 02/12/2014</i> </span></p>\n</body>
</html>
</ordotext>
<ordoMedicaStruct>
<PosologieList>
  <Posologie>
    <numVersion>2</numVersion>
    <cip>3373285</cip>
    <pk></pk>
    <libelle></libelle>
    <sexe>M</sexe>
    <terrain></terrain>
    <doseMin></doseMin>
    <doseMax></doseMax>
    <doseUnit>7</doseUnit>
    <doseLimitMax>0</doseLimitMax>
    <factCorpQU>0</factCorpQU>
    <factCorpUnit>2</factCorpUnit>
    <periode>86400</periode>
    <equiCoeff>0</equiCoeff>
    <unitCoeff>101</unitCoeff>
    <nbPrises>0</nbPrises>
    <schemaPrise></schemaPrise>
    <divers>ASPRO 500mg Cpr B/20</divers>
    <extraPk></extraPk>
    <note></note>
    <posoAdjust>1</posoAdjust>
    <secabilite>4</secabilite>
    <numOrdre>1</numOrdre>
    <posoTextuelle></posoTextuelle>
    <ald>0</ald>
    <posoType>2</posoType>
    <duree>691200</duree>
    <IdTable>1</IdTable>
  </Posologie>
</PosologieList>
<Posologie_LAP_List>
<OrdoLine>
<gph>
<status></status>
<smr></smr>
 <smr_l></smr_l>
 <gph_html><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN" "http://www.w3.org/TR/REC-html40/strict.dtd">\n<html><head><meta name="qrichtext" content="1" /><style type="text/css">\np, li { white-space: pre-wrap; }\n</style></head><body style=" font-family:'Sans Serif'; font-size:10px; font-weight:400; font-style:normal;">\n<p style=" margin-top:0px; margin-bottom:0px; margin-left:0px; margin-right:0px; -qt-block-indent:0; text-indent:0px;"><span style=" font-family:'arial,verdana,sans-serif'; font-size:10px; font-weight:600; color:#ff0000;">1)</span><span style=" font-size:10px;"> </span><span style=" font-family:'arial,verdana,sans-serif'; font-size:10px; font-weight:600; text-decoration: underline; color:#637867;">ACIDE ACÉTYLSALICYLIQUE 500 milligrammes</span><span style=" font-size:10px;"> </span><span style=" font-family:'arial,verdana,sans-serif'; font-size:10px; font-style:italic; color:#a6c9ad;">(ASPRO 500mg Cpr B/20)</span><span style=" font-size:10px;"> </span><span style=" font-family:'arial,verdana,sans-serif'; font-size:10px; font-weight:600; color:#637867;">comprimé par voie orale<br /></span><span style=" font-size:10px;"> </span><span style=" font-family:'arial,verdana,sans-serif'; font-size:10px; color:#637867;">, traitement à poursuivre pendant 8 jours</span><span style=" font-size:10px;"><br /></span></p></body></html></gph_html>
<gph_ald></gph_ald>
<gph_pk></gph_pk>
<gph_id>3373285</gph_id>
<gph_it>CIP</gph_it>
<gph_dr>12-01-2015 22:43:54</gph_dr>
<gph_dt>12-01-2015 22:43:53</gph_dt>
<gph_df>20-01-2015 22:43:53</gph_df>
<gph_na>ASPRO 500mg Cpr B/20</gph_na>
<gph_dcl>
  <gph_dc>
    <gph_dcn>ACIDE ACÉTYLSALICYLIQUE</gph_dcn> <gph_dcp>500</gph_dcp> <gph_dcu>milligramme</gph_dcu>
  </gph_dc>
</gph_dcl>
<gph_cy>J8R0</gph_cy>
<gph_sb>1</gph_sb>
<gph_dci>2</gph_dci>
<gph_uf>comprimé</gph_uf>
<gph_voie>orale</gph_voie>
<gph_fmin></gph_fmin>
<gph_fmax></gph_fmax>
<gph_funit></gph_funit>
<gph_pmin></gph_pmin> <gph_pmax></gph_pmax>
<gph_punit>milligramme</gph_punit>
<gph_pfc></gph_pfc>
<gph_pfcunit>Kg</gph_pfcunit>
<gph_pqbyuf>500</gph_pqbyuf>
<gph_dmin></gph_dmin>
<gph_dmax></gph_dmax>
<gph_dunit></gph_dunit> <gph_nm>0</gph_nm>\n <gph_cm>NOT_LITERAL</gph_cm>\n <gph_in></gph_in>\n <gph_co></gph_co>\n</gph>\n<gps>\n</gps>
<gpi_engine>Datasemp Version : 216A date d'extraction : 02/12/2014</gpi_engine>
<gpi_user>
  <m_Login>delafond</m_Login>
  <m_usual_name>Delafond</m_usual_name>
  <m_forename>Gerard</m_forename>
  <m_Nu_RPPS>810000344779</m_Nu_RPPS>
  <m_Nu_ClefRPPS></m_Nu_ClefRPPS>
</gpi_user>
</OrdoLine>
</Posologie_LAP_List>
</ordoMedicaStruct>\n\0\0

*/
if ( !isset( $_SESSION['login'] ) )
{
  header('location: index.php?page=liste' );
  exit;
}
$tab_login=explode("::",$_SESSION['login']);
$user=$tab_login[0];

//connexion a drtux
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

//connexion a la base vidal
$basemed="DatasempTest";
try 
{
  $strConnection = 'mysql:host='.$host.';dbname='.$basemed; 
  $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); 
  $pdomed = new PDO($strConnection, $loginbase, $pwd, $arrExtraParam); // Instancie la connexion
  $pdomed->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) 
{
  $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
  die($msg);
}

if (isset ($_REQUEST['submit_brut'])) //on valide le terrain brut et on s'en va
{
  $terrain_brut=$_REQUEST['terrain_brut'];
  if ($terrain_brut)
  {
    $sql_modifier_terrain=$pdo->prepare("UPDATE RubriquesBlobs SET RbDate_DataRub=? WHERE RbDate_PrimKey=?");
    $sql_modifier_terrain->bindValue(1,$terrain_brut, PDO::PARAM_STR);
    $sql_modifier_terrain->bindValue(2,$id_terrain, PDO::PARAM_STR);
    $sql_modifier_terrain->execute();
    $sql_modifier_terrain->closeCursor();
  }
  else //terrain vide ->on supprime
  {
    $sql_supprimer_blob=$pdo->prepare("DELETE FROM RubriquesBlobs WHERE RbDate_PrimKey=?");
    $sql_supprimer_blob->bindValue(1,$id_terrain, PDO::PARAM_STR);
    $sql_supprimer_blob->execute();
    $sql_supprimer_blob->closeCursor();
    $sql_supprimer_header=$pdo->prepare("DELETE FROM RubriquesHead WHERE RbDate_RefBlobs_PrimKey=?");
    $sql_supprimer_header->bindValue(1,$id_terrain, PDO::PARAM_STR);
    $sql_supprimer_header->execute();
    $sql_supprimer_header->closeCursor();
  }
  header ('location:tableaux.php?GUID='.$GUID_patient.'&affichage=terrains');
  exit;
}

function local_to_fr($date,$date_format) //fonction pour convertir les dates du terrain en fr, car stockees ainsi 
{
  $list_date=explode ("-",$date);
  if ($date_format=='fr')
  {
    $date=$date;
  }
  elseif ($date_format=='en')
    $date=$list_date[1].'-'.$list_date[0].'-'.$list_date[2];
  else //iso
    $date=$list_date[2].'-'.$list_date[1].'-'.$list_date[0];
  return $date;
}

//on cherche les droits de l'utilisateur
$sql_chercher_droits=$pdo->prepare("SELECT GUID,Droits FROM Personnes WHERE Login=?");
$sql_chercher_droits->bindValue(1, $user, PDO::PARAM_STR);
$sql_chercher_droits->execute();
$ligne_chercher_droits=$sql_chercher_droits->fetch(PDO::FETCH_ASSOC);
$sql_chercher_droits->closeCursor();

$GUID_user=$ligne_chercher_droits['GUID'];
$droits_user=$ligne_chercher_droits['Droits'];

if (isset($_GET['utilisateur_autorisant']))
  $utilisateur_autorisant=$_GET['utilisateur_autorisant'];
else
  $utilisateur_autorisant=$_SESSION['login'];

//recuperation des donnees de l'url

$ddr='';
if (isset($_REQUEST['ddr'])) //pas de ddr pour les hommes
  $ddr=$_REQUEST['ddr'];
$coche_allaitement='';
if (isset($_REQUEST['coche_allaitement']))
  $coche_allaitement=$_REQUEST['coche_allaitement'];
$date=$_REQUEST['date']; //date d'une nouvelle ligne de tableau
$tableau_variables=$_REQUEST['tableau_variables']; //les valeurs a ajouter pour les tableaux

$select_intolerance=array();
$intolerance_active=''; //atcd medicamenteux actif yes-no
$intolerance_ald='';
$text_atcd_full='';
$select_atcd=array();
/*select_family_atcd
select_genre_atcd
*/
$atcd_active='';//atcd de pathologie actif yes-no
$atcd_ald='';
$atcd_full='';
$intolerance_full='';
if ($_REQUEST['select_atcd'])
{
  $select_atcd=explode(' code cim10 ',$_REQUEST['select_atcd']);
  if ($_REQUEST['atcd_active']=='yes')
    $atcd_active='Actif';
  elseif ($_REQUEST['atcd_active']=='no')
    $atcd_active='Passé';
  if ($_REQUEST['atcd_ald']=='yes')
    $atcd_ald='ALD';
  $atcd_full=$select_atcd[0].' = '.$_REQUEST['select_family_atcd'].'('.$_REQUEST['select_genre_atcd'].') , '.$atcd_active.' , ~'.$select_atcd[1].'~ , '.$_REQUEST['comment_atcd'].' , '.local_to_fr($_REQUEST['date_atcd'],$date_format).','.$atcd_ald.'
';
}

if ($_REQUEST['select_intolerance'])
{
  $select_intolerance=explode(' code substance ',$_REQUEST['select_intolerance']);
  if ($_REQUEST['intolerance_active']=='yes')
    $intolerance_active='Actif';
  elseif ($_REQUEST['intolerance_active']=='no')
    $intolerance_active='Passé';

  if ($_REQUEST['intolerance_ald']=='yes')
    $intolerance_ald='ALD';

  $intolerance_full=$select_intolerance[0].' = '.$_REQUEST['select_family_intolerance'].'('.$_REQUEST['select_genre_intolerance'].') , '.$intolerance_active.' ,-'.$select_intolerance[1].'- , '.$_REQUEST['comment_intolerance'].' , '.local_to_fr($_REQUEST['date_intolerance'],$date_format).','.$intolerance_ald.'
';
}

if ($_REQUEST['text_atcd_libre'])
{
  $active='';
  if (isset($_REQUEST['text_active']))
  {
    if ($_REQUEST['text_active']=='yes')
      $active='Actif';
    elseif ($_REQUEST['text_active']=='no')
      $active='Passé';
  }
  $text_ald='';
  if (isset($_REQUEST['text_ald']))
  {
    if ($_REQUEST['text_ald']=='yes')
      $text_ald='ALD';
  }
  $text_atcd_full=$_REQUEST['text_atcd_libre'].' = '.$_REQUEST['select_family'].'('.$_REQUEST['select_genre'].') , '.$active.' , , '.$_REQUEST['comment_atcd_libre'].' , '.local_to_fr($_REQUEST['date_atcd_libre'],$date_format).','.$text_ald.'
';
}
echo 'full'.$text_atcd_full;
if ($ddr) 
  $grossesse=$ddr;
else
  $grossesse="non";

if ($coche_allaitement)
  $allaitement="oui";
else
  $allaitement="non";

if (isset($_REQUEST['select_ordo']))
{
  $cip_fond=array();
  $select_ordo=$_REQUEST['select_ordo']; //le numero d'ordonnance a mettre en traitement de fond ou vide si pas de tt de fond ou X si inchange
  if ($select_ordo!="X") //X = inchange
  {
    $chaine_proprietes=''; //on vide les anciens medocs
    $sql_chercher_ordo=$pdo->prepare("SELECT * FROM RubriquesBlobs WHERE RbDate_PrimKey=?"); //on cherche l'ordo par son numero
    $sql_chercher_ordo->bindValue(1, $select_ordo, PDO::PARAM_STR);
    $sql_chercher_ordo->execute();
    $ligne_chercher_ordo=$sql_chercher_ordo->fetch(PDO::FETCH_ASSOC);
    $sql_chercher_ordo->closeCursor();
    
    $ordo_chronique=$ligne_chercher_ordo['RbDate_DataRub']; //le contenu de l'ordo

  //On cherche les noms des medocs pour la rubrique Propriétés Ordonnance
    $expr="`(<cip>)(.*)(<\/cip>)`i"; //rechercher les CIP du traitement de fond pour les interactions
    $split_ordo=explode("\n", $ordo_chronique); //on recupere chaque ligne de l'ordo

    foreach ($split_ordo as $ligne_brut)
    {
      if(preg_match($expr,$ligne_brut,$tab))
      {
        $cip_fond[]=$tab[2]; //On met toutes les cip dans un tableau
      }
    }
    $compteur_medocs=0;
    
    $sql_chercher_libelle=$pdomed->prepare("SELECT f3 FROM t00 WHERE f2=?");
    foreach ($cip_fond AS $this_cip_fond) //On recherche les libelles des medocs de l'ordo pour les mettre en traitement de fond
    {
        $sql_chercher_libelle->bindValue(1, $this_cip_fond, PDO::PARAM_STR);
        $sql_chercher_libelle-> execute();  
        $ligne_chercher_libelle=$sql_chercher_libelle->fetch(PDO::FETCH_ASSOC);
      $chaine_proprietes.=$ligne_chercher_libelle['f3'].' = Renouvelable , '.$compteur_medocs.'
  ';
//$chaine_proprietes contient la prescription standard de tous les medocs en clair pour affichage
      $compteur_medocs++;
    }
    $sql_chercher_libelle->closeCursor();
  } //fin mode nouvelle ordonnance
}
else //pas de tt de fond
  $chaine_proprietes=''; //on vide les anciens medocs
if ($atcd_full) //N'ajouter des retours chariots que s'il existe une valeur
  $atcd_full.="
";
if ($intolerance_full)
  $intolerance_full.="
";
if ($text_atcd_full)
  $text_atcd_full.="
";

$update="[Antecedents]
".$atcd_full.$intolerance_full.$text_atcd_full; //provient des champs a remplir
if (isset($_REQUEST['check_atcd']))
{
  $check_atcd=$_REQUEST['check_atcd'];
  foreach ($check_atcd AS $this_atcd) //les ATCD deja remplis, si leur coche est bien mise.
    $update.=$this_atcd."
";
}
$update.="[Obstetrique]
Grossesse = ".$grossesse."
Allaitement = ".$allaitement." 
";
$num_title=0;
foreach ($title AS $this_title)
{
  $update.=$this_title."
";
  foreach ($column[$num_title] AS $this_column)
  {
    $update.=$this_column."
";
  }
  $num_date=0;
  foreach ($date[$num_title] AS $this_date)
  {
    $values='';
    if (array_filter($tableau_variables[$num_title][$num_date])) //pour sauter les lignes vides
    {
      foreach ($tableau_variables[$num_title][$num_date] AS $this_variable)
      {
	$values.=str_replace(',','.',$this_variable).","; //la virgule ajoute un champ intempestif ->passer en point decimal
      }
      $longueur_chaine=strlen($values);
      $update.=$this_date."=".substr($values, 0, $longueur_chaine-1)." 
";//on enleve la virgule finale pour ne pas avoir une colonne vide supplementaire
    }
    $num_date++;
  }
  $num_title++;
}
$update.="[Propriété Ordonnance]
".
$chaine_proprietes."
[Traitement]
".$ordo_chronique; //le traitement de fond

$sql_modifier_terrain=$pdo->prepare("UPDATE RubriquesBlobs SET RbDate_DataRub=? WHERE RbDate_PrimKey=?");
$sql_modifier_terrain->bindValue(1, $update, PDO::PARAM_STR);
$sql_modifier_terrain->bindValue(2, $id_terrain, PDO::PARAM_STR);
$sql_modifier_terrain->execute();
$sql_modifier_terrain->closeCursor();
//on renvoie sur la page
header ('location:tableaux.php?GUID='.$GUID_patient.'&affichage=terrains');
exit;
?>
