<!DOCTYPE html>
<html>
<head lang="fr">
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/feuilleSurveillance.css">
    <title>FEUILLE DE SURVEILLANCE</title>
</head>
<body>
    <div>
        <h1>FEUILLE DE SURVEILLANCE</h1>
        <tr>
            <td>
                <label>PRENOMS :</label>
            </td>
            <td>
                <input type="text" name="prenomsSurveillance">
            </td>
            <td>
                <label for="nomSurveillance">Nom :</label>
            <td>
                <input type="text" name="nomSurveillance" id="nomSurveillance">
            </td>
        </tr>
        <br>
        <br>
        <table border="1px">
            <thead>
                <tr>
                    <td colspan="2">DATE ET HEURE</td>
                    <td colspan="6">CONSTANTES</td>
                    <td colspan="2">ETAT DE LA CONSCIENCE</td>
                    <td colspan="2">PERFUSION</td>
                    <td>GLYCEMIE CAPILLAIRE</td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td>Heures</td>
                    <td>T°</td>
                    <td>Pouls</td>
                    <td>Diurèse</td>
                    <td>FR</td>
                    <td>TA</td>
                    <td>Sao2</td>
                    <td>Globale</td>
                    <td>Glasgow</td>
                    <td>Soluté</td>
                    <td>Débit</td>
                </tr>
            </thead>
            <tbody>
                    <tr>
                        <td><input type="date" name="dateSurveillance"></td>
                        <td><input type="time" name="timeSurveillance"></td>
                        <td><input type="text" name="temperature"></td>
                        <td><input type="text" name="pouls"></td>
                        <td><input type="text" name="diurese"></td>
                        <td><input type="text" name="fr"></td>
                        <td><input type="text" name="ta"></td>
                        <td><input type="text" name="sao2"></td>
                        <td><input type="text" name="globale"></td>
                        <td><input type="text" name="glasgow"></td>
                        <td><input type="text" name="solute"></td>
                        <td><input type="text" name="debit"></td>
                        <td><input type="text" name="glycemie"></td>
                    </tr>
            </tbody>
        </table>
    </div>
</body>
</html>