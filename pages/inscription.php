<?php
    require 'connexion_déconnexion/bdd_connexion.php';

    if (!empty($_POST['username']) && !empty($_POST['PassWord']) && !empty($_POST['PassWordConf'])) {
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['PassWord']);
        $pass_conf = htmlspecialchars($_POST['PassWordConf']);

        // if password is not the same
        if ($password != $pass_conf) {
            header('location: ../pages/inscription.php?error=1&pass=1');
            exit();
        }

        // Si le nom utilisateur existe déjà
        $request = $bdd -> prepare('SELECT count(*) as numUsername FROM users WHERE username = ?');
        $request -> execute(array($username)); //Lancement de la requête
        while ($username_check = $request->fetch()) {
            if ($username_check['numUsername'] != 0) {
                header('location: ../pages/inscription.php?error=1&user=1');
                exit();
            }
        }

        //HASH password
        $password = "3rji@&4".sha1($password."na5@h,43&@d");

        //Envoie de requête
        $request = $bdd ->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $request -> execute(array($username, $password));
        header('location: ../pages/inscription.php?success=1');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ludus_ERP : Inscription</title>
        <link rel="stylesheet" href="../src/style/connexion_inscription.css">
    </head>
<body>
    <div id="main">
        <h1>Inscrivez-vous</h1>
        <?php
            if (isset($_GET['error'])) {
                if (isset($_GET['pass'])) {
                    echo '<p class="error">Attention le mot de passe n\'est pas identique</p>';
                }
            }
            if (isset($_GET['user']) && isset($_GET['error'])) {
                echo '<p class="error">Attention le nom d\'utilisateur existe déjà</p>';
            } elseif (isset($_GET['success'])) {
                echo '<p class="success">Vous êtes inscrit.e</p>';
            }
        ?>
        <form method="post" action="inscription.php">
            <label for="username"></label>
            <input type="text" id="username" name="username" placeholder="*Nom d'utilisateur" required>
            <label for="PassWord"></label>
            <input type="password" id="PassWord" name="PassWord" placeholder="*Mot de passe" required>
            <label for="PassWordConf"></label>
            <input type="password" id="PassWordConf" name="PassWordConf" placeholder="*Confirmer votre mot de passe" required>
            <button type="submit" id="btn-sub">S'inscrire</button>
        </form>
        <p>Vous avez déjà un compte ? Connectez-vous <a href="connexion.php">ici</a></p>
    </div>
    </body>
</html>