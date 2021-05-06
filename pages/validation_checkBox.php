<?php 
    require 'connexion_déconnexion/bdd_connexion.php';
    if (!empty($_POST["email"])) {
        $nom = $_GET['nom'];
        $prenom = $_GET['prenom'];
        $email = $_GET['email'];
        $formation = $_GET['formation'];
        $tel = $_GET['tel'];
        $message = $_GET['msg'];
        $valid = $_POST["newsletter-sure"];
        var_dump($nom);
        var_dump($prenom);
        var_dump($email);
        var_dump($formation);
        var_dump($tel);
        var_dump($message);
        if ($email == $_POST["email"] && $valid == "on") {
        } else {
            echo "<p class=\"error\">Votre email ne correspond pas à celui que vous avez envoyé auparavant...</p>";
        }
        $request = $bdd->prepare('INSERT INTO renseignements (nom,prenom,email,formations,tel,newletters,msg) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $request->execute(array($nom, $prenom, $email, $formation, $tel, $valid, $message));
        echo "<p class=\"success\">Nous avons bien reçu vos informations</p>";
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="../src/style/formulaire.css">
    <title>Vérification</title>
</head>
<body>
    <form id="form-sure" method="post" action="validation_checkBox.php">
        <h2>Êtes-vous sûr.e de ne pas vouloir recevoir les newsletters ?</h2>
        <label for="emailConf">
            <span>Confirmer l'email pour recevoir les newsletter :</span>
            <input type="email" name="emailConf" id="emailConf" placeholder="Confirmer votre email :">
        </label>
        <br>
        <label for="newsletter-sure">
            <input type="checkbox" name="newsletter-sure" id="newsletter-sure">
            <span>Cocher pour accepter les newsletters</span>
        </label>
        <br>
        <button type="submit" id="btn-sure">Envoyer</button>
    </form>

   <!-- <script type="text/javascript">
        // let emailInput = document.querySelector('#emailConf');
        // let checkInput = document.querySelector('#newsletter-sure');
        // if(emailInput.value != "") {
        //     checkInput.setAttribute("required", "");
        // }

        // if(checkInput.checked) {
        //     emailInput.setAttribute("required", "");
        // }
    </script> -->
</body>
</html>