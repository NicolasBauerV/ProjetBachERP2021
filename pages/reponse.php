<?php
    require 'connexion_déconnexion/bdd_connexion.php';
    require './emails/mail_reponse.php';

// Début
    session_start();
    $idUser = null;
    //Si le client ne s'est pas connecté on renvoie à la page de connexion
    if (empty($_SESSION['username'])) {
        header('location: ../pages/connexion.php');
        exit();
    }

    if (isset($_GET['nbIdUser'])) {
        setcookie("idUser", $_GET['nbIdUser'], time() + 24 * 60 * 60, null, null, false, true);
    }
    $idUser = $_COOKIE['idUser'];
    $nom = null;
    $prenom = null;
    $msg = null;
    $email = null;
    
    $request = $bdd->prepare('SELECT nom FROM renseignements WHERE id = ?');
    $request->execute(array($idUser));
    
    $nom = $request->fetchColumn(0);

    $request = $bdd->prepare('SELECT prenom FROM renseignements WHERE id = ?');
    $request->execute(array($idUser));
    
    $prenom = $request->fetchColumn(0);

    $request = $bdd->prepare('SELECT msg FROM renseignements WHERE id = ?');
    $request->execute(array($idUser));

    $msg = $request->fetchColumn(0);

    $request = $bdd->prepare('SELECT email FROM renseignements WHERE id = ?');
    $request->execute(array($idUser));

    $email = $request->fetchColumn(0);


    if (!empty($_POST['subject']) && !empty($_POST['area_answer'])) {
        $subject = $_POST['subject'];
        $answer  = $_POST['area_answer'];
        
        sendMail($email, $nom, $prenom, $subject, $answer);
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
        <div id="child">
            <h2>Message de : <span style="color: #ca5b00;"><?php echo $nom.' '.$prenom?></span></h2>
            <p>
                <?php echo $msg ?>
            </p>
            
            <button id="retour" type="button" onclick="location.href = './demande_renseignement.php'">Retour</button>
        </div>

        <div id="main">
            <h2>Réponse au message de : <span style="color: #ca5b00;"> <?php echo $nom.' '.$prenom?></span></h2>
            <form action="./reponse.php" method="post">
                <label for="subject">
                    <input type="text" name="subject" id="subject" placeholder="*Saisissez un sujet" required>
                </label>
                <br>
                <textarea name="area_answer" id="area_answer" cols="50" rows="10" placeholder="*Insérer votre message..." required></textarea>
                <button id="send" type="submit">Envoyer</button>
            </form>
            <button id="otherans" type="submit" onclick="location.href='./reponse_perso.php'">Répondre avec un message personnaliser</button>
            
            <button id="retour" type="button" onclick="location.href = './demande_renseignement.php'">Retour</button>
        </div>
    </div>

    <script text="text/javascript">
        const idMsg = '<?php $_GET['nbIdMsg'] ?>';
        document.getElementById('area_answer').value = idMsg;
    </script>
</body>
</html>
