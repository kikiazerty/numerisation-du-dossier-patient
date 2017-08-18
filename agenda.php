<?php
session_start() ;
if ( !isset( $_SESSION['login'] ) ) 
{
  header('location: index.php?page=agenda' );
  exit;
}
include("config.php");

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

//on ajoute le champ lock si n'existe pas

$sql_ajouter_lock=$pdo->prepare("ALTER TABLE `agenda` ADD `lock` BOOLEAN NOT NULL DEFAULT '0'");
try 
{
  $sql_ajouter_lock->execute();
}
catch (PDOException $e) 
{
}
$sql_ajouter_lock->closeCursor();

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

//les dates de recherche selon la langue
$date=iso_to_local(date('Y-m-d', date('U')),$date_format);

//initialisation des jours de la semaine
$text_jsemaine=array(0=>"Dimanche",1=>"&nbsp;&nbsp;Lundi&nbsp;&nbsp;",2=>"&nbsp;&nbsp;Mardi&nbsp;&nbsp;",3=>"Mercredi",4=>"&nbsp;&nbsp;Jeudi&nbsp;&nbsp;",5=>"Vendredi",6=>"&nbsp;Samedi&nbsp;");

//On recupere les variables dans l'URL
$Type="%%";
if (isset($_GET['Type'])) //consultation, visite, etc.
{
  $Type=$_GET["Type"];
  if ($Type=="Tous")
    $Type='%%';
}

$status="%%";
if (isset($_GET['status'])) //Rdv termine, annule, etc.
{
  $status=stripslashes($_GET["status"]);
  if ($status=="Tous")
    $status='%%';
}
//echo $status;
$adresse='';
if (isset($_GET['Adresse']))
  $adresse=$_GET['Adresse'];

$intervenants='seul';  
if (isset($_GET['intervenants']))
  $intervenants=$_GET['intervenants'];

if (isset($_GET['critere_recherche']))
  $critere_recherche=$_GET['critere_recherche'];
else
  $critere_recherche="Nom";

if (isset($_GET['tri']))
  $tri=$_GET['tri'];
else
  $tri="Date_Time";

if (isset($_GET['id_rdv']))
  $id_rdv=$_GET['id_rdv'];
else 
  $id_rdv='';

if (isset($_GET['GUID']))
  $GUID="%".$_GET['GUID']."%";
else
  $GUID='%';

if (isset($_GET['RDV_PrisPar']))
  $RDV_PrisPar=$_GET['RDV_PrisPar'];
if (isset($_GET['Duree']))
  $Duree=$_GET['Duree'];
else
  $Duree='';
if (isset($_GET['Nom']))
  $Nom=$_GET['Nom'];
else
  $Nom="";
if (isset($_GET['nom'])) //provient de la fiche patient
  $nom=$_GET['nom'];
else
  $nom="";
if (isset($_GET['Prenom']))
  $Prenom=$_GET['Prenom'];
else
  $Prenom='';
if (isset($_GET['Tel']))
  $Tel=$_GET['Tel'];
else
  $Tel='';
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

if (isset($_GET['Note']))
  $Note=$_GET['Note'];
else
  $Note='';

if (isset($_GET['modif_rdv']))
  $modif_rdv=$_GET['modif_rdv'];

$envoyer='';
$bouton_envoyer='';
$Date='';

$critere_tri['Date_Time']="Date";
$critere_tri['Nom']="Nom";

$tab_login=explode("::",$_SESSION['login']);
$user=$tab_login[0];
if (isset($_GET['utilisateur_autorisant'])) //si on recupere un UA
{
//on le met en session
  if (!preg_match('`specialite`',$_GET['utilisateur_autorisant']))
    $_SESSION['login'] = $user."::".$_GET['utilisateur_autorisant'];
  $tab_login=explode("::",$_SESSION['login']);
}
$signuser=$tab_login[1];

if (isset($_GET['bouton_envoyer']) OR isset($_GET['envoyer']))
{
  if (isset($_GET['bouton_envoyer']))
  {
    $bouton_envoyer=$_GET['bouton_envoyer'];
  //Initialisation des dates
    if ($bouton_envoyer=="Modifier") // si on est en mode de modification ou d'ajout de rdv
  //la cle Ajouter semble ne servir a rien ici
    {
      $debut=$fin=$_GET['debut']; // on recupere la date dans l'URL
    }
    elseif ($bouton_envoyer=="Rendez-vous") // Si on vient du fichier patient ou de l'agenda
    {
      if (isset($_GET['jour']))
	$debut=$fin=$Date=$_GET['jour'];
      else
	$debut=$fin=$Date=$_GET['debut'];
      $Nom=$_GET['Nom'];
    }
    elseif ($bouton_envoyer=="Annuler")
    {
      $debut=$_GET['debut']; //locale
      $fin=$_GET['fin'];

      $sql_chercher_caracteristiques=$pdo->prepare('SELECT Date_Time,Duree,Type FROM agenda WHERE PrimKey= ?');
      $sql_chercher_caracteristiques->bindValue(1, $id_rdv, PDO::PARAM_STR);
      $sql_chercher_caracteristiques->execute();
      $ligne_chercher_caracteristiques=$sql_chercher_caracteristiques->fetch(PDO::FETCH_ASSOC);
      $sql_chercher_caracteristiques->closeCursor();

      $horodatage=$ligne_chercher_caracteristiques['Date_Time'];
      $Duree=$ligne_chercher_caracteristiques['Duree'];
      $Type=$ligne_chercher_caracteristiques['Type'];
      $status="%";
      $Nom='';
      $Prenom='';
      $GUID='';
      $Tel='';
      $Note='';

      $sql_inserer_vierge=$pdo->prepare('INSERT INTO agenda (Date_Time,Duree,RDV_PrisPar,RDV_PrisAvec,Type,status,Adresse) VALUES (?,?,?,?,?,"Non attribué","")');
      $sql_inserer_vierge->bindValue(1, $horodatage, PDO::PARAM_STR);
      $sql_inserer_vierge->bindValue(2, $Duree, PDO::PARAM_STR);
      $sql_inserer_vierge->bindValue(3, $user, PDO::PARAM_STR);
      $sql_inserer_vierge->bindValue(4, $signuser, PDO::PARAM_STR);
      $sql_inserer_vierge->bindValue(5, addslashes($Type), PDO::PARAM_STR);
      $sql_inserer_vierge->execute();
      $sql_inserer_vierge->closeCursor();

      $sql_annuler=$pdo->prepare('UPDATE agenda SET status="Annulé", RDV_PrisPar= ? WHERE  PrimKey= ?');
      $sql_annuler->bindValue(1, $user, PDO::PARAM_STR);
      $sql_annuler->bindValue(2, $id_rdv, PDO::PARAM_STR);
      $sql_annuler->execute();
      $sql_annuler->closeCursor();
    }
  }
  if   (isset($_GET['envoyer']))
  {
    if ($envoyer=="Chercher") //Si on a appuye sur le bouton de recherche ou si on vient d'une suppression
    {
      $debut=$_GET['debut']; //locale
      $fin=$_GET['fin'];
    }
    elseif ($envoyer=="Rendez-vous") 
    {
      $debut=$_GET['debut'];
      $fin=$_GET['debut'];
      $Date=$_GET['debut'];
      $Nom=$_GET['Nom'];
    }
    else //boutons fleches
    {
      $debut=$_GET['debut'];
      $fin=$_GET['fin'];
    }
  }
}
else //lancement sans argument
{
  $envoyer="";
  $debut=$fin=date('d-m-Y', date('U')); // date du jour
}

$debut_sql=local_to_iso($debut,$date_format);
$fin_sql=local_to_iso($fin,$date_format);

if ($fin_sql<$debut_sql)
{
  $fin_sql=$debut_sql; 
}
$debut_sql=$debut_sql." 00:00:00";
$fin_sql=$fin_sql." 23:59:59";

if ($intervenants=='specialite')
{
  $sql_specialite_intervenant=$pdo->prepare('SELECT Qualite FROM Personnes WHERE Login = ?');
  $sql_specialite_intervenant->bindValue(1, $user, PDO::PARAM_STR);
  $sql_specialite_intervenant->execute();
  $ligne_specialite_intervenant=$sql_specialite_intervenant->fetch(PDO::FETCH_ASSOC);

  $specialite=$ligne_specialite_intervenant['Qualite'];
  $sql_specialite_intervenant->closeCursor();

  $sql_chercher_specialiste=$pdo->prepare("SELECT Login FROM Personnes WHERE Qualite LIKE ? AND Login !=''");
  $sql_chercher_specialiste->bindValue(1, $specialite, PDO::PARAM_STR);
  $sql_chercher_specialiste->execute();
 
  $liste_intervenants_pour_sql='';
  $compteur=0;
  while ($ligne_chercher_specialiste=$sql_chercher_specialiste->fetch(PDO::FETCH_ASSOC))
  {
    $user_spe=addslashes($ligne_chercher_specialiste['Login']);
    $compteur++;
    if ($compteur==1)
      $liste_intervenants_pour_sql='AND ( RDV_PrisAvec =\''.$user_spe.'\' ';
    else //autres tours de la boucle
      $liste_intervenants_pour_sql=$liste_intervenants_pour_sql.' OR RDV_PrisAvec=\''.$user_spe.'\'';
  }
  $sql_chercher_specialiste->closeCursor();

  $liste_intervenants_pour_sql=$liste_intervenants_pour_sql.')';
}
elseif ($intervenants=='tous')
{
  $liste_intervenants_pour_sql='';
}
else // intervenant = seul
{
  $user=addslashes($user);
  $liste_intervenants_pour_sql="AND RDV_PrisAvec='".$signuser."'" ;
}
if (isset($_REQUEST['from_page'])) //si on vient du fichier patient ou de la liste - ne tient pas compte de l'identite
{

  $sql_liste_rdv=$pdo->prepare("SELECT * FROM agenda WHERE Date_Time BETWEEN ? AND ? $liste_intervenants_pour_sql ORDER BY Date_Time;");
  $sql_liste_rdv->bindValue(1, $debut_sql, PDO::PARAM_STR);
  $sql_liste_rdv->bindValue(2, $fin_sql, PDO::PARAM_STR);
}
else //si on vient de la page agenda
{
  //Si l'on est en mode de recherche par le bouton Chercher ou les boutons Aujourd'hui et fleches
  if (isset($_GET['envoyer']))
  {
    $envoyer=$_GET['envoyer'];
//les fleches
    if ($envoyer=='Précédent') //»
    {
      $debut=$fin=iso_to_local($_GET['precedent'],$date_format);
      $debut_sql=$_GET['precedent'].' 00:00:00';
      $fin_sql=$_GET['precedent'].' 23:59:59';
    }
    elseif ($envoyer=='Suivant') //«
    {
      $debut=$fin=iso_to_local($_GET['suivant'],$date_format);
      $debut_sql=$_GET['suivant'].' 00:00:00';
      $fin_sql=$_GET['suivant'].' 23:59:59';
    }
    elseif (preg_match('`Aujourd`',$envoyer,$tab))
    {
      $debut=$fin=iso_to_local(date('Y-m-d', date('U')),$date_format); // date du jour
      $debut_sql=date('Y-m-d', date('U')).' 00:00:00';
      $fin_sql=date('Y-m-d', date('U')).' 23:59:59';
    }

    $Nom="%$Nom%";
    $Prenom="%$Prenom%";
    if (isset ($_REQUEST['check_name'])) //on tient compte du nom dans la recherche de rendez-vous seulement si specifie
    {
      $sql_liste_rdv=$pdo->prepare("SELECT * FROM agenda WHERE Date_Time BETWEEN ? AND ? AND Nom LIKE ? AND Prenom LIKE ? AND GUID LIKE ? $liste_intervenants_pour_sql AND Type LIKE ? AND status LIKE ? ORDER BY $tri;");
      $sql_liste_rdv->bindValue(1, $debut_sql, PDO::PARAM_STR);
      $sql_liste_rdv->bindValue(2, $fin_sql, PDO::PARAM_STR);
      $sql_liste_rdv->bindValue(3, addslashes($Nom), PDO::PARAM_STR);
      $sql_liste_rdv->bindValue(4, addslashes($Prenom), PDO::PARAM_STR);
      $sql_liste_rdv->bindValue(5, addslashes($GUID), PDO::PARAM_STR);
      $sql_liste_rdv->bindValue(6, addslashes($Type), PDO::PARAM_STR);
      $sql_liste_rdv->bindValue(7, addslashes($status), PDO::PARAM_STR);
    }
    else //pas de nom
    {
      $sql_liste_rdv=$pdo->prepare("SELECT * FROM agenda WHERE Date_Time BETWEEN ? AND ? $liste_intervenants_pour_sql AND Type LIKE ? AND status LIKE ? ORDER BY $tri;");
      $sql_liste_rdv->bindValue(1, $debut_sql, PDO::PARAM_STR);
      $sql_liste_rdv->bindValue(2, $fin_sql, PDO::PARAM_STR);
      $sql_liste_rdv->bindValue(3, addslashes($Type), PDO::PARAM_STR);
      $sql_liste_rdv->bindValue(4, addslashes($status), PDO::PARAM_STR);
    }
  }
  else //au lancement sans argument
  {
    $sql_liste_rdv=$pdo->prepare("SELECT * FROM agenda WHERE Date_Time BETWEEN ? AND ? $liste_intervenants_pour_sql ORDER BY Date_Time;");
    $sql_liste_rdv->bindValue(1, $debut_sql, PDO::PARAM_STR);
    $sql_liste_rdv->bindValue(2, $fin_sql, PDO::PARAM_STR);
  }
}
$sql_liste_rdv->execute();
$ligne_liste_rdv_all=$sql_liste_rdv->fetchAll(PDO::FETCH_ASSOC);
$sql_liste_rdv->closeCursor();
$count_liste_rdv=count($ligne_liste_rdv_all);

//Recherche des couleurs de RdV
$sqlcouleur=$pdo->prepare("SELECT Name,Color,Duree FROM color_profils");
$sqlcouleur->execute();
$lignecouleur_all=$sqlcouleur->fetchAll(PDO::FETCH_ASSOC);
$sqlcouleur->closeCursor();

include("inc/header.php");

?>
  <meta name="keywords" lang="fr" content="Agenda pour MedinTux" />
  <title>Agenda de <?php echo $signuser ?></title>
  
   <script type="text/javascript">
//<![CDATA[
function  fillFields()
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
  
<!--   inutilise -->
  <script type="text/javascript">
//<![CDATA[
function  valider_patient()
{
//pour valider directement le rendez-vous a partir du deroulant des noms trouves -inutilisee
//Array ( [utilisateur_autorisant] => ines [RDV_PrisAvec] => delafond [intervenants] => seul [Date] => 16-01-2013 [Heure] => 00 [Minutes] => 00 [Type] => Consultation [Duree] => 15 [status] => Statut non défini [GUID] => [Nom] => [Prenom] => [Tel] => [RDV_PrisPar] => delafond [Note] => [fin] => 16-01-2013 [precedent] => 2013-01-15 [today_debut] => 2013-01-16 [suivant] => 2013-01-17 ) 
/*  var nomprenom=document.getElementById("select_patient").value.split('_');
  var nom=nomprenom[1];
  var prenom=nomprenom[2];
  var sign_user=document.getElementById("utilisateur_autorisant").value;
  var prisavec=document.getElementById("RDV_PrisAvec").value;
  var intervenants=document.getElementById("intervenants").value;
  var date=document.getElementById("datepicker").value;
  var heure=document.getElementById("Heure").value;
  var minutes=document.getElementById("Minutes").value;
  var type=document.getElementById("Type").value;
  var duree=document.getElementById("Duree").value;
  var status=document.getElementById("status_saisie").value;
  var GUID=nomprenom[0];
  var tel=document.getElementById("Tel").value;
  var prispar=document.getElementById("RDV_PrisPar").value;
  var note=document.getElementById("Note").value;
  var adresse=document.getElementById("Adresse").value;
  var fin=document.getElementById("datepickeur").value;
  var precedent=document.getElementById("precedent").value;
  var today_debut=document.getElementById("today_debut").value;
  var suivant=document.getElementById("suivant").value;
  
  window.location.href="validation_rdv.php?utilisateur_autorisant="+sign_user+"&RDV_PrisAvec="+prisavec+"&intervenants="+intervenants+"&Date="+date+"&Heure="+heure+"&minutes="+minutes+"&Type="+type+"&Duree="+duree+"&status="+status+"&GUID="+GUID+"&Nom="+nom+"&Prenom="+prenom+"&Tel="+tel+"&RDV_PrisPar="+prispar+"&Note="+note+"&fin="+fin+"&precedent="+precedent+"&today_debut="+today_debut+"&suivant="+suivant+"&Adresse="+adresse+"&envoyer=Ajouter";
  */
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
        
  <script type="text/javascript">
//<![CDATA[
function Invers()//pour inverser les coches 
{
  temp = document.forms['form_coches'].elements.length -2 ;
  for (i=0; i < temp; i++)
  {
    if(document.forms['form_coches'].elements[i].checked == 1)
    {
      document.forms['form_coches'].elements[i].checked = 0;
    }
    else 
    {
      document.forms['form_coches'].elements[i].checked = 1
    }
  }
}
//]]>
  </script>

  <script type="text/javascript">
//<![CDATA[
function change_status_this_appointment(string,number)
{
//fonction  pour changer sans recharger la page les statuts des rdv de la liste
  f1.location.href="modif_status.php?id="+number+"&status="+string; //marche pour name
  return false;
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

  <script type="text/javascript">
//<![CDATA[
function verif_tous()//pour ne pas enregistrer une fiche avec le type Tous, on substitue a la volee les valeurs par defaut
{
  // si la valeur du champ nom est vide
  if (document.forms['form_jour'].elements['Nom'].value == "") 
  {
    // sinon on affiche un message
    alert("Saisissez au moins le nom");
    // et on indique de ne pas envoyer le formulaire
    return false;
  }
  else 
  {
    // les données sont ok, on continue    
    if (document.forms['form_jour'].elements['Type'].value== 'Tous')
    {
      document.forms['form_jour'].elements['Type'].value= '<?php echo $rdv_default[$signuser] ?>';
    }
    if (document.forms['form_jour'].elements['status_saisie'].value== 'Tous')
    {
      document.forms['form_jour'].elements['status_saisie'].value= 'Statut non défini';
    }
    return true;
  }
}
//]]>
  </script>

<?php
include 'calendar_javascript.php';
?>
  <script type="text/javascript">
//<![CDATA[
function mettre_valeur(value)
{
//duree des rdv automatique selon le type de rdv
  var type=new Array;
<?php
//creation de variables javascript pour les durees des rdv
  foreach ($lignecouleur_all AS $lignecouleur)
  {
    $name=$lignecouleur['Name'];
    echo "type[\"".$name."\"]=\"".$lignecouleur['Duree']."\";"; 
  }
?>
  document.forms['form_jour'].Duree.value = type[value]; // on ecrit le resultat dans Duree
}
//]]>
  </script>
  <script type="text/javascript">
//<![CDATA[
function change_status(value)
{
//passer automatiquement le statut du rdv en statut non defini si remplissage du nom et statut non attribue initialement
  if (document.getElementById('status_saisie').value=='Non attribué')
  {
    if (value.length>0)
    {
    //suppose que le statut Non defini est le 4e dans la liste
    //plus elegant avec une moulinette qui cherche tous les statuts connus et trouve le numero de Non defini
    document.getElementById('status_saisie').selectedIndex=4;
    }
  }
}
//]]>
  </script>

  <script type="text/javascript">
//<![CDATA[
function chercher_patient()
// on ouvre dans une fenêtre
{ 
  nom=document.getElementById('Nom').value;
  prenom=document.getElementById('Prenom').value;
  window.open('recherche_patient_agenda.php?Nom='+nom+'&Prenom='+prenom,'Choisir','width=800,height=550,top=50,left=50,toolbar=no,scrollbars=yes,resizable=yes,location=no'); 
  if (document.getElementById('status_saisie').value=='Non attribué')
  {
    document.getElementById('status_saisie').selectedIndex=4; //remettre le statut en "non défini"
  }
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
<!-- <iframe id="f1"> </iframe> -->
<div style="display:none">
<iframe name="f1" id="f1"> </iframe>
<!-- <iframe id="f1"> </iframe> -->
</div>
  <div class="conteneur">
	
<!-- // insertion du menu d'en-tete	 -->
<?php
$anchor="Agenda";
include("inc/menu-horiz.php");
//recherche des autres agendas lisibles

$sql_utilisateur_delegue=$pdo->prepare('SELECT * FROM Personnes INNER JOIN user_perms ON Personnes.GUID=user_perms.FriendUserGUID WHERE Login= ? ORDER BY Qualite');
$sql_utilisateur_delegue->bindValue(1, $user, PDO::PARAM_STR);
$sql_utilisateur_delegue->execute();
$ligne_utilisateur_delegue_all=$sql_utilisateur_delegue->fetchAll(PDO::FETCH_ASSOC);
$sql_utilisateur_delegue->closeCursor();

$nombre_utilisateurs="";
$Qualite='';

?>
    <div class="groupe noPrint">
      <form action="validation_rdv.php" method="post" id="form_jour">
	<h1>
	  Agenda de 
	  <select name="utilisateur_autorisant" id="utilisateur_autorisant" onchange="select_user=form.submit()">
<?php

$sql_select_droits=$pdo->prepare('SELECT Droits FROM Personnes WHERE Login= ?');
$sql_select_droits->bindValue(1, $user, PDO::PARAM_STR);
$sql_select_droits->execute();
$ligne_select_droits=$sql_select_droits->fetch(PDO::FETCH_ASSOC);
$sql_select_droits->closeCursor();

if (preg_match('/sgn/',$ligne_select_droits['Droits'])) //ne pas proposer un utilisateur qui ne serait pas signataire.
{
?>
	    <option value="<?php echo $user ?>" >
	      <?php echo $user ?>
	    </option>
<?php
}
foreach ($ligne_utilisateur_delegue_all AS $ligne_utilisateur_delegue)
{
  $sql_utilisateur_autorisant=$pdo->prepare('SELECT Login,Qualite,Nom,Prenom FROM Personnes WHERE GUID= ?');
  $sql_utilisateur_autorisant->bindValue(1, $ligne_utilisateur_delegue['SignataireGUID'], PDO::PARAM_STR);
  $sql_utilisateur_autorisant->execute();
  $ligne_utilisateur_autorisant=$sql_utilisateur_autorisant->fetch(PDO::FETCH_ASSOC);
  $sql_utilisateur_autorisant->closeCursor();

  $sql_select_droits=$pdo->prepare('SELECT Login,Qualite,Nom,Prenom,Droits FROM Personnes WHERE GUID= ?');
  $sql_select_droits->bindValue(1, $ligne_utilisateur_delegue['SignataireGUID'], PDO::PARAM_STR);
  $sql_select_droits->execute();
  $ligne_select_droits=$sql_select_droits->fetch(PDO::FETCH_ASSOC);
  $sql_select_droits->closeCursor();

  if (preg_match('/agc/',$ligne_utilisateur_delegue['FriendUserDroits']) AND preg_match('/sgn/',$ligne_select_droits['Droits']))//peut prendre les rdv et est signataire
  {
    $nombre_utilisateurs++;
    if ($Qualite!=$ligne_utilisateur_delegue['Qualite'])
    {
//les intertitres de specialite
      echo "
	    <option value=\"specialite-".$ligne_utilisateur_autorisant['Login']."\"";
      if ($intervenants=='specialite')
      {
	if ($specialite==$ligne_utilisateur_autorisant['Qualite'])
	  echo " selected='selected' "; //on positionne le deroulant par defaut pour la specialite en cours
      }
      echo ">
			  --".$ligne_utilisateur_autorisant['Qualite']."--
	    </option>";
      $Qualite=$ligne_utilisateur_autorisant['Qualite']; //on change la valeur de la variable pour que le sous-titre de specialite n'apparaisse plus pour les intervenants suivants de la specialite
    }
?>
	    <option value="<?php echo $ligne_utilisateur_autorisant['Login'];?>" <?php if ($ligne_utilisateur_autorisant['Login']==$signuser) echo 'selected="selected"' ?> >
	      <?php echo $ligne_utilisateur_autorisant['Login'].' ('.$ligne_utilisateur_autorisant['Nom'].' '.$ligne_utilisateur_autorisant['Prenom'];?>)
	    </option>
<?php
  }
}
?>
	  </select>
<?php
  if($intervenants=='specialite') echo ' et toute sa spécialité'; elseif ($intervenants=='tous') echo ' - Tous agendas' 
?>
	</h1>
<div class="login">
		<fieldset class="fieldset_login">
		  <legend>
<?php
if ($bouton_envoyer=="Modifier") // si on a appelé le formulaire par le bouton modifier
{
?>
		Modification d'un rendez-vous
<?php
}
else
{
?>
		Saisie d'un nouveau rendez-vous
<?php
}
?>
		  </legend>
<!--Formulaire d'ajout-modification de RDV -->
			<label for="datepicker" style="width:70px;float:left;">
			  <strong>Jour&nbsp;: </strong>
			</label>

			<input name="RDV_PrisAvec" id="RDV_PrisAvec" type="hidden" value="<?php echo $user ?>"  />
			<input name="intervenants" id="intervenants" type="hidden" value="<?php echo $intervenants ?>" />
			<input name="Date" id="datepicker" type="text" value="<?php echo substr($debut,0,10) ?>" size="10" maxlength="10" style="float:left;display:inline; " title="Permet aussi bien de fixer la date pour créer un rendez-vous que de début pour une période d'affichage. À associer avec la zone Fin."/>
			<br />
			<b>
			  <label for="Heure" style="width:70px;float:left;clear: both;">
			    Heure&nbsp;: 
			  </label>
			</b>
			<input name="Heure" id="Heure" type="text" value="<?php echo $Heure ?>" size="2" maxlength="2"  style="float:left" />
			<input name="Minutes" id="Minutes" type="text" value="<?php echo $Minutes ?>" size="2" maxlength="2" style="float:left" /><br />
			<label for="Type" style="width:70px;float:left;clear: both;">

			 <strong> Type&nbsp;: </strong>
			</label>
			<select name="Type" id="Type" onchange="mettre_valeur(this.value)" title="Valeur par défaut <?php echo $rdv_default[$signuser];  ?> pour l'enregistrement des rendez-vous" style="float:left">
			  <option value='Tous'>
			    Tous
			  </option>
<?php
//recuperation des couleurs pour les differents types de RDV
foreach ($lignecouleur_all AS $lignecouleur)
{
  $couleur[$lignecouleur["Name"]]=$lignecouleur["Color"];
?>
			  <option <?php 
  if (isset($_REQUEST['Type'])) //modifier
  {
    if ($Type==$lignecouleur["Name"]) //si mode modifier
      echo " selected='selected'" ;
  }
?> value="<?php echo $lignecouleur["Name"]; ?>">
		  <?php echo $lignecouleur["Name"]?>
			  </option>
<?php
}
?>
			</select><br />
			<label for="Duree"  style="width:70px;float:left;clear: both;">
			  <strong>Dur&eacute;e : </strong>
			</label>
			<input name="Duree" id="Duree" type="text" value="<?php echo $Duree; ?>" size="5" maxlength="11" style="float:left" /><br />
			<label for="status_saisie" style="width:70px;float:left;clear: both;">
			  <strong>Statut : </strong>
			</label>
			<select name="status" id="status_saisie"  title="Valeur par défaut Statut non défini pour l'enregistrement des rendez-vous" style="float:left" >
			  <option value='Tous' <?php if ($status=='Tous') echo "selected='selected'" ?> >
			    Tous
			  </option>
<?php
foreach ($status_rdv AS $ce_rdv)
{
?>
			  <option value="<?php echo addslashes($ce_rdv) ?>"<?php 
  if (isset($_REQUEST['status'])) // si on a appelé le formulaire par le bouton modifier
  {
    if ($ce_rdv==$status) 
      echo " selected='selected'"; 
  }
?> >
<?php 
  echo $ce_rdv 
?>
			  </option>
<?php
}
?>
			</select><br />
			<b><label for="Note" style="width:70px;float:left;clear: both;">Notes : </label></b>
			<input name="RDV_PrisPar" id="RDV_PrisPar" type="hidden" value="<?php echo $user ?>" size="20" maxlength="20" />
			<input name="Note" id="Note" type="text" value="<?php echo $Note ?>" size="30" maxlength="100"  style="float:left"/><br />
<?php
if ($bouton_envoyer=="Modifier" OR isset($_REQUEST['hidden_envoyer'])) // si on a appelé le formulaire par le bouton modifier
{
  //On verifie si verrou existe
  $sql_chercher_verrou=$pdo->prepare('SELECT `lock` FROM agenda WHERE `PrimKey`= ?');
  $sql_chercher_verrou->bindValue(1, $id_rdv, PDO::PARAM_STR);
  $sql_chercher_verrou->execute();
  $ligne_chercher_verrou=$sql_chercher_verrou->fetch(PDO::FETCH_ASSOC);
  $sql_chercher_verrou->closeCursor();

  if ($ligne_chercher_verrou['lock']==1)
    echo "
		  <div class=\"notice\" style=\"width:300px;clear: both;\">Fiche en utilisation. Ne pas modifier <br />sauf si vous êtes certain de ne pas être en conflit <br />avec un autre utilisateur</div>";
  else
  {
  //on pose un verrou
    $sql_poser_verrou=$pdo->prepare('UPDATE agenda SET `lock`="1" WHERE `PrimKey`= ?');
    $sql_poser_verrou->bindValue(1, $id_rdv, PDO::PARAM_STR);
    $sql_poser_verrou->execute();
    $sql_poser_verrou->closeCursor();
  }
?>
			<input name="bouton_envoyer" type="submit" value="Modifier" onclick="return verif_tous()"  style="float:left"/>
			<input name="hidden_envoyer" type="hidden" value="Modifier" />
			<input name="id_rdv" type="hidden" value="<?php echo $id_rdv ?>" />
			<input name="Reset" type="reset" value="R&eacute;initialiser"  style="float:left"/>
<?php
}
else
{
?>
			<input name="bouton_envoyer" type="submit" value="Ajouter" title="Créer un rendez-vous avec toutes les données ci-dessus" onclick="return verif_tous()" style="float:left"/>
<?php
}
?>
	  <!-- Fin zone saisie du rdv -->
		</fieldset>
	      </div>
	      <div class="login">
		<fieldset class="fieldset_login">
		  <legend>
		    Patient
		  </legend>
		    <label for="Nom" style="width:70px;float:left;clear: both;">
		      <strong>Nom&nbsp;: </strong>
		    </label>
		    <label for="check_exact">
		    Exact
		    </label>
		    <input type="checkbox" name="check_exact" id="check_exact" onclick="request(readData)"/> 
		    <input name="GUID" id="GUID" type="hidden" value="<?php echo str_replace('%','',$GUID) ?>"/> <!--readonly="readonly" -->
		    <input name="Nom" id="Nom" type="text" value="<?php echo str_replace('%','',$Nom) ?>" size="20" maxlength="40" onchange="change_status(this.value)" style="float:left" onkeyup="request(readData);" /><br />
		    <label for="Prenom" style="width:70px;float:left;clear: both;">
		      <strong>Pr&eacute;nom&nbsp;: </strong>
		    </label>
		    <input name="Prenom" id="Prenom" type="text" value="<?php echo str_replace('%','',$Prenom) ?>" size="20" maxlength="40" style="float:left" onkeyup="request(readData);"/><br />
		    <b><label for="Tel" style="width:70px;float:left;clear: both;">T&eacute;l&eacute;phone&nbsp;: </label>
		    </b>
		    <input name="Tel" id="Tel" type="text" value="<?php echo $Tel ?>" size="10" maxlength="10" style="float:left"/><br />
		    <b><label for="Adresse" style="width:70px;float:left;clear: both;">Adresse&nbsp;: </label>
		    </b>
		    <input name="Adresse" id="Adresse" type="text" value="<?php echo $adresse ?>" size="30" maxlength="10" style="float:left"/><br />
                    <select name="select_critere" id="select_critere" style="float:left" onchange="request(readData)">
                      <option value="0">
                        critère de recherche
                      </option>
                      <option value="FchPat_NomFille">
                        Nom de jeune fille
                      </option>
                      <option value="FchPat_Adresse">
                        Adress
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
                    <input type="text" name="text_select_critere" id="text_select_critere" style="float:left" onkeyup="request(readData)"/><br />
		    <label for="check_name" style="float:left">
		      Utiliser l'identité pour la recherche de rendez-vous
		    </label><br />
		    <input type="checkbox" name="check_name" id="check_name" value="check" <?php if (isset($_REQUEST['check_name'])) echo 'checked="checked"' ?> style="float:left;clear: both;" onchange="request(readData);" />
		    <select name="select_patient" id="select_patient" style="float:left;clear: both;" onchange="fillFields()">
                      <option></option>
		    </select><br />
		    <input type="button" value="Remplir les champs" onclick="fillFields()" style="float:left;clear: both;"/>
<!-- 		    <input type="button" value="Choisir un patient" onclick="chercher_patient()" style="float:left;"/> -->
		    <input type="button" value="Vider" onclick="vide_form()"  style="float:left"/>
		</fieldset>
	      </div>
	      <div class="login">
		<fieldset class="fieldset_login">
		  <legend>
		    Rechercher
		  </legend>
	  <!-- table des recherches -->
			<label for="datepickeur">
			  <strong>Fin&nbsp;: </strong>
			</label>
			<input name="fin" id="datepickeur" type="text" value="<?php echo $fin; ?>" size="10" maxlength="10" title="Sert pour le mode recherche : permet de déterminer le dernier jour d'affichage de l'agenda"/>
			<br />
			<label for="praticien">
			  Praticien 
			</label>
			<input type="radio" name="intervenants" id="praticien" value="seul" <?php if ($intervenants=='seul') echo 'checked="checked"' ?>  onchange="select_user=form.submit()"/>
			<label for="specialite">
			  Spécialité 
			</label>
			<input type="radio" name="intervenants"  id="specialite" value="specialite" <?php if ($intervenants=='specialite') echo 'checked="checked"' ?>  onchange="select_user=form.submit()"/>
			<label for="tous">Tous </label>
			<input type="radio" name="intervenants" id="tous"  value="tous" <?php if ($intervenants=='tous') echo 'checked="checked"' ?> onchange="select_user=form.submit()" /><br />
			<input name="envoyer" id="button_chercher" type="submit" value="Chercher" />
			<input name="envoyer" type="submit" value="Cocher" title="Permet de sélectionner des fiches qui seront définitivement effacées"/>
<?php
//On passe la date en composantes pour le calcul

$debut_iso=local_to_iso($debut,$date_format);

$list_date=explode ("-",$debut_iso);
?>
			<div class="precsuiv">
			  <input name="precedent" id="precedent" type="hidden" value="<?php echo date("Y-m-d", mktime(0, 0, 0,$list_date[1],$list_date[2]-1,$list_date[0])); ?>" />
			  <input name="envoyer" type="submit" value="Précédent" title="Jour pr&eacute;c&eacute;dent le premier jour de la période"  style="float:left"/>
			  <input name="today_debut" id="today_debut" type="hidden" value="<?php echo date('Y-m-d', date('U')) ?>" />
			  <input name="envoyer" type="submit" value="Aujourd'hui"  style="float:left"/>
			  <input name="suivant" id="suivant" type="hidden" value="<?php echo date("Y-m-d", mktime(0, 0, 0,$list_date[1],$list_date[2]+1,$list_date[0])); ?>" />
			  <input name="envoyer" type="submit" value="Suivant" title="Jour suivant le dernier jour de la période"  style="float:left"/>
			</div>
<!-- 		  zone des recherches -->
		</fieldset>
	</div><!-- Fin class=tableau -->
      </form>
    </div><!-- Fin class=groupe noprint titre et tableau principal -->

    <div class="groupe">
      <h1>

<?php

//Si l'on est en mode de recherche par date
if (isset($_GET['envoyer']))
{
  $envoyer=$_GET['envoyer'];
//les fleches
  if ($envoyer=='&raquo;') //»
  {
    $debut=$_GET['raquo_debut'];
    $fin=$_GET['raquo_fin'];
  }
  elseif ($envoyer=='&laquo;') //«
  {
    $debut=$_GET['laquo_debut'];
    $fin=$_GET['laquo_fin'];
  }
  elseif ($envoyer=='Aujourd\'hui')
  {
    $debut=$fin=iso_to_local($_GET['today_debut'],$date_format);
  }
  //Affichage du resume si les dates de debut et de fin sont les memes
  $status_propre=stripslashes($status);

  $string_request_key='';
  if (isset($_REQUEST['check_name']))
    $string_request_key= "pour $critere_recherche = $Nom ,";

  if (substr($debut_sql,0,10)==substr($fin_sql,0,10))
  {
    if ($count_liste_rdv==0)
    {
      echo "
	  Aucun rendez-vous trouv&eacute; le $debut $string_request_key de type $Type, de statut $status_propre.";
    }
    elseif ($count_liste_rdv==1)
      echo "
	  1 rendez-vous trouv&eacute; le $debut $string_request_key de type $Type, de statut $status_propre.";
    else
      echo "
	  ".$count_liste_rdv." rendez-vous trouv&eacute;s le $debut $string_request_key de type $Type, de statut $status_propre, tri&eacute;s par $critere_tri[$tri].";
  }
//Si deux dates differentes
  else
  {
    if ($count_liste_rdv==0)
      echo "
	  Aucun rendez-vous trouv&eacute; entre le $debut et le $fin $string_request_key de type $Type, de statut $status_propre.";
    elseif ($count_liste_rdv==1)
      echo "
	  1 rendez-vous trouv&eacute; entre le $debut et le $fin $string_request_key de type $Type, de statut $status_propre.";
    else
      echo "
	  $count_liste_rdv rendez-vous trouv&eacute;s entre le $debut et le $fin $string_request_key de type $Type, de statut $status_propre, tri&eacute;s par $critere_tri[$tri]" ;
  }
}
//si rien n'est renseigne
else
{
  if ($count_liste_rdv==0)
    echo "
	  Aucun rendez-vous trouv&eacute; le $debut.";
  elseif ($count_liste_rdv==1)
    echo "
	  1 rendez-vous trouv&eacute; le $debut.";
  else
    echo "
	  $count_liste_rdv rendez-vous trouv&eacute;s le $debut, tri&eacute;s par heure";
}

echo "
	</h1>"; //fin  nombre resultats

if ($count_liste_rdv) //afficher les en-tetes seulement si resultat
{
  if ($Nom=="")
  {
    $Nom="%";
  }
  if ($envoyer=="Cocher") //initialisation du formulaire des cases a cocher
{
	echo "
	<form id=\"form_coches\" action=\"suppr_rdv.php\" method=\"get\">
	  <div>
	    <input name=\"debut\" type=\"hidden\" value=\"". $debut ."\" />
	    <input name=\"fin\" type=\"hidden\" value=\"". $fin ."\" />
	  </div>";
}

?>
<br />
	  <div class="tableau">
	    <table title="Vous pouvez ordonner par nom ou date/heure en cliquant sur la t&ecirc;te de colonne">
	      <col /><col /><col /><col /><col /><col /><col /><col /><col /><col /><col /><col />
		<tr><!--Les en-tetes du tableau d'affichage des RDV -->
<?php if ($envoyer=="Cocher")
{
?>
		  <th class="fond_th">
		    <input name="button_invert_selection" onclick="Invers()" type="button" value="Inverser la sélection" />
		  </th>
<?php
}
?>
		  <th class="fond_th">
		    <a href="agenda.php?debut=<?php echo $debut ?>&amp;fin=<?php echo $fin ?>&amp;critere_recherche=<?php echo $critere_recherche?>&amp;nom=<?php echo $Nom ?>&amp;Type=<?php echo $Type ?>&amp;envoyer=Chercher&amp;tri=Date_Time">
		      Date
		    </a>
		  </th>
		  <th class="fond_th">
		    Heure
		  </th>
		  <th class="fond_th">
		    Dur&eacute;e
		  </th>
		  <th class="fond_th">
		    <a href="agenda.php?debut=<?php echo $debut ?>&amp;fin=<?php echo $fin ?>&amp;critere_recherche=<?php echo $critere_recherche?>&amp;nom=<?php echo $nom ?>&amp;Type=<?php echo $Type ?>&amp;envoyer=Chercher&amp;tri=Nom">
		      Nom
		    </a>
		  </th>
		  <th class="fond_th">
		    Pr&eacute;nom
		  </th>
		  <th class="fond_th">
		    T&eacute;l
		  </th>
		  <th class="fond_th">
		    Adresse
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
		    Pris avec
		  </th>
<?php if ($envoyer!="Cocher") echo "
		  <th colspan=\"2\" class=\"fond_th\">
		    Rendez-vous
		  </th>";
?>
		</tr>
<?php
}
foreach ($ligne_liste_rdv_all AS $ligne_liste_rdv) 
{
  $tableau_date_time=explode(" ",$ligne_liste_rdv["Date_Time"]); //on separe la date de l'heure - format iso
  $date=iso_to_local($tableau_date_time[0],$date_format);
  $time=substr($ligne_liste_rdv["Date_Time"],11,5);
  $tableau_components=explode("-",$tableau_date_time[0]);

//Recuperation du type de rdv pour y affecter une couleur
  $type_RV=$ligne_liste_rdv["Type"];
  $couleur_ligne=$couleur["$type_RV"];
  $jd = date("w",mktime(0,0,0,$tableau_components[1],$tableau_components[2],$tableau_components[0]));//numero du jour de la semaine

//Affichage si le rendez-vous n'est pas dans le fichier
  if (!$ligne_liste_rdv["GUID"])
  {
    if ($ligne_liste_rdv["status"])
      $color=stripslashes($ligne_liste_rdv["status"]);
    else
      $color="Statut non défini";
    echo "
		<tr>";
    if ($envoyer=="Cocher") 
    {
      echo "
		  <td class=\"fond_td\">
		    <input type=\"checkbox\" name=\"coche[]\" value=\"".$ligne_liste_rdv["PrimKey"]."\" />
		  </td>";
    }
    echo "
		  <td class=\"fond_td\">
		    ".$text_jsemaine[$jd]." ".$date." 	  
		  </td>	  
		  <td class=\"fond_td\">
		    ".$time."
		  </td>
		  <td class=\"fond_td\">
		    ".$ligne_liste_rdv["Duree"].
		  "'</td>
		  <td class=\"fond_td\">
		    <a href=\"liste.php?critere_recherche=FchGnrl_NomDos&amp;envoyer=Chercher&amp;cle=".$ligne_liste_rdv["Nom"]."%&amp;Prenom=".$ligne_liste_rdv["Prenom"]."&amp;Tel=".$ligne_liste_rdv["Tel"]."&amp;Nom=".$ligne_liste_rdv["Nom"]."\">
		      ".$ligne_liste_rdv["Nom"]."
		    </a>
		  </td>
		  <td class=\"fond_td\">
		    ".$ligne_liste_rdv["Prenom"]."
		  </td>
		  <td class=\"fond_td\">
		    ".$ligne_liste_rdv["Tel"]."
		  </td>
		  <td class=\"fond_td\">
		    ".$ligne_liste_rdv["Adresse"]."
		  </td>
		  <td style=\"background:$couleur_ligne;\" class=\"fond_td\">
		    ".$ligne_liste_rdv["Type"]."
		  </td>
		  <td style=\"background:$color_status[$color];\" class=\"fond_td\">
		    ";
?>
		    <select name="status" id="status_saisie<?php echo $ligne_liste_rdv["PrimKey"] ?>" onchange="change_status_this_appointment(this.value,<?php echo $ligne_liste_rdv["PrimKey"] ?>)" >
<?php
reset ($status_rdv);
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
echo "
		  </td>
		  <td class=\"fond_td\">
		    ".$ligne_liste_rdv["Note"]."
		  </td>
		  <td class=\"fond_td\" title=\"Pris par ".$ligne_liste_rdv["RDV_PrisPar"]." \">
		    ".$ligne_liste_rdv["RDV_PrisAvec"]."
		  </td>";
    if ($envoyer!="Cocher") 
    {
?>
		  <td class="fond_td">
		    <form action="agenda.php"  method="get"> <!--Bouton de modification de rendez-vous--> 
		      <div>
			<input name="nom" type="hidden" value="%" />
			<input name="debut" type="hidden" value="<?php echo $date ?>" />
			<input name="fin" type="hidden" value="<?php echo $date ?>" />
			<input name="id_rdv" type="hidden" value="<?php echo $ligne_liste_rdv['PrimKey']?>" />
			<input name="GUID" type="hidden" value="" />
			<input name="RDV_PrisPar" type="hidden" value="<?php echo $ligne_liste_rdv['RDV_PrisPar']?>" />
			<input name="Date" type="hidden" value="<?php echo $date ?>" />
			<input name="Time" type="hidden" value="<?php echo $time?>" />
			<input name="RDV_PrisAvec" type="hidden" value="<?php echo $ligne_liste_rdv['RDV_PrisAvec']?>" />
			<input name="Duree" type="hidden" value="<?php echo $ligne_liste_rdv['Duree']?>" />
			<input name="Nom" type="hidden" value="<?php echo $ligne_liste_rdv['Nom']?>" />
			<input name="Prenom" type="hidden" value="<?php echo $ligne_liste_rdv['Prenom']?>" />
			<input name="Tel" type="hidden" value="<?php echo $ligne_liste_rdv['Tel']?>" />
			<input name="Note" type="hidden" value="<?php echo $ligne_liste_rdv['Note']?>" />
			<input name="Type" type="hidden" value="<?php echo $ligne_liste_rdv['Type']?>" />
			<input name="status" type="hidden" value="<?php echo $ligne_liste_rdv['status']?>" />
			<input name="intervenants" type="hidden" value="<?php echo $intervenants ?>" />
			<input name="bouton_envoyer" type="submit" value="Modifier" />
<?php
      if ($ligne_liste_rdv["status"]!='Annulé') //pas de bouton Annuler si déjà annulé
      {
?>
			<input name="bouton_envoyer" type="submit" value="Annuler" />
<?php
      }
?>
		      </div>
		    </form>
		  </td>
<?php
}
?>
		</tr>
<?php
	}
//Affichage si le rendez-vous est dans le fichier
	else
	{
	  $color="Statut non défini";
	  if ($ligne_liste_rdv["status"])
	    $color=stripslashes($ligne_liste_rdv["status"]);
	  echo "
		<tr>";
	  if ($envoyer=="Cocher") 
	  {
	    echo "
		  <td class=\"fond_td\">
		    <input type=\"checkbox\" name=\"coche[]\" value=\"".$ligne_liste_rdv["PrimKey"]."\" />
		  </td>";
	  }

	  echo "
		  <td class=\"fond_td\">
		    ".$text_jsemaine[$jd]." ".$date."
		  </td>
		  <td class=\"fond_td\">
		    ".$time."
		  </td>
		  <td class=\"fond_td\">
		    ".$ligne_liste_rdv["Duree"]."'
		  </td>
		  <td class=\"fond_td\">
		    <a href=\"frame_patient.php?GUID=".$ligne_liste_rdv["GUID"]."\">
		      ".$ligne_liste_rdv["Nom"]."
		    </a> 
		  </td>
		  <td class=\"fond_td\">
		    ".$ligne_liste_rdv["Prenom"]."
		  </td>
		  <td class=\"fond_td\">
		    ".$ligne_liste_rdv["Tel"]."
		  </td>
		  <td class=\"fond_td\">
		    ".$ligne_liste_rdv["Adresse"]."
		  </td>
		  <td style=\"background:".$couleur_ligne.";\" class=\"fond_td\">
		    ".$ligne_liste_rdv["Type"]."
		  </td>
		  <td style=\"background:$color_status[$color];\" class=\"fond_td\">
		    ";
?>
		    <select name="status" id="status_saisie<?php echo $ligne_liste_rdv['PrimKey'] ?>" onchange="change_status_this_appointment(this.value,<?php echo $ligne_liste_rdv["PrimKey"] ?>)">
<?php
	  reset ($status_rdv);
	  foreach ($status_rdv AS $ce_rdv)
	  {
?>
			<option value="<?php echo addslashes($ce_rdv) ?>"
<?php 
	    if ($ce_rdv==stripslashes($ligne_liste_rdv["status"])) 
	      echo " selected='selected'"; 
?>
  >
<?php 
	    echo $ce_rdv.'
			</option>
';
	  }
?>
			</select>
		  </td>
		  <td class="fond_td">
<?php
	  echo $ligne_liste_rdv["Note"]."
		  </td>
		  <td class=\"fond_td\" title=\"Pris par ".$ligne_liste_rdv["RDV_PrisPar"]."\">
		    ".$ligne_liste_rdv["RDV_PrisAvec"]."
		  </td>";
	  if ($envoyer!="Cocher") 
	  {
?>
		  <td class="fond_td">
		    <form action="agenda.php"  method="get"> <!--Bouton de modification de rendez-vous--> 
		      <div>
			<input name="nom" type="hidden" value="%" />
			<input name="debut" type="hidden" value="<?php echo $date ?>" />
			<input name="fin" type="hidden" value="<?php echo $date ?>" />
			<input name="id_rdv" type="hidden" value="<?php echo $ligne_liste_rdv["PrimKey"]?>" />
			<input name="GUID" type="hidden" value="<?php echo $ligne_liste_rdv["GUID"]?>" />
			<input name="RDV_PrisPar" type="hidden" value="<?php echo $ligne_liste_rdv["RDV_PrisPar"]?>" />
			<input name="Date" type="hidden" value="<?php echo $date ?>" />
			<input name="Time" type="hidden" value="<?php echo $time?>" />
			<input name="RDV_PrisAvec" type="hidden" value="<?php echo $ligne_liste_rdv['RDV_PrisAvec']?>" />
			<input name="Duree" type="hidden" value="<?php echo $ligne_liste_rdv['Duree']?>" />
			<input name="Nom" type="hidden" value="<?php echo $ligne_liste_rdv['Nom']?>" />
			<input name="Prenom" type="hidden" value="<?php echo $ligne_liste_rdv['Prenom']?>" />
			<input name="Tel" type="hidden" value="<?php echo $ligne_liste_rdv['Tel']?>" />
			<input name="Note" type="hidden" value="<?php echo $ligne_liste_rdv['Note']?>" />
			<input name="Type" type="hidden" value="<?php echo $ligne_liste_rdv['Type']?>" />
			<input name="status" type="hidden" value="<?php echo $ligne_liste_rdv['status']?>" />
			<input name="intervenants" type="hidden" value="<?php echo $intervenants ?>" />
			<input name="bouton_envoyer" type="submit" value="Modifier" />
<?php
	    if ($ligne_liste_rdv["status"]!='Annulé') //pas de bouton Annuler si déjà annulé
	    {
?>
			<input name="bouton_envoyer" type="submit" value="Annuler" />
<?php
	    }
?>
		      </div>
		    </form>
		  </td>
<?php
	  }
?>
		</tr>
<?php
	}
      }
      if ($envoyer=="Cocher")
      {
?>
<!-- Derniere ligne coches -->
		<tr>
		  <td title="Ce bouton permet de supprimer définitivement tous les rendez-vous cochés ci-dessus en combinaison avec la coche.">
		    <input name="confirmer" type="checkbox" value="Supprimer" />
		    <input name="button_supprimer" type="submit" value="Supprimer" />
		  </td>
		</tr>
<?php
      }

      if ($count_liste_rdv)
      {
?>
	      </table>
	    </div>
<?php
	if ($envoyer=="Cocher")
	{
?>
<!-- fin formulaire form_coches -->
	  </form>
<?php
	}
      }
?>
	</div>
<?php
include("inc/footer.php");
?>