<?php
ob_start(); // retenir l’envoi de données
    require 'connexion_déconnexion/bdd_connexion.php';
    require './email.php';
    $emailVerif = $_COOKIE['email'];
    var_dump($emailVerif);
    if (!empty($_POST["emailConf"])) {
        $email = $_POST["emailConf"];
        $valid = null;
        if (isset($_POST['newsletter-sure'])) {
            $valid = $_POST["newsletter-sure"];
        }
        if ($email == $emailVerif && $valid == "on") {
            $valid = 1;
            try {
                $request = $bdd->prepare('UPDATE renseignements SET newletters = ? WHERE email = ?');
                $request->execute(array($valid, $email));
                sendMail($_COOKIE['email'], $_COOKIE['nom'], $_COOKIE['prenom']); // envoie d'email
                header('Location: ../pages/validation_checkBox.php?success=1');
                exit();
            } catch (Exception $e) {
                echo '<p class="error">Nous n\'avons pas pus obtenir vos informations, veuillez réessayer...';
                sleep(4);
                header('Location: ../pages/formulaire_renseignement.php');
                exit();
            }
        }
        if (empty($valid)) {
            $valid = '0';
            try {
                $request = $bdd->prepare('UPDATE renseignements SET newletters = ? WHERE email = ?');
                $request->execute(array($valid, $email));
                sendMail($_COOKIE['email'], $_COOKIE['nom'], $_COOKIE['prenom']); // envoie d'email
                header('Location: ./validation_checkBox.php?success=1');
                die();
            } catch (Exception $e) {
                echo '<p class="error">Nous n\'avons pas pus obtenir vos informations, veuillez réessayer...';
                sleep(4);
                header('Location: ./formulaire_renseignement.php');
                exit();
            }
        }
    }
    ob_end_flush(); // libère les données retenues
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
        <?php 
            if (!isset($_GET['success'])) {
                echo '<h2>Êtes-vous sûr.e de ne pas vouloir recevoir les newsletters ?</h2>';
            }
        ?>
        <h3>
            <?php 
                if (isset($_GET['success'])) {
                    echo '<p class="success">Nous avons bien reçu vos informations, un email vous a été envoyer</p>';
                }
            ?>
        </h3>
        <label for="emailConf">
            <span>Confirmer l'email pour envoyer la demande :</span>
            <input type="email" name="emailConf" id="emailConf" placeholder="Confirmer votre email :" required>
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
        document.write('<p class=\"error\">Votre email ne correspond pas à celui que vous avez envoyé auparavant...</p>');
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