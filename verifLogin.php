<?php
// On demarre la session
session_start();
$loginOK = false;  
include("config.php");

try 
{
  $strConnection = 'mysql:host='.$host.';dbname='.$base; 
  $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); 
  $pdo = new PDO($strConnection, $loginbase, $pwd, $arrExtraParam); //Ligne 3; Instancie la connexion
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Ligne 4
}
catch(PDOException $e) {
    $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
    die($msg);
}

//Transmis par Manager de MedinTux
//login={{USER LOGIN MEDECIN}}&pass=&ID

if (isset($_GET['pass']))
{
  if ($login=$_GET['login'] AND $pass=$_GET['pass']) //on evite les login vides qui autrement connecteraient n'importe qui
  {
    if (isset ($_GET['ID'])) //le GUID du patient
      $ID=$_GET['ID'];
    else
      $ID='';
    if (preg_match('`::`',$login)) //si on a recu login::signataire
    {
      $tab=explode('::',$login);
      $login =$tab[0];
      $signataire=$tab[1];
    }
    else
    {
      $signataire=$login;
    }
//requete PREPARE
    $resultat=$pdo->prepare('SELECT Login FROM Personnes WHERE Login = ? AND PassWord=? ');
    $resultat->bindValue(1, $login, PDO::PARAM_STR);
    $resultat->bindValue(2, $pass, PDO::PARAM_STR);
    $resultat->execute();

    $count=$resultat->fetchAll();
    //Clore la requête préparée
    $resultat->closeCursor();
    $resultat = NULL;
    if ($count) //on a un utilisateur qui correspond
    {
      $_SESSION['login'] = $login."::".$signataire;//On cree la session
      if (isset($_REQUEST['cuv_medoc'])) //Pour medocs.php
      {
	header('location: medocs.php?cuv_medoc='.$_REQUEST['cuv_medoc'].'&radio_dispo=yes&radio_distri=4&radio_classe=1' ); //on va sur la page du medoc
	exit;
      }
      header('location: frame_patient.php?GUID='.$ID ); //on va sur la page du patient
      exit;
    }
    else //On arrive de MedinTux sans mot de passe valable -> page de login
    {
      header('location: index.php' );
      exit;
    }
  }
}
//Fin de l'acces par MedinTux

// On n'effectue les traitement qu'a la condition que les informations aient ete effectivement postees
if ( isset($_POST) && (!empty($_POST['login'])) && (!empty($_POST['password'])) ) 
{
  extract($_POST);  // login et password
//On convertit le mot de passe en crypte pour le comparer au mot de passe crypte dans la base de donnees
  $len = strlen($password);
  $pass_crypt = '';

  for ($i=0; $i<$len; $i++)
  {
    $val = ord($password{$i});
    $val = ($val << (($i + 1) & 0x000F)) ^ $val;
    $pass_crypt .= sprintf("%04X", $val);
  }
// On va chercher le mot de passe afferent a ce login

  $resultat=$pdo->prepare('SELECT Login, PassWord FROM Personnes WHERE Login = ? AND PassWord=? ');
  $resultat->bindValue(1, addslashes($login), PDO::PARAM_STR);
  $resultat->bindValue(2, $pass_crypt, PDO::PARAM_STR);
  $resultat->execute();

  $count=$resultat->fetchAll();
  if ($count)
  {
	$loginOK = true;
  }
  //Clore la requête préparée
  $resultat->closeCursor();
  $resultat = NULL;
}

// Si le login a ete valide on met les donnees en session
if ($loginOK) 
{
  if (isset($_REQUEST['select_sign']))
  {
    if ($_REQUEST['select_sign'])  //le signataire est renseigne
    {
      $select_sign=$_REQUEST['select_sign']; //nom en clair pris dans le deroulant
      if ($select_sign==$login) //le signataire est l'utilisateur
	$sign='OK';
    //on verifie que le signataire existe bien
      $resultat_utilisateur_delegue=$pdo->prepare('SELECT SignataireGUID FROM Personnes INNER JOIN user_perms ON Personnes.GUID=user_perms.FriendUserGUID WHERE Login= ? ');
      $resultat_utilisateur_delegue->bindValue(1, addslashes($login), PDO::PARAM_STR);
      $resultat_utilisateur_delegue->execute();

      $count_utilisateur_delegue=$resultat_utilisateur_delegue->fetchAll();
      $resultat_utilisateur_delegue->closeCursor();
      $resultat_utilisateur_delegue = NULL;
      
      $resultat_sign_login=$pdo->prepare('SELECT Login FROM Personnes WHERE GUID= ? ');
      foreach ($count_utilisateur_delegue AS $ligne)
      {
	$resultat_sign_login->bindValue(1, $ligne[0], PDO::PARAM_STR);
	$resultat_sign_login->execute();
	$count_sign_login=$resultat_sign_login->fetchAll();

	if ($select_sign==$count_sign_login[0]['Login']) //nom de login
	{
	  $sign='OK';
	  break;
	}    
      }
      $resultat_sign_login->closeCursor();
      $resultat_sign_login = NULL;

      if ($sign!='OK')
      {
      //le signataire n'est pas valable : on renvoie sur la page de login
	  header('location: index.php' );
	  exit;
      }
    }
    else //le signataire est vide
    {
      header('location: index.php' );
      exit;
    }
  }
  else //le signataire n'est pas donne
  {
    header('location: index.php' );
    exit;
  }
  $_SESSION['login'] = $login."::".$select_sign;
//$page est le nom de la page qui a ete initialement demandee
  $page=$_REQUEST['page'];

  if ($page)
  {
    header('location: '.$page.'.php' );
    exit;
  }
}
else //le login n'est pas valide
{
  if (empty($_POST['password']))
  {
    header('location: index.php?message=message3' );
    exit;
  } 
//	Le mot de passe ou le login est faux. Cause possible : verrouillage numerique ou majuscule actif
  header('location: index.php?message=message1' );
  exit;
}
?>
