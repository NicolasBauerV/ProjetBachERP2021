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
    <title>Ludus-ERP : Demande Renseignement</title>
    <link type="text/css" rel="stylesheet" href="../src/style/liste_renseignements.css?t=<? echo time(); ?>"/>
    <link rel="stylesheet" href="../src/style/reponse.css">
</head>
<body>
    <header>
        <h2 style="margin-left: .4em"><?php echo $_SESSION['username'];?></h2>
        <ul>
            <li><a href="account_settings.php">Mon compte</a></li>
        </ul>
    </header>

    <div id="container">
        <div id="main">
            <h2>Réponse au message de : </h2>
            <form action="./reponse.php" method="post">
                <textarea name="area_answer" id="area_answer" cols="50" rows="10" placeholder="Insérer votre message..."></textarea>

                <button id="send" type="submit">Envoyer</button>
            </form>
            
            <button id="retour" type="button" onclick="location.href = './demande_renseignement.php'">Retour</button>
        </div>
    </div>
    <script type="text/javascript">
    </script>
</body>
</html>
