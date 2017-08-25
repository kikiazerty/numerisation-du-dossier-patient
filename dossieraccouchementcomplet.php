<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head lang="en">
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/dossierAccouchement.css">
    <title>Dossier d'accouchement</title>
</head>

<body bgcolor="yellow">
    <h3>REPUBLIQUE DU SENEGAL <BR>Ministère de la Santé et de l'Action Sociale <BR>HOPITAL DE PIKINE</h3>

    <h1>Dossier D'Accouchement</h1>

    <?php
    include("config.php");

    if (isset($_GET['GUID'])) {
         
         $patient = $_GET['GUID'];


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


         $sql=$pdo->prepare('SELECT * FROM IndexNomPrenom WHERE FchGnrl_IDDos=?');
            $sql->bindValue(1, $patient, PDO::PARAM_STR);
            $sql->execute();
            $ligne=$sql->fetch(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            $nom=$ligne["FchGnrl_NomDos"];
            $prenom=$ligne["FchGnrl_Prenom"];
            $primkey=$ligne["ID_PrimKey"];

            $sql2=$pdo->prepare('SELECT * FROM fchpat WHERE FchPat_GUID_Doss=?');
            $sql2->bindValue(1, $patient, PDO::PARAM_STR);
            $sql2->execute();
            $ligne2=$sql2->fetch(PDO::FETCH_ASSOC);
            $sql2->closeCursor();

            $ddn = $ligne2["FchPat_Nee"];
            $adresse =$ligne2["FchPat_Adresse"];
            $profession=$ligne2["FchPat_Profession"];
            $tel1 = $ligne2["FchPat_Tel1"];
            $tel2 = $ligne2["FchPat_Tel2"];


            $antecedents=$pdo->prepare('SELECT * FROM antecedents_accouchement WHERE index_patient=?');
            $antecedents->bindValue(1, $patient, PDO::PARAM_STR);
            $antecedents->execute();
            $resutlantecedent=$antecedents->fetch(PDO::FETCH_ASSOC);
            $antecedents->closeCursor();



            
    ?>
    <div id="index" align="right"> 
        <label>N° INDEX</label>
        <input type="text" name="index" value="<?php echo $patient; ?>">
    </div>
<div>
    
    <?php echo $nom." ".$prenom; ?><br>
    <?php echo $ddn?><br>
     <?php echo $adresse?><br>
     <?php echo $profession?><br>
     <?php echo $tel1?><br>
     <?php echo $tel2?><br>

</div>
   
    <hr>
    <form action="#" method="POST">
    <table>
        <tr>
            <td colspan="2">
                <h2>ANTECEDENTS</h2>
            </td>
            <td>
                <label>GESTE : </label>
            </td>
            <td>
                <?php echo $resutlantecedent['GESTE']; ?>
            </td>
            <td>
                <label>PARTIE : </label>
            </td>
            <td>
                <?php echo $resutlantecedent['PARTIE']; ?>
            </td>
            <td>
                <label>ENF VIV : </label>
            </td>
            <td>
                <?php echo $resutlantecedent['ENFVIV']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <label>AVT :</label>
            </td>
            <td>
                <?php echo $resutlantecedent['AVT']; ?>
            </td>
            <td>
                <label>ROM : </label>
            </td>
            <td>
                <?php echo $resutlantecedent['ROM']; ?>
            </td>
            <td>
                <label>M.NE : </label>
            </td>
            <td>
                <?php echo $resutlantecedent['MNE']; ?>
            </td>
            <td>
                <label>DCD av.8.j : </label>
            </td>
            <td>
                <?php echo $resutlantecedent['DCDAVANT']; ?>
            </td>
            <td>
                <label>DCD ap.8.j : </label>
            </td>
            <td>
               <?php echo $resutlantecedent['DCDAPRES']; ?>
            </td>
        </tr>
    </table>
    <hr>
    <table>
    <h2>GROSSESSE</h2>
        <tr>
             <td>
                <label>GEU</label>
            </td>
            <td>
                Oui<input type="radio" name="geu" value="oui"> /  Non<input type="radio" name="geu" value="non">
            </td>
            <td>
                <label>HTA/COMPLICA</label>
            </td>
            <td>
                Oui<input type="radio" name="htaComplica" value="oui"> /  Non<input type="radio" name="htaComplica" value="non">
            </td>
            <td colspan="2"></td>
            <td>
                <label>GRSH</label>
            </td>
            <td>
                <input type="text" name="gsrh">
            </td>
        </tr>
        <tr>
            <td>
                <label>PREM</label>
            </td>
            <td>
                Oui<input type="radio" name="prem" value="oui"> /  Non<input type="radio" name="prem" value="non">
            </td>
            <td>
                <label>ACC DYST</label>
            </td>
            <td>
                Oui<input type="radio" name="accDyst" value="oui"> /  Non<input type="radio" name="accDyst" value="non">
            </td>
            <td colspan="2"></td>
            <td>
                <label>TEST D'EMMEL</label>
            </td>
            <td>
                <input type="text" name="testEmmel">
            </td>
        </tr>
        <tr>
            <td>
                <label>Cesar</label>
            </td>
            <td>
                <input type="number" name="cesar">
            </td>
            <td>
                <label>Date</label>
            </td>
            <td>
                <input type="date" name="date">
            </td>
            <td>
                <label>Motif</label>
                <input type="text" name="motif">
            </td>
            <td></td>
            <td>
                <label>G.A.J</label>
            </td>
            <td>
                <input type="text" name="gaj">
            </td>
        </tr>
        <tr>
            <td>
                <label>Type MGF :</label>
            </td>
            <td>
                <input type="number" name="cesar">
            </td>
            <td colspan="4"></td>
            <td>
                <label>BW :</label>
            </td>
            <td>
                <input type="text" name="BW">
            </td>
        </tr>
        <tr>
            <td colspan="6"></td>
            <td>
                <label>AgHBS</label>
            </td>
            <td>
                <input type="text" name="aghbs">
            </td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr>
            
            <td colspan="1"></td>
            <td>
                <label>DDR</label>
            </td>
            <td>
                <input type="date" name="ddr">
            </td>
            <td>
               <label>type</label>
               <select name="type">
                   <option value="simple">Simple</option>
                   <option value="gemillaire">Gémellaire</option>
                   <option value="triple">Triple</option>
               </select>
            </td>
            <td colspan="2"></td>
            <td>
                <label>PTME : </label>
            </td>
            <td>
                <input type="text" name="ptme">
            </td>
        </tr>
        <tr>
            <td>
                <label>SUIVI par</label>
            </td>
            <td>
                <input type="text" name="suiviPar">
            </td>
            <td>
                <label>Nombre CPN</label>
            </td>
            <td>
                <input type="number" name="cpn">
            </td>
            <td colspan="2"></td>
            <td>
                <label>NFS :</label>
            </td>
            <td>
                <input type="text" name="nfs">
            </td>
        </tr>
        <tr>
            <td>
                <label>VAT1</label>
            </td>
            <td>
                <input type="text" name="vat1">
            </td>
            <td>
                <label>VAT2</label>
            </td>
            <td>
                <input type="text" name="vat2">
            </td>
            <td>
                <label>VAT3</label>
                <input type="text" name="vat3">
            </td>
            <td></td>
            <td>
                <label>CREAT</label>
            </td>
            <td>
                <input type="text" name="creat">
            </td>
        </tr>
        <tr>
            <td>
                <label>Patho</label>
            </td>
            <td>
                <input type="text" name="patho">
            </td>
            <td>
                <label>hospitalisation</label>
            </td>
            <td>
                oui<input type="radio" name="hospitalisation" value="oui"> / non <input type="radio" name="hospitalisation" value="non">
            </td>
            <td colspan="2"></td>
            <td>
                <label>ALAT</label>
            </td>
            <td>
                <input type="text" name="asat">
            </td>
        </tr> <tr>
            <td>
                <label>F.risque</label>
            </td>
            <td>
                <input type="text" name="fRisque">
            </td>
            <td>
                <label>Réference</label>
            </td>
            <td>
                oui<input type="radio" name="reference" value="oui"> / non <input type="radio" name="reference" value="non">
            </td>
            <td>
                <label>Motif</label>
                <input type="text" name="motif">
            </td>
            <td></td>
            <td>
                <label>ASAT</label>
            </td>
            <td>
                <input type="text" name="asat">
            </td>
        </tr>
    </table>
    <hr>
    <table>
        <tr>
            <td>
                <h2>ADMISSION le :</h2>
            </td>
            <td></td>
            <td>
                <label>Date</label>
            </td>
            <td>
                <input type="text" name="dateAdmission">
            </td>
            <td></td>
            <td>
                <label>Heure :</label>
            </td>
            <td>
                <input type="time" name="HeureAdmission">
            </td>
            <td colspan="2"></td>
            <td>
                <label>Par : </label>
            </td>
            <td>
                <input type="text" name="admisPar">
            </td>
        </tr>
        <tr>
            <td>
                <label>EVACUEE par: </label>
            </td>
            <td>
                <input type="text" name="evacuePar">
            </td>
        </tr>
       <tr>
           <td>
               <label>HU</label>
           </td>
           <td>
               <input type="number" name="hu" placeholder="en cm">
           </td>
           <td>
               <label>BDC</label>
           </td>
           <td>
               oui<input type="radio" name="bdc" value="oui"> / non <input type="radio" name="bdc" value="non">
           </td>
           <td></td>
           <td>
               <label>T° :</label>
           </td>
           <td>
               <input type="number" name="t">
           </td>
           <td colspan="2"></td>
           <td>
               <label>Pâleur</label>
           </td>
           <td>
               oui<input type="radio" name="paleur" value="oui"> / non <input type="radio" name="paleur" value="non">
           </td>
           <td>
               <label>TA</label>
           </td>
           <td>
               <input type="text" name="ta1" PLACEHOLDER="                    /             ">
           </td>
       </tr>
        <tr>
            <td>
                <label>
                    POIDS :
                </label>
            </td>
            <td>
                <input type="text" name="poids">
            </td>
            <td>
                <label>TAILLE :</label>
            </td>
            <td>
                <input type="text" name="taille">
            </td>
        </tr>
        <tr>
            <td>
                <label>PDE intacte,rompue le</label>
            </td>
            <td>
                <input type="text" name="pde">
            </td>
            <td>
                <label>à</label>
            </td>
            <td>
                <input type="time" name="heurePde">
            </td>
            <td></td>
            <td>
                <label>LA :</label>
            </td>
            <td>
                <input type="text" name="laPde">
            </td>
        </tr>
        <tr>
            <td>
                <label>PRESENTATION</label>
            </td>
            <td>
                <input type="text" name="presentation">
            </td>
            <td>
                <label>BASSIN</label>
            </td>
            <td>
                <input type="text" name="bassin">
            </td>
            <td></td>
            <td>
                <label>COL</label>
            </td>
            <td>
                <input type="number" name="col">
                <label>cm</label>
            </td>
        </tr>
        <tr>
            <td>
                <label>DIAGNOSTIC : </label>
            </td>
            <td>
                <input type="text" name="diagnostic">
            </td>
            <td>
                <label>DECISION : </label>
            </td>
            <td>
                <input type="text" name="decision">
            </td>
        </tr>
    </table>
    <hr>
    <table>
        <tr>
            <td>
                <h2>ACCOUCHEMENT</h2>
            </td>
            <td>
                <label>type</label>
                <select name="type">
                    <option value="normal">normal</option>
                    <option value="forceps/Ventouse">forceps/Ventouse</option>
                    <option value="manoeuvre">Manoeuvre</option>
                    <option value="cesarienne">Césarienne</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <label>Accouché le</label>
            </td>
            <td>
                <input type="date" name="accoucherLe">
            </td>
            <td>
                <label>à</label>
            </td>
            <td>
                <input type="time" name="heureAccouchement">
            </td>
            <td>
                <label>par</label>
            </td>
            <td>
                <input type="text" name="parAccouchement">
            </td>
        </tr>
        <tr>
            <td>
                <label>Transfert:</label>
            </td>
            <td>
                <input type="text" name="transfert">
            </td>
            <td>
                <label>motif:</label>
            </td>
            <td>
                <input type="text" name="motifTransfert">
            </td>
        </tr>
        <tr>
            <td>
                <label>Délivrance:</label>
            </td>
            <td>
                <select name="spontane">
                    <option value="spontane">spontane</option>
                    <option value="DA">DA</option>
                    <option value="RU">RU</option>
                    <option value="Placenta">Placenta</option>
                </select>
            </td>
            <td>
                <label>GATPA:</label>
            </td>
            <td>
                <input type="radio" id="radio1" name="GATPA" value="oui">
                <label for="radio1">oui</label>
                /
                <input type="radio" id="radio2" name="GATPA" value="non">
                <label for="radio2">non</label>
            </td>
        </tr>
        <tr>
            <td>
                <label>Hémorragie </label>
            </td>
            <td>
                oui<input type="radio" name="hemorragie" value="oui"> / non <input type="radio" name="hemorragie" value="non">
            </td>
            <td>
                <label>OCYTOCIQUES PER PARFUM</label>
            </td>
            <td>
                oui<input type="radio" name="operp" value="oui"> / non <input type="radio" name="operp" value="non">
            </td>
            <td>
                <label>OCYTOCIQUES POST PARFUM</label>
            </td>
            <td>
                oui<input type="radio" name="oposp" value="oui"> / non <input type="radio" name="oposp" value="non">
            </td>
        </tr>
        <tr>
            <td>
                <label>Antibiotiques </label>
            </td>
            <td>
                oui<input type="radio" name="antibiotiques" value="oui"> / non <input type="radio" name="antibiotiques" value="non">
            </td>
            <td>
                <label>Anticonvulsivant </label>
            </td>
            <td>
                oui<input type="radio" name="anticonvusivant" value="oui"> / non <input type="radio" name="anticonvusivant" value="non">
            </td>
            <td>
                <label>Transfusion </label>
            </td>
            <td>
                oui<input type="radio" name="transfusion" value="oui"> / non <input type="radio" name="transfusion" value="non">
            </td>
        </tr>
    </table>
    <hr>
    <table>
        <tr>
            <td>
                <h2>ENFANT</h2>
            </td>
            <td>
                <label>SEXE </label>
                <select name="sexe">
                    <option value="M">M</option>
                    <option value="F">F</option>
                </select>
            </td>
            <td>
                <label>POIDS :</label>
            </td>
            <td>
                <input type="text" name="poidsEnfant" placeholder="en gr">
            </td>
            <td>
                <label>APGARD 1'</label>
            </td>
            <td>
                <input type="text" name="apgard11" placeholder="                  /           ">
            </td>
            <td>
                <label>APGARD 5'</label>
            </td>
            <td>
                <input type="text" name="apgard51" placeholder="                   /            ">
            </td>
            <td>
                <label>MALF : </label>
            </td>
            <td>
                <input type="text" name="malf">
            </td>
            <td></td>
        </tr>
        <tr>
            <td>
                <label>REANIMATION</label>
            </td>
            <td>
                <input type="text" name="reanimation">
            </td>
            <td>
                <label>TAILLE</label>
            </td>
            <td>
                <input type="text" name="tailleEnfant">
            </td>
            <td>
                <label>PC</label>
            </td>
            <td>
                <input type="text" name="PCenfant">
            </td>
            <td>
                <label>PT</label>
            </td>
            <td>
                <input type="text" name="PTenfant">
            </td>
            <td>
                <label>PB</label>
            </td>
            <td>
                <input type="text" name="PBenfant">
            </td>
        </tr>
        <tr>
            <td>
                <label>Poids Placenta</label>
            </td>
            <td>
                <input type="text" name="poidsPlacenta" placeholder="en gr">
            </td>
            <td>
                <label>Insertion</label>
            </td>
            <td>
                <select name="Marginale">
                    <option value="Marginale">Marginale</option>
                    <option value="Centrale">Centrale</option>
                    <option value="En raquette">En raquette</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <label>Aspect cordon :</label>
            </td>
            <td>
                <select name="Grêle">
                    <option value="grele">Grêle</option>
                    <option value="Epais">Epais</option>
                </select>
            </td>
            <td>
                <label>Longueur cordon :</label>
            </td>
            <td>
                <input type="text" name="longueurCordon" placeholder="en cm">
            </td>
        </tr>
        <tr>
            <td>
                <label>SAT :</label>
            </td>
            <td>
                oui<input type="radio" name="satEnfant" value="oui"> / non <input type="radio" name="satEnfant" value="non">
            </td>
            <td>
                <label>Vit K:</label>
            </td>
            <td>
                oui<input type="radio" name="vitK" value="oui"> / non <input type="radio" name="vitK" value="non">
            </td>
            <td>
                <label>Collyre :</label>
            </td>
            <td>
                oui<input type="radio" name="collyre" value="oui"> / non <input type="radio" name="collyre" value="non">
            </td>
        </tr>
    </table>

    <input type="submit" name="valider">


    </form>



</body>
<?php } ?>
</html>