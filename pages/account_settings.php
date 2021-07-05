<?php
ob_start();
    require 'connexion_déconnexion/bdd_connexion.php';
    session_start();

    if ((!empty($_POST['nom']) && !empty($_POST['old_password']) && !empty($_POST['password']) &&
        !empty($_POST['conf_password'])) || (!empty($_FILES['pdp']) && !empty($_FILES['pdp']['error']) == 0)) {
        $new_username = htmlspecialchars($_POST['nom']);
        $old_password = htmlspecialchars($_POST['old_password']);
        $new_password = htmlspecialchars($_POST['password']);
        $conf_password = htmlspecialchars($_POST['password']);

        // Check mot de passe et sa confirmation
        if ($new_password != $conf_password) {
            header('location: ../pages/account_settings.php?error=1&pass=1');
            exit();
        }

        // Code crypté de l'ancien mot de passe
        $old_password = "3rji@&4".sha1($old_password."na5@h,43&@d");

        // Vérification du password if not exist
        $req = $bdd->prepare('SELECT count(*) as numPass FROM users WHERE password = ?');
        $req->execute(array($old_password));
        while ($data = $req->fetch()) {
            if ($data['numPass'] == 0) {
                header('location: ../pages/account_settings.php?error=1&message=Mot de passe incorrect');
                exit();
            }
        }

        // Vérification du username
        $req = $bdd->prepare('UPDATE users SET username = ? WHERE username = ?');
        $req->execute(array($new_username, $_SESSION['username']));
        $_SESSION['username'] = $new_username;

        // Cryptage new Password
        $new_password = "3rji@&4".sha1($new_password."na5@h,43&@d");

        // Si le mot de passe est correct
        $req = $bdd->prepare('UPDATE users SET password = ? WHERE password = ?');
        $req->execute(array($new_password, $old_password));
    }
ob_end_flush();

?>
<!Doctype html>
<html>
<head>
    <title>Account settings</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../src/style/account_settings.css?t=<? echo time(); ?>"/>
    <link rel="stylesheet" href="../src/style/btn-quit.css">
</head>
<body>
    <div id="main">
        <h1>Information compte utilisateur : <?php echo '<span style="color: green;">'.$_SESSION['username'].'</span>';?></h1>
        <form method="post" action="account_settings.php" enctype="multipart/form-data">
            <!-- Nom d'utilisateur -->
            <label for="nom">Modifier votre nom: </label>
            <input type="text" name="nom" placeholder="Votre nom" required>

            <!-- Mot de passe -->
            <label for="old_password">Ancien mot de passe: </label>
            <input type="password" name="old_password" placeholder="Ancien mot de passe" required>

            <label for="password">Modifier votre mot de passe: </label>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <label for="conf_password">Confirmer votre mot de passe: </label>
            <input type="password" name="conf_password" placeholder="Confirmation" required>

            <!-- Envoie -->
            <button type="submit" id="btn-sub">Enregistrer</button>
        </form>
        <button onclick="location.href = 'accueil.php'" id="btn-quit">Quitter</button>
    </div>
</body>
</html>
