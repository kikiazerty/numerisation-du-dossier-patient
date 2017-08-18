<?php
//page utilisee par AJAX pour chercher les medicaments a ajouter a l'ordonnance
//URL de type     recherche_drug.php?nom_medoc=adep&type_requete=Nom+commercial&radio_distri=4&tri_par=n&select_type=1&age_jours=4000&poids=90&compatibles=yes&sexe=M

header("Content-Type: text/plain");

//echo str_replace('_','',$_SERVER['REQUEST_URI']); 

$nom_medoc = (isset($_GET["nom_medoc"])) ? $_GET["nom_medoc"] : NULL; //la cle de recherche :nom, DCI, indication ou classe ATC
$type_requete = (isset($_GET["type_requete"])) ? $_GET["type_requete"] : NULL; //recherche par Nom commercial, DCI, Indication ou ATC
$tri_par = (isset($_GET["tri_par"])) ? $_GET["tri_par"] : NULL; //tri par nom ou prix = n ou p
$select_type = (isset($_GET["select_type"])) ? $_GET["select_type"] : NULL; //medicaments, accessoires, dietetique... 1 à 6
$age_jours = (isset($_GET["age"])) ? $_GET["age"] : NULL; //age patient

$poids = (isset($_GET["poids"])) ? $_GET["poids"] : NULL; //poids patient en kg
$compatibles = (isset($_GET["compatibles"])) ? $_GET["compatibles"] : NULL; // yes ou no pour les medocs compatibles avec le poids et l'age seulement
$brackets = (isset($_GET["brackets"])) ? $_GET["brackets"] : NULL; // yes ou no pour les crochets DOM TOM
$sexe_patient = (isset($_GET["sexe"])) ? $_GET["sexe"] : NULL; // M ou F
//$age_jours = 615;
$basemed="DatasempTest";

include("config.php");

try 
{
  $strConnection = 'mysql:host='.$host.';dbname='.$basemed;
  $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
  $pdo = new PDO($strConnection, $loginbase, $pwd,$arrExtraParam); // Instancie la connexion
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) 
{
  $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
  die($msg);
}

$list_select=''; 
//Les requetes preparees qui seront utilisees par la suite
$request_prixboite="SELECT f3 FROM t01 WHERE f0=?"; //f3 = prix de la boite, f0=cuv
$request_ville_hopital="SELECT f2 FROM t02 WHERE f0=?"; //f2 code de lieu de commercialisation
$request_dispo="SELECT f3 FROM t02 WHERE f0=?"; //code si commercialise
if ($tri_par=='n')
  $request_cip_atc="SELECT f0 FROM t4B WHERE fA LIKE ? ORDER BY f2";
else //tri par prix
{
 /* $request_cip_atc="SELECT t4B.f0, t01.f3
  FROM t01
  INNER JOIN t00 ON t00.f1 = t01.f0
  INNER JOIN t4B ON t4B.f0 = t00.f0
  WHERE t4B.fA LIKE ?
  ORDER BY t01.f3";*/
  $request_cip_atc="SELECT t4B.f0, t01.f3  FROM t01,t00,t4B WHERE t00.f1 = t01.f0 AND t4B.f0 = t00.f0 AND t4B.fA LIKE ?  ORDER BY t01.f3";
}
$request_cuv="SELECT t00.f1 AS cuv, t00.fG AS libelle_complet FROM t00 WHERE f0=? AND fK LIKE ?";

//les requetes de terrain
if ($compatibles=="yes")
{
  $sql_chercher_medoc=$pdo->prepare("SELECT f0 FROM t00 WHERE f1= ? "); 
  $sql_posologies=$pdo->prepare("SELECT f0 FROM t2P WHERE f1=?"); //f0 = code poso, f1=code medicament
  $sql_bornes=$pdo->prepare("SELECT * FROM t2L WHERE f4= ?"); //Plusieurs reponses selon la problematique (poso, sexe)
}
//Pour le sexe, chercher s'il existe une ligne de t2L dont le f6 ne vaut pas NULL. Si oui, on l'analyse et on regarde s'il vaut M ou F

function verif_compat ($pdo,$cuv,$sql_chercher_medoc,$sql_bornes,$sql_posologies,$age_jours,$poids,$sexe_patient) //pour verifier si ce medoc est compatible avec le patient : poids, age, sexe
{
//return 1;
  $sql_chercher_medoc->bindValue(1, $cuv, PDO::PARAM_STR);
  $sql_chercher_medoc->execute();
  $ligne_chercher_medoc=$sql_chercher_medoc->fetch(PDO::FETCH_ASSOC);
  $sql_chercher_medoc->closeCursor();

  $sql_posologies->bindValue(1, $ligne_chercher_medoc['f0'], PDO::PARAM_STR); //151401 pour Lombax //000010 pour Daivonex //->ne donne rien pour les accessoires //000498 pour oflocet unidose //141809 monoflocet //508209 oflocet 200 //minidril 502840
  $sql_posologies->execute();
  $all_posologies=$sql_posologies->fetchAll(PDO::FETCH_ASSOC);
  $sql_posologies->closeCursor();
/* $all_posologies
3 valeurs pour oflocet 200
6090 //tous sexes de 6 a 18 ans
6091 //femme de 18 a 65 ans
42123 //homme de 18 a 150 ans
*/
//6093 pour oflocet unidose
//5408 pour minidril
  $multiplicateur['38']=1;
  $multiplicateur['42']=7;
  $multiplicateur['43']=30;
  $multiplicateur['44']=365;

  if (count($all_posologies)==0)
    return 1; //pas de poso pour ce produit, donc pas de limitation

  foreach ($all_posologies AS $ligne_posologies)
  {
    $sql_bornes->bindValue(1, $ligne_posologies['f0'], PDO::PARAM_STR); //6093 pour oflocet unidose //5508 monoflocet
    $sql_bornes->execute();
    $all_bornes=$sql_bornes->fetchAll(PDO::FETCH_ASSOC);
    $sql_bornes->closeCursor();
    if (empty($all_bornes)) //on n'a pas de ligne de borne age ou poids
    {
      return 1; // le medicament est toujours accepte
    }
    $sexe='OK';
    $borne_age='OK';
    $borne_poids='OK';
//on peut trouver plusieurs lignes de bornes pour une posologie (sexe et age par ex)
    foreach ($all_bornes AS $ligne_bornes) //on examine la ligne sexe et la ligne bornes pour ce code poso
    {
      if ($ligne_bornes['f6']!=$sexe_patient AND $ligne_bornes['f6']!=NULL) //on a une ligne, mais elle montre une incompatibilite avec le sexe
      {
	$sexe='NOT_OK';
      }
      if ($ligne_bornes['f3']=='44' OR $ligne_bornes['f3']=='43' OR $ligne_bornes['f3']=='42' OR $ligne_bornes['f3']=='38') //les ages 44 = ans, 43 = mois 42=semaines 38=jours 1 = kg 0=rien //le zero est traite par ailleurs (juste le sexe)
      {
	$borne_inf=$ligne_bornes['f1']*$multiplicateur[$ligne_bornes['f3']];
	$borne_sup=$ligne_bornes['f2']*$multiplicateur[$ligne_bornes['f3']];
	if ($age_jours>=$borne_inf AND $age_jours<=$borne_sup OR !$age_jours) //on affiche la ligne trouvee si age correspond ou si pas d'age du tout
	{
	  $borne_age='OK';
	}
	else
	  $borne_age='NOT_OK';
      }
      elseif ($ligne_bornes['f3']=='1') //poso par poids
      {
	$borne_inf=$ligne_bornes['f1'];
	$borne_sup=$ligne_bornes['f2'];
	if ($poids!='inconnu')
	{
	  if ($poids>=  $borne_inf AND $poids<=$borne_sup)
	  {
	    $borne_poids='OK';
	  }
	  else
	    $borne_poids='NOT_OK';
	}
	else //poids inconnu : on accepte.
	  $borne_poids='OK';

      } //les ages ou les poids ne correspondent pas ->on refuse le medicament
//test echo 'line'.$ligne_posologies['f0'].'age'.$age_jours.'sexe'.$sexe.'bornes'.$bornes.'<br />';
//on a termine d'examiner toutes les conditions liees a cette poso
    } //on a balaye toutes les lignes de bornes pour cette posologie
    if ($borne_age=='OK' AND $borne_poids=='OK' AND $sexe=='OK')
    {
      return 1; //on a au moins une poso, on valide et on quitte !
    }
  } //si on n'a trouve aucune poso qui correspond
  return 0;
}

function stripAccents($string)
{
  return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}


function prix_boite ($cuv,$request_prixboite,$pdo)
{
  $resultat=$pdo->prepare($request_prixboite);
  $resultat->bindValue(1,$cuv, PDO::PARAM_STR);
  $resultat->execute();
  $count=$resultat->fetchAll();
  $resultat->closeCursor();
  $resultat = NULL;

  return $count[0]['f3'];
}

function dispo_ville_hopital($cuv,$request_ville_hopital,$request_prixboite,$request_dispo,$name,$pdo)
{
//On cherche si commercialise en ville ou hopital
  $radio_distri = (isset($_GET["radio_distri"])) ? $_GET["radio_distri"] : NULL;  //hopitaux ou officines = 2 ou 4
  $data='';
  $resultat=$pdo->prepare($request_ville_hopital);
  $resultat->bindValue(1,$cuv, PDO::PARAM_STR);
  $resultat->execute();
  $count=$resultat->fetchAll();
  $resultat->closeCursor();
  $resultat = NULL;

  if ($count[0]['f2']==$radio_distri) //On ne cherche pas si commercialise si pas dans la bonne rubrique ville-hopital
  {
//On cherche le code de dispo selon le CUV
    $resultat=$pdo->prepare($request_dispo);
    $resultat->bindValue(1,$cuv, PDO::PARAM_STR);
    $resultat->execute();
    $count=$resultat->fetchAll();
    $resultat->closeCursor();
    $resultat = NULL;

    if ($count[0]['f3']=='0') // medicament commercialise seulement
    {
      $data=$name.' Prix : '.prix_boite($cuv,$request_prixboite,$pdo).' €'.'_'.$cuv;
    }
  } //fin ville-hopital
  return ($data);
}

if (strlen($nom_medoc) > 2) 
{
  $nom_medoc="%".stripAccents(urldecode($_REQUEST['nom_medoc']))."%";
  $data=array(); //initialiation du tableau des medicaments trouves

  if ($type_requete=='Nom commercial')
  {
//rechercher le cpg du medoc et sa classe
//t4B=table generale - f0=cpg f2=nom fA= numero de classe
//t42 = table des classes - f0= num de classe, 
//on cherche si le medoc est commercialise
//on cherche le CUV = code unite de vente
//f1=numero
//fG=nom long
//fK=classement
    $string_brackets="";
    if ($brackets=='no')
      $string_brackets="AND t00.fG NOT LIKE '%[%]%'";
    if ($tri_par=='p') //en fait, il faut trier par prix d'UCD, autrement dit le comprime.
    {
      $request_chercher_cuv="SELECT t00.f1,t00.fG FROM t00,t01 WHERE CAST(fG AS CHAR) LIKE ? AND fK LIKE ? AND t00.f1=t01.f0 $string_brackets ORDER BY t01.f3"; //fG = nom medoc
    }
    else //par nom ou pas precise
      $request_chercher_cuv="SELECT f1,fG FROM t00 WHERE CAST(fG AS CHAR) LIKE ? AND fK LIKE ? $string_brackets ORDER BY f3"; //fG = nom medoc
    $resultat=$pdo->prepare($request_chercher_cuv);
    $resultat->bindValue(1,$nom_medoc, PDO::PARAM_STR);
    $resultat->bindValue(2,$select_type.'%', PDO::PARAM_STR); //1 a 6 - premier chiffre - 5 pour accessoires
//    $resultat->bindValue(3,"AND t00.fG NOT LIKE '%[%'", PDO::PARAM_STR); //EXCLURE RES NOMS DE MEDOCS CONTENANT [ si coche pas mise
    $resultat->execute();
    $count=$resultat->fetchAll();
    $resultat->closeCursor();
    $resultat = NULL;

    foreach($count AS $this_cuv) //on balaye toutes les cuv pour trouver celles qui sont commercialisees - ex 10002710K7 pour Lombax
    {
      $cuv=$this_cuv['f1'];
      $libelle_complet=$this_cuv['fG'];

      if ($data=dispo_ville_hopital($cuv,$request_ville_hopital,$request_prixboite,$request_dispo,$libelle_complet,$pdo))
      {
	if ($compatibles=="yes")
	{
	  if (verif_compat ($pdo,$cuv,$sql_chercher_medoc,$sql_bornes,$sql_posologies,$age_jours,$poids,$sexe_patient))
	  {
	    $list_select.='|'.$data;
	  }
	}
	else //si la coche compatibles seulement pas cochee, on liste tout
{
	  $list_select.='|'.$data;
//echo 'age'.$age_jours; 
}
      }
    }
  } //fin nom commercial
  elseif ($type_requete=='DCI') 
  {
    if ($tri_par=='p')
    {
      $request_recherche_par_substance="
      SELECT t54.f0 AS num_subst,
      t00.fG AS nom_medoc,
      t00.f0 AS num_medoc ,
      t00.f1 AS cuv_medoc 
      FROM t54 
      INNER JOIN t07 ON t54.f0=t07.f2 
      INNER JOIN t00 ON t07.f0=t00.f0 
      INNER JOIN t01 ON t01.f0=t00.f1 
      INNER JOIN t08 ON t08.f0=t07.f0
      WHERE CAST(t54.f2 AS CHAR) LIKE ? 
      AND t54.f1='FRA'  
      AND fK LIKE ? 
      AND t08.fB='0'
      AND t08.f1=t07.f1
       ORDER BY t01.f3"; //On supprime la recherche par DCI anglaise
 //     $request_chercher_cuv="SELECT t00.f1,t00.fG FROM t00,t01 WHERE CAST(fG AS CHAR) LIKE ? AND fK LIKE ? AND t00.f1=t01.f0 ORDER BY t01.f3"; //fG = nom medoc
    }
    else //par nom ou pas precise //t54 =noms substances, t07=substances par numeros,t00=conditionnements,t08=composants par numeros, t08.f0=principe actif
      $request_recherche_par_substance="
      SELECT t54.f0 AS num_subst,
      t00.fG AS nom_medoc,
      t00.f0 AS num_medoc ,
      t00.f1 AS cuv_medoc 
      FROM t54 
      INNER JOIN t07 ON t54.f0=t07.f2 
      INNER JOIN t00 ON t07.f0=t00.f0 
      INNER JOIN t08 ON t08.f0=t07.f0
      WHERE CAST(t54.f2 AS CHAR) LIKE ? 
      AND t54.f1='FRA'  
      AND t00.fK LIKE ? 
      AND t08.fB='0'
      AND t08.f1=t07.f1
      ORDER BY t00.f3"; //On supprime la recherche par DCI anglaise 
      //t00.fK=t14.f0=famille SEMP (medicaments, accessoires...)
      

    $resultat=$pdo->prepare($request_recherche_par_substance);
    $resultat->bindValue(1,$nom_medoc, PDO::PARAM_STR);
    $resultat->bindValue(2,$select_type.'%', PDO::PARAM_STR);
    $resultat->execute();
    $count=$resultat->fetchAll();
    $resultat->closeCursor();
    $resultat = NULL;

//T54.f2= nom substance, t54.f0 = numero substance
//t07.f0 =t00.f0 = cpg 
    foreach ($count AS $this_medoc)
    {
      $cuv=$this_medoc['cuv_medoc'];//code unite de vente
      $this_line=$this_medoc['nom_medoc'];
//On cherche si commercialise en ville ou hopital
      if ($data=dispo_ville_hopital($cuv,$request_ville_hopital,$request_prixboite,$request_dispo,$this_line,$pdo))
      {
	$list_select.='|'.$data;
      }
    }
  } //fin DCI
  elseif ($type_requete=='Indication')
  {
     $text_indication='%'.strtolower(addslashes($_REQUEST['nom_medoc'])).'%';
/*
t52 = etats pathologiques selon produit
  f0 = code du terme ->t50.f0 (glossaire) et ->t55.f0 (liaison CIM-X)
  f1 = CIP ->t00.f0
  f2 = nature du lien (contre-indique...) -> t1C.f0 (liste des codes) ou t51.f2
  f3 = frequence
  f5 = libelle en clair de l'effet
  f6 = code document ->t45.f0
*/
    if ($tri_par=='p')
    {
      $request_cip_indication="SELECT t52.f1 AS cip, t00.fG AS libelle, t00.f1 AS cuv FROM t52 , t50 , t00 ,t01 WHERE t52.f2='2994' AND Lower(t50.f1) LIKE ? AND t52.f1=t00.f0 AND t52.f0=t50.f0 AND fK LIKE ? AND t01.f0=t00.f1 ORDER BY t01.f3";
    }
    else //tri par nom
      $request_cip_indication="SELECT t52.f1 AS cip, t00.fG AS libelle, t00.f1 AS cuv FROM t52 , t50 , t00  WHERE t52.f2='2994' AND Lower(t50.f1) LIKE ? AND t52.f1=t00.f0 AND t52.f0=t50.f0 AND fK LIKE ? ORDER BY t00.f3";
    $resultat=$pdo->prepare($request_cip_indication);
    $resultat->bindValue(1,$text_indication, PDO::PARAM_STR);
    $resultat->bindValue(2,$select_type.'%', PDO::PARAM_STR); //1 a 6
    $resultat->execute();
    $count=$resultat->fetchAll();
    $resultat->closeCursor();
    $resultat = NULL;
    foreach ($count AS $this_indication)
    {
      $cuv=$this_indication['cuv'];//code unite de vente
      $this_line=$this_indication['libelle'];

      if ($data=dispo_ville_hopital($cuv,$request_ville_hopital,$request_prixboite,$request_dispo,$this_line,$pdo))
      {
	$list_select.='|'.$data;
      }
    }
  } //fin indication
  elseif ($type_requete=='ATC')
  {
    $codeATC=strtoupper($nom_medoc); //les codes ATC sont stockes en majuscules
    $resultat=$pdo->prepare($request_cip_atc);
    $resultat->bindValue(1,$codeATC.'%', PDO::PARAM_STR);
    $resultat->execute();
    $count=$resultat->fetchAll();
    $resultat->closeCursor();
    $resultat = NULL;
    foreach ($count AS $this_cuv)
    {
      $resultat=$pdo->prepare($request_cuv);
      $resultat->bindValue(1,$this_cuv[0], PDO::PARAM_STR);
      $resultat->bindValue(2,$select_type.'%', PDO::PARAM_STR);
      $resultat->execute();
      $count_cuv=$resultat->fetchAll();
      $resultat->closeCursor();
      $resultat = NULL;
      if ($count_cuv)
      {
	$cuv=$count_cuv[0]['cuv'];
 	$libelle_complet=$count_cuv[0]['libelle_complet'];
  //recherche ville-hopital
	if ($data=dispo_ville_hopital($cuv,$request_ville_hopital,$request_prixboite,$request_dispo,$libelle_complet,$pdo))
	{
 	  $list_select.='|'.$data;
	}
      }
    }
  } //fin ATC
}
if ($list_select=='')
  $list_select='NOP';
echo $list_select;
?>