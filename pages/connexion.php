<?php
    require 'connexion_déconnexion/bdd_connexion.php';
    // Démarrage de la session
    session_start();

    if (!empty($_POST['username']) && !empty($_POST['PassWord'])) {
        $username = $_POST['username'];
        $password = $_POST['PassWord'];

        // Encodage password
        $req = $bdd->prepare('SELECT password FROM users WHERE username = ?');
        $req->execute(array($username));
        $password = $req->fetchColumn();

        // Selectionner le nom d'utilisateur
        $request = $bdd -> prepare('SELECT * FROM users WHERE username = ?');
        $request -> execute(array($username));

        // Récupération des données
        while ($user = $request->fetch()){
            // Connexion session
            if ($password === $user['password']) {
                $_SESSION['connect'] = 1;
                $_SESSION['username'] = $user['username'];

                header('location: ../pages/connexion.php?success=1');
                exit();
            }
        }

        header('location: ../pages/connexion.php?error=1');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ludus-ERP : Connexion</title>
    <script src="../src/scripts/main.js" defer></script>
    <link rel="stylesheet" href="../src/style/connexion_inscription.css?t=<? echo time(); ?>">
</head>
<body>
    <div id="main">
        <h1>Se connecter</h1>
        <?php
            if (isset($_GET['error'])) {
                echo '<p class="error">Attention le nom d\'utilisateur ou le mot de passe est incorrect</p>';
            } elseif (isset($_GET['success'])) {
                echo '<p class="success">Connexion réussi</p>';
                header('location: ../pages/accueil.php');
                exit();
            }
        ?>
        <form method="post" action="connexion.php">
            <label for="username"></label>
            <input type="text" id="username" name="username" placeholder="Nom d'utilisateur">
            <label for="PassWord"></label>
            <input type="password" id="PassWord" name="PassWord" placeholder="Mot de passe">
            <button type="submit">Connexion</button>
        </form>
    </div>

</body>
</html>