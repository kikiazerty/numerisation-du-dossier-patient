<?php
session_start() ;
if ( !isset( $_SESSION['login'] ) ) 
{
  header('location: index.php?page=correspondant' );
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
if (isset($_GET['ID_corresp']))
{	
  $sql=$pdo->prepare('SELECT * FROM Personnes where ID_PrimKey = ?');
  $sql->bindValue(1, $_GET['ID_corresp'], PDO::PARAM_STR);
  $sql->execute();
  $ligne=$sql->fetch(PDO::FETCH_ASSOC);
  $sql->closeCursor();
}
if (isset($_GET['envoyer']))
  $Envoyer=$_GET['envoyer'];
if (isset($_GET['modif']))
  $modif=$_GET['modif'];
  
//pour remplir les libelles des champs dans les champs
$Titre="Titre";
$nom_label=$nom="Nom";
$prenom="Prénom";
$specialite="Spécialité";
$Adresse="Adresse";
$Tel_1="Tél 1";
$Tel_2="Tél 2";
$Tel_3="Tél 3";
$Tel_Type1="";
$Tel_Type2="";
$Tel_Type3="";
$Tel_Abr_1="Abrégé";
$Tel_Abr_2="Abrégé";
$Tel_Abr_3="Abrégé";

$CP=$cp_default;
$Ville=$ville_default;
$EMail="eMail";
$Note="Notes";
$Qualite="";
$ID_PrimKey="";
$OrdreCorresp="Numéro ADELI";
$RPPSCorresp="Numéro RPPS";
$sexe_intervenant="";
$politesse="Politesse";

if ($Envoyer=="Modifier" or $Envoyer=="Supprimer")
{
  if ($ligne['Nom']) //On ne remplit ce champ qu'avec une valeur existante, sinon on laisse la valeur par defaut qui sert de libelle
    $nom=$ligne['Nom'];
  if ($ligne['Prenom'])
    $prenom=stripslashes($ligne['Prenom']);
  if ($ligne['Adresse'])
    $Adresse=$ligne['Adresse'];
  if ($ligne['Tel_1'])
    $Tel_1=$ligne['Tel_1'];
  if ($ligne['Tel_2'])
    $Tel_2=$ligne['Tel_2'];
  if ($ligne['Tel_3'])
    $Tel_3=$ligne['Tel_3'];
  $Tel_Type1=$ligne['Tel_Type1'];
  $Tel_Type2=$ligne['Tel_Type2'];
  $Tel_Type3=$ligne['Tel_Type3'];
  $Tel_Abr_1=$ligne['Tel_Abr_1'];
  $Tel_Abr_2=$ligne['Tel_Abr_2'];
  $Tel_Abr_3=$ligne['Tel_Abr_3'];
  if ($ligne['CodePostal'])
    $CP=$ligne['CodePostal'];
  if ($ligne['Ville'])
    $Ville=$ligne['Ville'];
  if ($ligne['EMail'])
    $EMail=$ligne['EMail'];
  if ($ligne['Note'])
    $Note=$ligne['Note'];
  $Qualite=$ligne['Qualite'];
  if ($ligne['Titre'])
    $Titre=$ligne['Titre'];
  $ID_PrimKey=$ligne['ID_PrimKey'];
  if ($ligne['NumOrdre'])
    $OrdreCorresp=$ligne['NumOrdre'];
  if ($ligne['NumRPPS'])
    $RPPSCorresp=$ligne['NumRPPS'];
  $sexe_intervenant=$ligne['Sexe'];
  if ($ligne['Cher'])
    $politesse=$ligne['Cher'];
}
include("inc/header.php");
$sql_specialite=$pdo->prepare('SELECT Qualite FROM Personnes GROUP BY Qualite ORDER BY Qualite'); 
$sql_specialite->execute();
$ligne_chercher_all_specialite=$sql_specialite->fetchAll(PDO::FETCH_ASSOC);
$sql_specialite->closeCursor();
$count_specialite=count($ligne_chercher_all_specialite);
	
?>
    <title>
      MedWebTux - Intervenants - Utilisateur <?php echo $_SESSION['login'] ?>
    </title>
    
<script type="text/javascript">
//<![CDATA[
function inArray(needle, haystack) //chercher correspondance de la chaine saisie avec le deroulant et positionner le deroulant sur la valeur correspondante 
{
  var length = <?php echo $count_specialite ?>; //le nombre de specialites trouvees
  var liste_spe=document.getElementById('liste_spe');  //on recupere l'id de la liste deroulante
  for(var i = 0; i < length; i++) //On balaye tous les items de la liste et on cherche une correspondance avec la saisie
  {
    if(haystack[i].toUpperCase().includes (needle.toUpperCase())) //on met tout en majuscules pour comparer
    {
      var j=i+2; //on decale le compteur
      liste_spe[j].selected = true; //on selectionne le premier item qui matche qu'on trouve
      break;
    }
    else
    {
      liste_spe[0].selected = true; //on selectionne Specialite
    }
  }
}

function chooseOption() //pour choisir automatiquement une specialite dans le deroulant quand on saisit dans la zone a cote
{
//creation de la variable tableau javascript qui contient toutes les specialites
  var specialites=new Array (<?php 
  $i=1;
  foreach ($ligne_chercher_all_specialite AS $this_specialite)
  {
    if ($this_specialite['Qualite']!='')
    {
      echo '"'.$this_specialite['Qualite'].'"';
      if ($i<$count_specialite) echo ',';
    }
    $i++;
  }
  ?>);
  var specialite =document.getElementById('specialite').value; //on recupere la saisie (noter le singulier)
  inArray(specialite,specialites); //on regarde si la saisie est incluse dans la liste
}
//]]>
</script> 

<script type="text/javascript">
//<![CDATA[
function check_data()
// verifier les donnees avant validation fiche
{
<?php
 if ($Envoyer!="Modifier" AND $Envoyer!="Supprimer") // le nom est celui qui vient de la base, donc c'est normal qu'on le conserve
 {
?>
  if(document.getElementById('nom_corresp').value =='<?php echo $nom_label ?>') //vider le champ si valeur par defaut (label)
  {
    document.getElementById('nom_corresp').value ='';
    alert ('Il faut au moins donner un nom');
    return false;
  }
<?php
  }
?>
  if(document.getElementById('titre_corresp').value =='<?php echo $Titre ?>') //vider le champ si valeur par defaut (label)
  {
    document.getElementById('titre_corresp').value ='';
  }

  if(document.getElementById('prenom_corresp').value =='<?php echo $prenom ?>') //vider le champ si valeur par defaut (label)
  {
    document.getElementById('prenom_corresp').value ='';
  }
  if(document.getElementById('ordre_corresp').value =='<?php echo $OrdreCorresp ?>') //vider le champ si valeur par defaut (label)
  {
    document.getElementById('ordre_corresp').value ='';
  }
  if(document.getElementById('rpps_corresp').value =='<?php echo $RPPSCorresp ?>') //vider le champ si valeur par defaut (label)
  {
    document.getElementById('rpps_corresp').value ='';
  }
  if(document.getElementById('politesse').value =='<?php echo $politesse ?>') //vider le champ si valeur par defaut (label)
  {
    document.getElementById('politesse').value ='';
  }
  if(document.getElementById('Adresse_corresp').value =='<?php echo $Adresse ?>') //vider le champ si valeur par defaut (label)
  {
    document.getElementById('Adresse_corresp').value ='';
  }
  if(document.getElementById('mail_corresp').value =='<?php echo $EMail ?>') //vider le champ si valeur par defaut (label)
  {
    document.getElementById('mail_corresp').value ='';
  }
  if(document.getElementById('tel1_corresp').value =='<?php echo $Tel_1 ?>') //vider le champ si valeur par defaut (label)
  {
    document.getElementById('tel1_corresp').value ='';
  }
  if(document.getElementById('tel2_corresp').value =='<?php echo $Tel_2 ?>') //vider le champ si valeur par defaut (label)
  {
    document.getElementById('tel2_corresp').value ='';
  }
  if(document.getElementById('tel3_corresp').value =='<?php echo $Tel_3 ?>') //vider le champ si valeur par defaut (label)
  {
    document.getElementById('tel3_corresp').value ='';
  }
  if(document.getElementById('Notes_corresp').value =='<?php echo $Note ?>') //vider le champ si valeur par defaut (label)
  {
    document.getElementById('Notes_corresp').value ='';
  }
   if(document.getElementById('liste_spe').value =='<?php echo $specialite ?>') //vider le champ si valeur par defaut (label)
  {
    document.getElementById('liste_spe').value ='Autre';//on met sur "Autre, qui sera gere par la validation
  }
}
//]]>
</script>    
    
<script type="text/javascript">
//<![CDATA[
function zip_code(value)
// on ouvre dans une fenêtre le fichier passé en paramètre.
{
  window.open('zipcode.php?code='+value,'Codepostal','width=450,height=250,top=50,left=50,toolbar=yes, scrollbars=yes, location=no');
}
//]]>
</script>
<script type="text/javascript">
//<![CDATA[
function city(value)
// on ouvre dans une fenêtre le fichier passé en paramètre.
{
  window.open('city.php?city='+value,'Ville','width=450,height=250,top=50,left=50,toolbar=yes, scrollbars=yes, location=no');
}
//]]>
</script>
    <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen" />
  </head>
  <body style="font-size:<?php echo $fontsize; ?>pt" >
    <div class="conteneur">
<?php	
// insertion du menu d'en-tete	
$anchor='modification_de_fiche_d_intervenant';
include("inc/menu-horiz.php");
?>
    <div class="groupe">
      <h1>CHNP | Nouvel utilisateur </h1>
<?php
if (isset($_GET['modif']))
{
  if ($modif=="oui")
  {
?>
      <h2 style="text-align:center;">
        Modification effectu&eacute;e
      </h2>
<?php
  }
}
if ($Envoyer=="Modifier")
{
?>
      <p  style="text-align:center;">
        <a href="fiche_intervenant.php?intervenant=<?php echo $ID_PrimKey ?>">Acc&eacute;der &agrave; la fiche de <?php echo $nom." ".$prenom ?></a>
      </p>
<?php
}
if ($Envoyer=="Nouveau" or $Envoyer=="Modifier")
{
?>
      <fieldset>
        <legend>
          Nouvel Utilisateur 
        </legend>
        <form action="validation_correspondant.php" method="get" id="form_general">
          <input name="titre_corresp" id="titre_corresp" type="text" value="<?php echo $Titre ?>" size="4" maxlength="50" onfocus="if (this.value== this.defaultValue ) this.value=''" onblur="if (this.value== '' ) this.value=this.defaultValue" title="Titre" />
          <input name="nom_corresp" id="nom_corresp" type="text" value="<?php echo $nom?>" size="20" maxlength="60" <?php if ($Envoyer=='Nouveau') echo 'onfocus="if (this.value== this.defaultValue) this.value=\'\' "' ?> title="Nom"  onblur="if (this.value== '' ) this.value=this.defaultValue" />
          <input name="prenom_corresp" id="prenom_corresp" type="text" value="<?php echo $prenom?>" size="20" maxlength="60"  <?php if ($Envoyer=='Nouveau') echo 'onfocus="if (this.value== this.defaultValue) this.value=\'\' "' ?> title="Prénom"  onblur="if (this.value== '' ) this.value=this.defaultValue"/>
          <br />
          <select name="liste_spe" id="liste_spe" >
            <option value="<?php echo $specialite ?>">
              <?php echo $specialite ?>
            </option>
            <option value="Autre">
              Autre
            </option>
<?php
  foreach ($ligne_chercher_all_specialite AS $ligne_specialite)
  { 
    if ($ligne_specialite['Qualite'])
    {
?>
            <option value="<?php echo $ligne_specialite['Qualite'];?>" <?php if ($ligne_specialite['Qualite']==$Qualite) echo "selected='selected'" ?> >
              <?php echo $ligne_specialite['Qualite'] ?>
            </option>
<?php
    }
  }
?>
          </select>
          <input name="specialite" id="specialite" type="text" value="" title="Ne sert que si le déroulant est sur Autre - 40 caractères maxi" size="20" maxlength="40" onkeyup="chooseOption()" />
<br />
          <input name="ordre_corresp" id="ordre_corresp" type="text" value="<?php echo $OrdreCorresp ?>" size="10" maxlength="15"  <?php if ($Envoyer=='Nouveau') echo 'onfocus="if (this.value== this.defaultValue) this.value=\'\' "' ?> title="ADELI"  onblur="if (this.value== '' ) this.value=this.defaultValue" />
<br />
          <input name="rpps_corresp" id="rpps_corresp" type="text" value="<?php echo $RPPSCorresp ?>" size="10" maxlength="15"  <?php if ($Envoyer=='Nouveau') echo 'onfocus="if (this.value== this.defaultValue) this.value=\'\' "' ?> title="RPPS" onblur="if (this.value== '' ) this.value=this.defaultValue" />
<br />
            Sexe : 
          <input type="radio" <?php if ($sexe_intervenant=="M") echo "checked='checked'" ?> name="sexe_intervenant" id="sexe_intervenantM" value="M" /><label for="sexe_intervenantM">Masculin</label>
          <input type="radio" <?php if ($sexe_intervenant=="F") echo "checked'checked'" ?> name="sexe_intervenant" id="sexe_intervenantF" value="F" /><label for="sexe_intervenantF">F&eacute;minin</label>
<br />
          <input name="politesse" id="politesse" type="text" value="<?php echo $politesse ?>" title="Exemple : Cher confrère" <?php if ($Envoyer=='Nouveau') echo 'onfocus="if (this.value== this.defaultValue) this.value=\'\' "' ?> title="Politesse" onblur="if (this.value== '' ) this.value=this.defaultValue" />
<br />
          <textarea name="Adresse_corresp" id="Adresse_corresp" rows="3" cols="60" <?php if ($Envoyer=='Nouveau') echo 'onfocus="if (this.value== this.defaultValue) this.value=\'\' "' ?> onblur="if (this.value== '' ) this.value=this.defaultValue" title="Adresse" ><?php echo $Adresse ?></textarea>
<br />
          <input name="CP" id="CP" type="text" value="<?php echo $CP ?>" size="5" maxlength="5" onchange="zip_code(this.value)"/>
<br />
          <input name="Ville" id="Ville" type="text" value="<?php echo $Ville ?>" size="40"  maxlength="128" onchange="city(this.value)" />
<br />
          <input name="mail_corresp" id="mail_corresp" type="text" value="<?php echo $EMail ?>" size="40" maxlength="128"  <?php if ($Envoyer=='Nouveau') echo 'onfocus="if (this.value== this.defaultValue) this.value=\'\' "' ?> title="eMail" onblur="if (this.value== '' ) this.value=this.defaultValue" />
<?php
    $sql_tel_type1=$pdo->prepare('SELECT Tel_Type1 FROM Personnes GROUP BY Tel_Type1 ORDER BY Tel_Type1'); //le signataire
    $sql_tel_type1->execute();
    $ligne_all_tel_type1=$sql_tel_type1->fetchAll(PDO::FETCH_ASSOC);
    $sql_tel_type1->closeCursor();

    $sql_tel_type2=$pdo->prepare('SELECT Tel_Type2 FROM Personnes GROUP BY Tel_Type2 ORDER BY Tel_Type2'); //le signataire
    $sql_tel_type2->execute();
    $ligne_all_tel_type2=$sql_tel_type2->fetchAll(PDO::FETCH_ASSOC);
    $sql_tel_type2->closeCursor();

    $sql_tel_type3=$pdo->prepare('SELECT Tel_Type3 FROM Personnes GROUP BY Tel_Type3 ORDER BY Tel_Type3'); //le signataire
    $sql_tel_type3->execute();
    $ligne_all_tel_type3=$sql_tel_type3->fetchAll(PDO::FETCH_ASSOC);
    $sql_tel_type3->closeCursor();
?>
		    <br />
          <input name="tel1_corresp" id="tel1_corresp" type="text" value="<?php echo $Tel_1 ?>" size="10" maxlength="20"  <?php if ($Envoyer=='Nouveau') echo 'onfocus="if (this.value== this.defaultValue) this.value=\'\' "' ?> onblur="if (this.value== '' ) this.value=this.defaultValue" />
          <input name="tel1_abr_corresp" id="tel1_abr_corresp" type="text" value="<?php echo $Tel_Abr_1 ?>" title="Num&eacute;ro abr&eacute;g&eacute;" size="5" maxlength="20"  <?php if ($Envoyer=='Nouveau') echo 'onfocus="if (this.value== this.defaultValue) this.value=\'\' "' ?> onblur="if (this.value== '' ) this.value=this.defaultValue" title="Tél 1" />
          <label for="liste_type_tel1">
            Type :
          </label>
          <select name="liste_type_tel1" id="liste_type_tel1" >
            <option  value="Autre">
              Autre
            </option>
<?php
  foreach ($ligne_all_tel_type1 AS $ligne_tel_type1)
  {
    if ($ligne_tel_type1['Tel_Type1'])//ON ELIMINE LES LIGNES VIDES
    {

?>
            <option <?php if ($Tel_Type1==$ligne_tel_type1['Tel_Type1']) echo "selected='selected'" ?> value="<?php echo $ligne_tel_type1['Tel_Type1']?>">
              <?php echo $ligne_tel_type1['Tel_Type1']?>
            </option>
<?php
    }
  }
?>				
          </select>
          <label for="tel1_type_corresp">
            Si Autre, pr&eacute;ciser :
          </label>
          <input name="tel1_type_corresp" id="tel1_type_corresp" type="text" value="" size="5" maxlength="20" />
          <br />
          <input name="tel2_corresp" id="tel2_corresp"  type="text" value="<?php echo $Tel_2 ?>" size="10" maxlength="20"  <?php if ($Envoyer=='Nouveau') echo 'onfocus="if (this.value== this.defaultValue) this.value=\'\' "' ?> onblur="if (this.value== '' ) this.value=this.defaultValue" title="Tél 2" />
          <input name="tel2_abr_corresp" id="tel2_abr_corresp" type="text" value="<?php echo $Tel_Abr_2 ?>"  size="5" maxlength="20" <?php if ($Envoyer=='Nouveau') echo 'onfocus="if (this.value== this.defaultValue) this.value=\'\' "' ?> onblur="if (this.value== '' ) this.value=this.defaultValue" />
          <label for="liste_type_tel2">
            Type : 
          </label>
          <select name="liste_type_tel2" id="liste_type_tel2">
            <option  value="Autre">
              Autre
            </option>
<?php
  foreach ($ligne_all_tel_type2 AS $ligne_tel_type2)
  {	
    if ($ligne_tel_type2['Tel_Type2'])//ON ELIMINE LES LIGNES 
    {
?>
            <option <?php if ($Tel_Type2==$ligne_tel_type2['Tel_Type2']) echo "selected='selected'" ?> value="<?php echo $ligne_tel_type2['Tel_Type2']?>">
              <?php echo $ligne_tel_type2['Tel_Type2']?>
            </option>
<?php
    }
  }
?>				
          </select>
          <label for="tel2_type_corresp">
            Si Autre, pr&eacute;ciser :
          </label>
          <input name="tel2_type_corresp" id="tel2_type_corresp" type="text" value="" size="5" maxlength="20" />
          <br />
          <input name="tel3_corresp" id="tel3_corresp" type="text" value="<?php echo $Tel_3 ?>" size="10" maxlength="20"  <?php if ($Envoyer=='Nouveau') echo 'onfocus="if (this.value== this.defaultValue) this.value=\'\' "' ?> onblur="if (this.value== '' ) this.value=this.defaultValue" title="Tél 3" />
          <input name="tel3_abr_corresp" id="tel3_abr_corresp" type="text" value="<?php echo $Tel_Abr_3 ?>"  size="5" maxlength="20" <?php if ($Envoyer=='Nouveau') echo 'onfocus="if (this.value== this.defaultValue) this.value=\'\' "' ?> onblur="if (this.value== '' ) this.value=this.defaultValue" />
          <label for="liste_type_tel3">
            Type : 
          </label>
          <select name="liste_type_tel3" id="liste_type_tel3" >
            <option  value="Autre">
              Autre
            </option>
<?php
  foreach ($ligne_all_tel_type3 AS $ligne_tel_type3)
  {
    if ($ligne_tel_type3['Tel_Type3'])//ON ELIMINE LES LIGNES VIDES
    {
?>
          <option <?php if ($Tel_Type3==$ligne_tel_type3['Tel_Type3']) echo "selected='selected'" ?> value="<?php echo $ligne_tel_type3['Tel_Type3']?>">
            <?php echo $ligne_tel_type3['Tel_Type3']?>
          </option>
<?php
    }
  }
?>				
        </select>
        <label for="tel3_type_corresp">
          Si Autre, pr&eacute;ciser :
        </label>
        <input name="tel3_type_corresp" id="tel3_type_corresp" type="text" value="" size="5" maxlength="20" />
        <br />
        <textarea name="Notes_corresp" id="Notes_corresp" rows="3" cols="60" <?php if ($Envoyer=='Nouveau') echo 'onfocus="if (this.value== this.defaultValue) this.value=\'\' "' ?> onblur="if (this.value== '' ) this.value=this.defaultValue" title="Notes" ><?php echo $Note ?></textarea>
        <input name="envoyer" type="submit" value="Ajouter" onclick="return check_data()"/>
<?php
if ($Envoyer=="Modifier")
{
?>
        <input name="ID_PrimKey" type="hidden" value="<?php echo $ID_PrimKey ?>" />
        <input name="envoyer" type="submit" value="Modifier" onclick="return check_data()"/>
        <input name="reset" type="reset" value="R&eacute;initialiser" />
<?php
}
?>
      </form>
   </fieldset>
  </div>
<?php
}
elseif ($Envoyer=="Supprimer")
{
//On cherche si patients liés
  $sql2=$pdo->prepare("SELECT * FROM fchpat_Intervenants INNER JOIN IndexNomPrenom ON fchpat_Intervenants.fchpat_Intervenants_PatGUID=IndexNomPrenom.FchGnrl_IDDos WHERE fchpat_Intervenants_IntervPK= ? ORDER BY FchGnrl_NomDos,FchGnrl_Prenom");
  $sql2->bindValue(1, $_GET['ID_corresp'], PDO::PARAM_STR);
  $sql2->execute();
  $ligne2_all=$sql2->fetchAll(PDO::FETCH_ASSOC);
  $sql2->closeCursor();
  $count2=count($ligne2_all);

//On cherche si l'intervenant est un utilisateur

?>
  <div class="groupe">
    <h1>
      Mode suppression
    </h1>
    <b>Confirmez-vous la suppression de la fiche de <a href="fiche_intervenant.php?intervenant=<?php echo $ID_PrimKey ?>"><?php echo stripslashes($nom)." ".stripslashes($prenom) ?> </a></b>, lié à <?php echo $count2 ?> patients ?
    <br />
<?php
  if ($ligne['Login'])
  {
?>
  <div class="information">
    Avertissement : cet intervenant est aussi un utilisateur du logiciel !<br />
<?php
//On cherche s'il possède des documents    
    $sql_documents_create=$pdo->prepare("SELECT * FROM RubriquesHead WHERE RbDate_CreateUser=?");
    $sql_documents_create->bindValue(1, $ligne['Login'], PDO::PARAM_STR);
    $sql_documents_create->execute();
    $ligne_all_documents_create=$sql_documents_create->fetchAll(PDO::FETCH_ASSOC);
    $sql_documents_create->closeCursor();
    $count_documents_create=count($ligne_all_documents_create);
  
    $sql_documents_sign=$pdo->prepare("SELECT * FROM RubriquesHead WHERE RbDate_CreateSignUser=?");
    $sql_documents_sign->bindValue(1, $ligne['Login'], PDO::PARAM_STR);
    $sql_documents_sign->execute();
    $ligne_all_documents_sign=$sql_documents_sign->fetchAll(PDO::FETCH_ASSOC);
    $sql_documents_sign->closeCursor();
    $count_documents_sign=count($ligne_all_documents_sign);
?>
Nombre de documents créés : <?php echo $count_documents_create ?><br />
Nombre de documents signataire : <?php echo $count_documents_sign ?><br />
Ces documents n'auront plus de propriétaire !
  </div>
<?php
//On cherche s'il possède des rendez-vous

    $sql_rdv=$pdo->prepare("SELECT * FROM agenda WHERE RDV_PrisAvec=?");
    $sql_rdv->bindValue(1, $ligne['Login'], PDO::PARAM_STR);
    $sql_rdv->execute();
    $ligne_all_rdv=$sql_rdv->fetchAll(PDO::FETCH_ASSOC);
    $sql_rdv->closeCursor();
    $count_rdv=count($ligne_all_rdv);

    if ($count_rdv)
    {
?>
  <div class="information">
Cet utilisateur a <?php echo $count_rdv ?> rendez-vous.
  </div>
<?php
    }
  }
  if ($count2)
  {
    echo "<br /><strong>Patients liés :</strong><br />";
    foreach ($ligne2_all AS $ligne2)
//    while ($ligne2=mysqli_fetch_array($resultat2))
    {
      echo "<a href=\"patient.php?GUID=".$ligne2['FchGnrl_IDDos']."\">".$ligne2['FchGnrl_NomDos']."</a> ".$ligne2['FchGnrl_Prenom']."<br />";
    }
  }
?>
<br /><br />
      <form action="validation_correspondant.php" method="get">
	<div>
	  <input name="ID_PrimKey" type="hidden" value="<?php echo $ID_PrimKey ?>" />
	  <input name="Nom" type="hidden" value="<?php echo $nom ?>" />
	  <input name="confirmer" type="submit" value="Supprimer" />
	  <input name="confirmer" type="submit" value="Conserver" />
	</div>
      </form>
      <p>
	<b>Attention ! Cette action est irr&eacute;versible et entra&icirc;ne une perte d&eacute;finitive de donn&eacute;es.</b>
      </p>
  </div>
 </div>
<?php
} //fin supprimer
?>

<?php
include("inc/footer.php");
?>