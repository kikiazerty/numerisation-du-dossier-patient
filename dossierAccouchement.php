<?php
/**
 * Created by PhpStorm.
 * User: ngnin
 * Date: 18/08/2017
 * Time: 17:35
 */ { ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head lang="en">
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/dossierAccouchement.css">
    <title></title>
</head>
<body bgcolor="yellow">
    <h1>Dossier D'Accouchement</h1>
    <label>N° INDEX</label>
    <input type="text" name="index">
    <table>
        <tr>
            <td>
                <label>NOM/PRENOMS</label>
             </td>
             <td colspan="3">
                <input type="text" name="nom">
             </td>
            <td>
                <label>AGE</label>
            </td>
            <td colspan="2">
                 <input type="text" name="age">
            <td>
        </tr>
        <tr>
            <td>
                <label>Adresse</label>
            </td>
            <td>
                <input type="text" name="adresse">
            </td>

            <td>
                <label>Profession</label>
            </td>
            <td>
                <input type="text" name="profession">
            </td>
            <td>
                <label>Tél</label>
            </td>
            <td>
                <input type="text" name="telephone">
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
                 <input type="text" name="telephoneEpoux">
            </td>
        </tr>
    </table>
    <hr>
    <table>
        <tr>
            <td colspan="2">
                <h2>Antecedents</h2>
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
                <input type="number" name="enfViv">
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
                <input type="number" name="dcdAv">
            </td>
            <td>
                <label>DCD ap.8.j</label>
            </td>
            <td>
                <input type="number" name="dcdAp">
            </td>
        </tr>
    </table>
    <hr>
    <table>
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
            </td>
            <td>
                <input type="text" name="motif">
            </td>
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
    </table>
    <br>
    <br>
    <table>
        <tr>
            <td>
                <h2>GROSSESSE</h2>
            </td>
            <td>
                <label>DDR</label>
            </td>
            <td>
                <input type="date" name="ddr">
            </td>
            <td>
               <label>type</label>
            </td>
            <td>
               <select name="type">
                   <option value="simple">Simple</option>
                   <option value="gemillaire">Gémellaire</option>
                   <option value="triple">Triple</option>
               </select>
            </td>
        </tr>
    </table>

</body>
</html>
<?php }?>