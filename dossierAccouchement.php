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
            $date = date('Y-m-d');






            if(isset($_POST['valider'])) { 


                // on insere les antecdents 


                    $insert_antecedent =$pdo->prepare("INSERT INTO   antecedents_accouchement (index_patient,AVT,GESTE,ROM,PARTIE,MNE,ENFVIV,DCDAVANT,DCDAPRES) VALUES (:index_patient,:avt,:geste,:rom,:partie,:mne,:enfviv,:dcdav , :dcdap)");

                

                    
                      $insert_antecedent->bindValue(':index_patient' ,$patient);
                      $insert_antecedent->bindValue(':avt' , $_POST['avt']);
                      $insert_antecedent->bindValue(':geste' , $_POST['geste']);
                      $insert_antecedent->bindValue(':rom' , $_POST['rom']);
                      $insert_antecedent->bindValue(':partie' , $_POST['partie']);
                      $insert_antecedent->bindValue(':mne' , $_POST['mne']);
                      $insert_antecedent->bindValue(':enfviv' , $_POST['enfviv']);
                      $insert_antecedent->bindValue(':dcdav' , $_POST['dcdav']);
                      $insert_antecedent->bindValue(':dcdap' , $_POST['dcdap']);
                      $insert_antecedent->execute();
                      

                      
                      // on insere les details de la grossesse
                      $insert_grossesse=$pdo->prepare("INSERT INTO grossesse_accouchement (index_patient,geu,prem,hta_complica,acc_dyst,cesar,date_grossesse,motif1,type_mgf,ddr,type,suivi_par,vat1,patho,f_risque,nombre_cpn,vat2,hospitalisation,reference,vat3,motif2) VALUES (:index_patient,:geu,:prem,:hta_complica,:acc_dyst,:cesar,:date_grossesse,:motif1,:type_mgf,:ddr,:type,:suivi_par,:vat1,:patho,:f_risque,:nombre_cpn,:vat2,:hospitalisation,:reference,:vat3,:motif2)");

                      $insert_grossesse->bindValue(':index_patient' , $patient);
                      $insert_grossesse->bindValue(':geu' , $_POST['geu']);
                      $insert_grossesse->bindValue(':prem' , $_POST['prem']);
                      $insert_grossesse->bindValue(':hta_complica' , $_POST['hta_complica']);
                      $insert_grossesse->bindValue(':acc_dyst' , $_POST['acc_dyst']);
                      $insert_grossesse->bindValue(':cesar' , $_POST['cesar']);
                      $insert_grossesse->bindValue(':date_grossesse' , $_POST['date_grossesse']);
                      $insert_grossesse->bindValue(':motif1' , $_POST['motif1']);
                      $insert_grossesse->bindValue(':type_mgf' , $_POST['type_mgf']);
                      $insert_grossesse->bindValue(':ddr' , $_POST['ddr']);
                      $insert_grossesse->bindValue(':type' , $_POST['type']);
                      $insert_grossesse->bindValue(':suivi_par' , $_POST['suivi_par']);
                      $insert_grossesse->bindValue(':vat1' , $_POST['vat1']);
                      $insert_grossesse->bindValue(':patho' , $_POST['patho']);
                      $insert_grossesse->bindValue(':f_risque' , $_POST['f_risque']);
                      $insert_grossesse->bindValue(':nombre_cpn' , $_POST['nombre_cpn']);
                      $insert_grossesse->bindValue(':vat2' , $_POST['vat2']);
                      $insert_grossesse->bindValue(':hospitalisation' , $_POST['hospitalisation']);
                      $insert_grossesse->bindValue(':reference' , $_POST['reference']);
                      $insert_grossesse->bindValue(':vat3', $_POST['vat3']);
                      $insert_grossesse->bindValue(':motif2',$_POST['motif2']);
                      $insert_grossesse->execute();
                    


                      //on insere les constantes de la grossesse 


                      $insert_constantes_gro = $pdo->prepare("INSERT INTO  constante_grossesse_accouchement(index_patient , grsh , test_emmel , gaj , bw , aghbs , ptme , nfs , creat , alat , asat) VALUES (:index_patient , :grsh , :test_emmel , :gaj , :bw , :aghbs ,:ptme ,:nfs , :creat ,:alat ,:asat)");

                      $insert_constantes_gro->bindValue(':index_patient' , $patient);
                      $insert_constantes_gro->bindValue(':grsh' , $_POST['grsh']);
                      $insert_constantes_gro->bindValue(':test_emmel' , $_POST['testEmmel']);
                      $insert_constantes_gro->bindValue(':gaj' , $_POST['gaj']);
                      $insert_constantes_gro->bindValue(':bw', $_POST['bw']);
                      $insert_constantes_gro->bindValue(':aghbs' , $_POST['aghbs']);
                      $insert_constantes_gro->bindValue(':ptme' , $_POST['ptme']);
                      $insert_constantes_gro->bindValue(':nfs' , $_POST['nfs']);
                      $insert_constantes_gro->bindValue(':creat' , $_POST['creat']);
                      $insert_constantes_gro->bindValue(':alat' , $_POST['alat']);
                      $insert_constantes_gro->bindValue(':asat' , $_POST['asat']);
                      $insert_constantes_gro->execute();
                      


                      //on insere les details de l'admission 

                      $insert_admission = $pdo->prepare("INSERT INTO admission(index_patient,date_admission,heure,par,evacuepar,hu,poids,pde_intacte_rompue,presentation,diagnostic,bdc,taille,heurepde,bassin,decision,temperature,la,col,paleur,ta) VALUES (:index_patient,:date_admission,:heure,:par,:evacuepar,:hu,:poids,:pde_intacte_rompue,:presentation,:diagnostic,:bdc,:taille,:heurepde,:bassin,:decision,:temperature,:la,:col,:paleur,:ta)");

                      $insert_admission->bindValue(':index_patient', $patient);
                      $insert_admission->bindValue(':date_admission' , $_POST['date_admission']);
                      $insert_admission->bindValue(':heure' , $_POST['heure']);
                      $insert_admission->bindValue(':par' , $_POST['par']);
                      $insert_admission->bindValue(':evacuepar' , $_POST['evacuepar']);
                      $insert_admission->bindValue(':hu' , $_POST['hu']);
                      $insert_admission->bindValue(':poids' , $_POST['poids']);
                      $insert_admission->bindValue(':pde_intacte_rompue' , $_POST['pde_intacte_rompue']);
                      $insert_admission->bindValue(':presentation' , $_POST['presentation']);
                      $insert_admission->bindValue(':diagnostic' , $_POST['diagnostic']);
                      $insert_admission->bindValue(':bdc' , $_POST['bdc']);
                      $insert_admission->bindValue(':taille' , $_POST['taille']);
                      $insert_admission->bindValue(':heurepde' , $_POST['heurepde']);
                      $insert_admission->bindValue(':bassin' , $_POST['bassin']);
                      $insert_admission->bindValue(':decision' , $_POST['decision']);
                       $insert_admission->bindValue(':temperature' , $_POST['temperature']);
                        $insert_admission->bindValue(':la' , $_POST['la']);
                         $insert_admission->bindValue(':col' , $_POST['col']);
                          $insert_admission->bindValue(':paleur' , $_POST['paleur']);
                           $insert_admission->bindValue(':ta' , $_POST['ta']);
                           $insert_admission->execute();
                           



                    //on insere les details de l'accouchement

                    $insert_accouchement = $pdo->prepare("INSERT INTO accouchement(index_patient,type,date_accouchement,transfert,delivrance,hemorragie,antibiotique,heure_accouchement,motif,gatpa,ocytociquesperparfum,anticonvulsivant,par,ocytociquespostparfum) VALUES (:index_patient ,:type,:date_accouchement,:transfert,:delivrance,:hemorragie,:antibiotique,:heure_accouchement,:motif,:gatpa,:ocytociquesperparfum,:anticonvusivant,:par,:ocytociquespostparfum)");

                    $insert_accouchement->bindValue(':index_patient' , $patient);
                    $insert_accouchement->bindValue(':type' , $_POST['type']);
                    $insert_accouchement->bindValue(':date_accouchement' , $_POST['date_accouchement']);
                    $insert_accouchement->bindValue(':transfert' , $_POST['transfert']);
                    $insert_accouchement->bindValue(':delivrance' , $_POST['delivrance']);
                    $insert_accouchement->bindValue(':hemorragie' , $_POST['hemorragie']);
                    $insert_accouchement->bindValue(':antibiotique' , $_POST['antibiotique']);
                    $insert_accouchement->bindValue(':heure_accouchement' , $_POST['heure_accouchement']);
                    $insert_accouchement->bindValue(':motif' , $_POST['motif']);
                    $insert_accouchement->bindValue(':gatpa' , $_POST['gatpa']);
                    $insert_accouchement->bindValue(':ocytociquesperparfum' , $_POST['ocytociquesperparfum']);
                    $insert_accouchement->bindValue(':anticonvusivant' , $_POST['anticonvusivant']);
                    $insert_accouchement->bindValue(':par' , $_POST['par']);
                    $insert_accouchement->bindValue(':ocytociquespostparfum' , $_POST['ocytociquespostparfum']);

                    $insert_accouchement->execute();
                    



                    //on insere les details de l'enfant

                    $insert_enfant = $pdo->prepare("INSERT INTO enfant(index_patient , sexe , reanimation , poids_placenta , aspect_cordon , sat , poids , taille , insertion , longeur_cordon , vit_k , collyre , apgard1 , pc , apgard5 , pt , malf , pb) VALUES (:index_patient ,:sexe , :reanimation,:poids_placenta,:aspect_cordon,:sat,:poids,:taille,:insertion,:longueur_cordon,:vit_k,:collyre,:apgard1,:pc,:apgard5,:pt,:malf,:pb)");

                    $insert_enfant->bindValue(':index_patient' , $patient);
                    $insert_enfant->bindValue(':sexe' , $_POST['sexe']);
                    $insert_enfant->bindValue(':reanimation' , $_POST['reanimation']);
                    $insert_enfant->bindValue(':poids_placenta' , $_POST['poids_placenta']);
                    $insert_enfant->bindValue(':aspect_cordon' , $_POST['aspect_cordon']);
                    $insert_enfant->bindValue(':sat' , $_POST['sat']);
                    $insert_enfant->bindValue(':poids' , $_POST['poids']);
                    $insert_enfant->bindValue(':taille' , $_POST['taille']);
                    $insert_enfant->bindValue(':insertion' , $_POST['insertion']);
                    $insert_enfant->bindValue(':longueur_cordon' , $_POST['longueur_cordon']);
                    $insert_enfant->bindValue(':vit_k' , $_POST['vit_k']);
                    $insert_enfant->bindValue(':collyre' , $_POST['collyre']);
                    $insert_enfant->bindValue(':apgard1' , $_POST['apgard1']);
                    $insert_enfant->bindValue(':pc' , $_POST['pc']);
                    $insert_enfant->bindValue(':apgard5' , $_POST['apgard5']);
                    $insert_enfant->bindValue(':pt' , $_POST['pt']);
                    $insert_enfant->bindValue(':malf' , $_POST['malf']);
                    $insert_enfant->bindValue(':pb' , $_POST['pb']);
                    $insert_enfant->execute();
                    





                    




            }
     
    ?>
    <div id="index" align="right"> 
        <label>N° INDEX</label>
        <input type="text" name="index" value="<?php echo $patient; ?>">
    </div>
    <table>
        <tr>
            <td>
                <label>NOM/PRENOMS</label>
             </td>
             <td colspan="3">
                <input type="text" name="nom" value="<?php echo $nom." ".$prenom; ?>">
             </td>
            <td>
                <label>Date de Naissane</label>
            </td>
            <td colspan="2">
                 <input type="text" name="age" value="<?php echo $ddn?>">
            <td>
        </tr>
        <tr>
            <td>
                <label>Adresse</label>
            </td>
            <td>
                <input type="text" name="adresse" value="<?php echo $adresse?>">
            </td>

            <td>
                <label>Profession</label>
            </td>
            <td>
                <input type="text" name="profession" value="<?php echo $profession?>">
            </td>
            <td>
                <label>Tél</label>
            </td>
            <td>
                <input type="text" name="telephone" value="<?php echo $tel1?>">
             <td>
        </tr>
        <tr>
            <td>
                 <label>Nom Epoux</label>
            </td>
            <td>
                 <input type="text" name="nomEpoux">
            </td>
            <td>
                 <label>Profession</label>
            </td>
            <td>
                 <input type="text" name="professionEpoux">
            </td>
            <td>
                 <label>Tél</label>
            </td>
            <td>
                 <input type="text" name="telephoneEpoux" value="<?php echo $tel2?>">
            </td>
        </tr>
    </table>
    <hr>
    <form action="#" method="POST">
    <table>
        <tr>
            <td colspan="2">
                <h2>ANTECEDENTS</h2>
            </td>
            <td>
                <label>GESTE</label>
            </td>
            <td>
                <input type="number" name="geste">
            </td>
            <td>
                <label>PARTIE</label>
            </td>
            <td>
                <input type="number" name="partie">
            </td>
            <td>
                <label>ENF VIV</label>
            </td>
            <td>
                <input type="number" name="enfviv">
            </td>
        </tr>
        <tr>
            <td>
                <label>AVT</label>
            </td>
            <td>
                <input type="number" name="avt">
            </td>
            <td>
                <label>ROM</label>
            </td>
            <td>
                <input type="number" name="rom">
            </td>
            <td>
                <label>M.NE</label>
            </td>
            <td>
                <input type="number" name="mne">
            </td>
            <td>
                <label>DCD av.8.j</label>
            </td>
            <td>
                <input type="number" name="dcdav">
            </td>
            <td>
                <label>DCD ap.8.j</label>
            </td>
            <td>
                <input type="number" name="dcdap">
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
                Oui<input type="radio" name="hta_complica" value="oui"> /  Non<input type="radio" name="hta_complica" value="non">
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
                Oui<input type="radio" name="acc_dyst" value="oui"> /  Non<input type="radio" name="acc_dyst" value="non">
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
                <input type="date" name="date_grossesse" value="">
            </td>
            <td>
                <label>Motif</label>
                <input type="text" name="motif1">
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
                <input type="number" name="type_mgf">
            </td>
            <td colspan="4"></td>
            <td>
                <label>BW :</label>
            </td>
            <td>
                <input type="text" name="bw">
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
                <input type="text" name="suivi_par">
            </td>
            <td>
                <label>Nombre CPN</label>
            </td>
            <td>
                <input type="number" name="nombre_cpn">
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
                <input type="text" name="alat">
            </td>
        </tr> <tr>
            <td>
                <label>F.risque</label>
            </td>
            <td>
                <input type="text" name="f_risque">
            </td>
            <td>
                <label>Réference</label>
            </td>
            <td>
                oui<input type="radio" name="reference" value="oui"> / non <input type="radio" name="reference" value="non">
            </td>
            <td>
                <label>Motif</label>
                <input type="text" name="motif2">
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
                <input type="date" name="date_admission">
            </td>
            <td></td>
            <td>
                <label>Heure :</label>
            </td>
            <td>
                <input type="time" name="heure">
            </td>
            <td colspan="2"></td>
            <td>
                <label>Par : </label>
            </td>
            <td>
                <input type="text" name="par">
            </td>
        </tr>
        <tr>
            <td>
                <label>EVACUEE par: </label>
            </td>
            <td>
                <input type="text" name="evacuepar">
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
               <input type="number" name="temperature">
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
               <input type="text" name="ta" PLACEHOLDER="                    /             ">
           </td>
       </tr>
        <tr>
            <td>
                <label>
                    POIDS :
                </label>
            </td>
            <td>
                <input type="number" name="poids">
            </td>
            <td>
                <label>TAILLE :</label>
            </td>
            <td>
                <input type="number" name="taille">
            </td>
        </tr>
        <tr>
            <td>
                <label>PDE intacte,rompue le</label>
            </td>
            <td>
                <input type="text" name="pde_intacte_rompue">
            </td>
            <td>
                <label>à</label>
            </td>
            <td>
                <input type="time" name="heurepde">
            </td>
            <td></td>
            <td>
                <label>LA :</label>
            </td>
            <td>
                <input type="text" name="la">
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
                <input type="date" name="date_accouchement">
            </td>
            <td>
                <label>à</label>
            </td>
            <td>
                <input type="time" name="heure_accouchement">
            </td>
            <td>
                <label>par</label>
            </td>
            <td>
                <input type="text" name="par">
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
                <input type="text" name="motif">
            </td>
        </tr>
        <tr>
            <td>
                <label>Délivrance:</label>
            </td>
            <td>
                <select name="delivrance">
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
                <input type="radio" id="radio1" name="gatpa" value="oui">
                <label for="radio1">oui</label>
                /
                <input type="radio" id="radio2" name="gatpa" value="non">
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
                oui<input type="radio" name="ocytociquesperparfum" value="oui"> / non <input type="radio" name="ocytociquesperparfum" value="non">
            </td>
            <td>
                <label>OCYTOCIQUES POST PARFUM</label>
            </td>
            <td>
                oui<input type="radio" name="ocytociquespostparfum" value="oui"> / non <input type="radio" name="ocytociquespostparfum" value="non">
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
                <input type="number" name="poids" placeholder="en gr">
            </td>
            <td>
                <label>APGARD 1'</label>
            </td>
            <td>
                <input type="text" name="apgard1" placeholder="                  /           ">
            </td>
            <td>
                <label>APGARD 5'</label>
            </td>
            <td>
                <input type="text" name="apgard5" placeholder="                   /            ">
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
                <input type="number" name="taille">
            </td>
            <td>
                <label>PC</label>
            </td>
            <td>
                <input type="text" name="pc">
            </td>
            <td>
                <label>PT</label>
            </td>
            <td>
                <input type="text" name="pt">
            </td>
            <td>
                <label>PB</label>
            </td>
            <td>
                <input type="text" name="pb">
            </td>
        </tr>
        <tr>
            <td>
                <label>Poids Placenta</label>
            </td>
            <td>
                <input type="number" name="poids_placenta" placeholder="en gr">
            </td>
            <td>
                <label>Insertion</label>
            </td>
            <td>
                <select name="insertion">
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
                <select name="aspect_cordon">
                    <option value="grele">Grêle</option>
                    <option value="Epais">Epais</option>
                </select>
            </td>
            <td>
                <label>Longueur cordon :</label>
            </td>
            <td>
                <input type="number" name="longueur_cordon" placeholder="en cm">
            </td>
        </tr>
        <tr>
            <td>
                <label>SAT :</label>
            </td>
            <td>
                oui<input type="radio" name="d=sat" value="oui"> / non <input type="radio" name="sat" value="non">
            </td>
            <td>
                <label>Vit K:</label>
            </td>
            <td>
                oui<input type="radio" name="vit_k" value="oui"> / non <input type="radio" name="vit_k" value="non">
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