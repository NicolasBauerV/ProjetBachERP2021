<?php
ob_start(); // retenir l’envoi de données
    require 'connexion_déconnexion/bdd_connexion.php';
    // traitement des informations
    if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['cycle']) && !empty($_POST['email']) && !empty($_POST['tel']) && !empty($_POST['message'])) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $formation = $_POST['cycle'];
        $email = $_POST['email'];
        $tel = $_POST['tel'];
        $newsletter = null;
        if (isset($_POST['newsletter'])) {
            $newsletter = $_POST['newsletter'];
        }
        $message = $_POST['message'];

        //Cookies
        setcookie('email', htmlspecialchars($email), time() + 24 * 3600, null, null, false, true);
        setcookie('nom', htmlspecialchars($nom), time() + 24 * 3600, null, null, false, true);
        setcookie('prenom', htmlspecialchars($prenom), time() + 24 * 3600, null, null, false, true);


        if ($newsletter == "on") {
            $newsletter = '1';
            $request = $bdd->prepare('INSERT INTO renseignements (nom,prenom,email,formations,tel,newletters,msg) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $request->execute(array($nom, $prenom, $email, $formation, $tel, $newsletter, $message));
            include_once './emails/email.php';
            sendMail($email, $nom, $prenom); // envoie d'email
            header('location:./formulaire_renseignement.php?success=1');
            exit();
        }

        if (empty($newsletter)) {
            $newsletter = '0';
            $request = $bdd->prepare('INSERT INTO renseignements (nom,prenom,email,formations,tel,newletters,msg) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $request->execute(array($nom, $prenom, $email, $formation, $tel, $newsletter, $message));
            header('location: ./validation_checkBox.php');
            exit();
        }
    }
    ob_end_flush(); // libère les données retenues
?>

<!Doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ludus-ERP : Renseignement</title>
    <link type="text/css" rel="stylesheet" href="../src/style/formulaire.css?t=<? echo time(); ?>"/>
</head>
<body>
    <header>
        <h1>Bonjour, auriez-vous une question ? Vous souhaitez recevoir une brochure ?</h1>
        <?php 
            if (isset($_GET['success'])) {
                echo '<p class="success">Nous avons bien reçus vos informations !</p>
                <p class="success"><b>Un email vous a été envoyé.</b></p>
                <p class="error"><b>Vérifier vos spams si vous pensez ne pas l\'avoir pas reçus.</b></p>';
            }
        ?>
    </header>
    <div id="wrapper">
        <form method="post" action="formulaire_renseignement.php">
            <section class="petit-container">
                <input required type="text" name="nom" id="nom" placeholder="*Votre nom :">
                <input required type="text" name="prenom" id="prenom" placeholder="*Votre prenom :">
                <select required name="cycle" id="cycle">
                    <option value="">*Choisissez une formation</option>
                    <option value="Cycle-1">Cycle 1</option>
                    <option value="Cycle-2">Cycle 2</option>
                    <option value="Cycle-3">Cycle 3</option>
                </select>
                <input required type="email" name="email" id="email" placeholder="*Votre email :">
                <input required type="text" name="tel" id="tel" placeholder="*Numero tel :">
                <label for="newsletter">
                    <input type="checkbox" name="newsletter" id="newsletter"> Recevoir les newsletters, <br> (Portes ouvertes, Réunions d'informations)
                </label>
            </section>
            <section class="petit-container">
                <textarea required name="message" id="message" cols="50" rows="10" placeholder="*Rédiger votre message..."></textarea>
            </section>
            <section id="contact" class="petit-container">
                <h2>Contact</h2>
                <h4>N° Téléphone</h4>
                <p style="color: red">
                    +33(0) 3 90 20 21 77
                    <br>
                    +33(0) 6 95 34 85 67
                </p>
                <h4>Adresse Postal</h4>
                <p style="color: red">24 Place des Halles 67000 Strasbourg</p>
                <h4>Email de contact</h4>
                <a href="mailto:contact@ludus-academie.fr">contact@ludus-academie.fr</a>
            </section>
            <br>
            <button id="btn-send">Envoyer</button>
        </form>
    </div>
    
</body>
</html>