<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/dossierExterne.css">
    <title>DOSSIER EXTERNE</title>
</head>
<body>
    <h3>Ministère de la Santé <BR> et de l'Action Sociale <BR>HOPITAL DE PIKINE<br>BP:20630-Tél:33 853 00 71</h3>
    <h1>DOSSIER EXTERNE</h1>
    <div>
        <table>
            <tr>
                <td>
                    <label>Service</label>
                </td>
                <td>
                    <input type="text" name="serviceExterne">
                </td>
            </tr>
            <tr>
                <td>
                    <label>Prénoms</label>
                </td>
                <td>
                    <input type="text" name="prenomsExterne">
                </td>
            </tr>
            <tr>
                <td>
                    <label>Nom</label>
                </td>
                <td>
                    <input type="text" name="nomExterne">
                </td>
            </tr>
            <tr>
                <td>
                    <label>Age</label>
                </td>
                <td>
                    <input type="text" name="ageExterne">
                </td>
                <td>
                    <label>Sexe</label>
                </td>
                <td>
                    <input type="text" name="sexeExterne">
                </td>
            </tr>
            <tr>
                <td>
                    <label>Adresse:</label>
                </td>
                <td>
                    <input type="text" name="adresseClinique">
                </td>
            </tr>
            <tr>
                <td>
                    <label>Telephone:</label>
                </td>
                <td>
                    <input type="text" name="telephoneExterne">
                </td>
            </tr>
        </table>
    </div>
    <br>
    <br>
    <label>DATE</label>
    <label id="observation">OBSERVATIONS</label>
    <hr>
    <div class="contenuExterne">
        <table>
            <tr>
                <td>
                    <input type="date" name="dateClinique">
                    <label>Nom du medecin</label>
                </td>
                <hr class="separation">
                <td>
                    <textarea cols="120" rows="13" >
                    </textarea>
                </td>
            </tr>
        </table>
</body>
</html>