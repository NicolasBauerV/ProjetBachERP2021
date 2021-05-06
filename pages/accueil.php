<?php
    require 'connexion_déconnexion/bdd_connexion.php';

// Début
    session_start();
    //Si le client ne s'est pas connecté on renvoie à la page de connexion
    if (empty($_SESSION['username'])) {
        header('location: ../pages/connexion.php');
        exit();
    }
?>

<!Doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ludus-ERP : Accueil</title>
    <link type="text/css" rel="stylesheet" href="../src/style/accueil.css?t=<? echo time(); ?>"/>
</head>
<body>
    <header>
        <h2 style="margin-left: .4em;">Bienvenue  <?php echo '<span style="color: green;">'.$_SESSION['username'].'</span>';?></h2>
        <ul>
            <li><a href="account_settings.php">Mon compte</a></li>
        </ul>
    </header>

    <div id="main">
        <button type="button" onclick="location.href = 'demande_renseignement.php'">Demandes de renseignement</button>

        <button disabled type="button" onclick="location.href = 'dossier_candidature.php'">Dossier candidature</button>
        
        <br>
        <br>
        <button type="button" onclick="location.href = 'connexion_déconnexion/deconnexion.php'">Se déconnecter</button>
    </div>
</body>
</html>