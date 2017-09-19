<!DOCTYPE html>
<html>
<head lang="fr">
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/feuilleObservationClinique.css">
    <title>FEUILLE D'OBSERVATION CLINIQUE</title>
</head>
<body>
    <div>
        <nav>REPUBLIQUE DU SENEGAL<BR>MINISTERE DE LA SANTE ET DE L'ACTION SOCIALE</nav>
        <h2>HOPITAL DE PIKINE</h2>
        <h2 id="aEncadrer">FEUILLE D'OBSERVATION CLINIQUE</h2>
    </div>
    <label id="eurograh">Eurograph: 33 832 88 30</label>
    <hr>
    <table>
        <tr>
            <td>
                 <label class="decalage">SERVICE</label>
            </td>
            <td>
                 <input type="text" name="service">
            </td>
            <td>
                <label>NUMERO DU DOSSIER</label>
            </td>
            <td>
                <input type="text" name="numDossier">
            </td>
        </tr>
        <hr class="separation">
        <tr>
            <td>
                <label class="decalage">N° du Lit</label>
            </td>
            <td>
                <input type="text" name="numLit">
            </td>
        </tr>
    </table>
    <hr>
    <table>
        <tr>
            <td>
                <label>Prénoms</label>
            </td>
            <td>
                <input type="text" name="prenomsClinique">
            </td>
            <td colspan="2"></td>
            <td>
                <label>Nom</label>
            </td>
            <td>
                <input type="text" name="nomClinique">
            </td>
        </tr>
        <tr>
            <td>
                <label>Age</label>
            </td>
            <td>
                <input type="text" name="ageClinique">
            </td>
            <td>
                <label>Sexe</label>
            </td>
            <td>
                <input type="text" name="sexeClinique">
            </td>
            <td>
                <label>Race</label>
            </td>
            <td>
                <input type="text" name="raceClinique">
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
                <label>Entré(e):</label>
            </td>
            <td>
                <input type="text" name="adresseClinique">
            </td>
        </tr>
        <tr>
            <td>
                <label>Diagnostic d'entrée:</label>
            </td>
            <td class="espace">
                <input type="text" name="adresseClinique">
            </td>
        </tr>
        <tr>
            <td>
                <label>Transeat le:</label>
            </td>
            <td>
                <input type="text" name="adresseClinique">
            </td>
        </tr>
        <tr>
            <td>
                <label>Sortie:</label>
            </td>
            <td>
                <input type="text" name="adresseClinique">
            </td>
        </tr>
        <tr>
            <td>
                <label>Diagnostic de sortie:</label>
            </td>
            <td>
                <input type="text" name="adresseClinique">
            </td>
        </tr>
    </table>
    <hr>
    <nav>NB: toujours indiquer en marge, la date des examens cliniques et le nom du medecin ou de l'Etudiant</nav>
    <div class="contenuClinique">
        <table>
            <tr>
                <td>
                    <input type="date" name="dateClinique">
                    <label>Nom du medecin</label>
                </td>
                <hr class="separation2">
                <td>
                    <textarea cols="120" rows="13" >
                    </textarea>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>