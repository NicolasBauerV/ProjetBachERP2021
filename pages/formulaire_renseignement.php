<?php
    mail("bauer.nicolas266@gmail.com", "test", "ceci est un test");
    require 'connexion_déconnexion/bdd_connexion.php';

    // traitement des informations
    if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['cycle']) && !empty($_POST['email']) && !empty($_POST['tel']) && !empty($_POST['message'])) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $formation = $_POST['cycle'];
        $email = $_POST['email'];
        $tel = $_POST['tel'];
        $newsletter = $_POST['newsletter'];
        $message = $_POST['message'];

        if ($newsletter == "on") {
            $newsletter = '1';
            // header('location: ../pages/validation_checkBox.php?nom='.$nom.'&prenom='.$prenom.'&email='.$email.'&formation='.$formation.'&tel='.$tel.'&msg='.$message);
        } else {
            $newsletter = '0';
        }
        $request = $bdd->prepare('INSERT INTO renseignements (nom,prenom,email,formations,tel,newletters,msg) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $request->execute(array($nom, $prenom, $email, $formation, $tel, $newsletter, $message));
        header('location: ../pages/formulaire_renseignement.php?success=1');
        exit();
    }
?>

<!Doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ludus-Net : Renseignement</title>
    <link type="text/css" rel="stylesheet" href="../src/style/formulaire.css?t=<? echo time(); ?>"/>
</head>
<body>
    <header>
        <h1>Bonjour, auriez-vous une question ? Vous souhaitez recevoir une brochure ?</h1>
        <?php 
            if (isset($_GET['success'])) {
                echo '<p class="success">Nous avons bien reçus les informations !</p>';
            }
        ?>
    </header>
    <form id="form1" method="post" action="formulaire_renseignement.php">
        <section class="petit-container">
            <input required type="text" name="nom" id="nom" placeholder="*Votre nom :">
            <br>
            <input required type="text" name="prenom" id="prenom" placeholder="*Votre prenom :">
            <br>
            <select required name="cycle" id="cycle">
                <option value="">*Choisissez une formation</option>
                <option value="Cycle 1">Cycle 1</option>
                <option value="Cycle 2">Cycle 2</option>
                <option value="Cycle 3">Cycle 3</option>
            </select>
            <br>
            <input required type="email" name="email" id="email" placeholder="*Votre email :">
            <br>
            <input required type="text" name="tel" id="tel" placeholder="*Numero tel :">
            <br>
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
        <button id="btn-send">Envoyer</button>
    </form>
</body>
</html>
