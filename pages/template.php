<?php
    require 'connexion_déconnexion/bdd_connexion.php';

// Début
    session_start();

    $btnActivate = null;
    //Si le client ne s'est pas connecté on renvoie à la page de connexion
    if (empty($_SESSION['username'])) {
        header('location: ../pages/connexion.php');
        exit();
    }

    //Enregistrement de message
    if (!empty($_POST['message'])) {
        $message = $_POST['message'];
        $request = $bdd->prepare('INSERT INTO message_perso (message) VALUES (?)');
        $request->execute(array($message));
        header('location: ./template.php?success=1');
        die();
    }

    //Compter si il existe des messages
    $request = $bdd->prepare('SELECT COUNT(message) as nbMessage FROM message_perso');
    $request->execute();
    while ($donnee = $request->fetch()) {
        if ($donnee['nbMessage'] != 0) {
            $btnActivate = true;
        }
    }

    $request = $bdd->prepare('SELECT * FROM message_perso');
    $request->execute();


?>

<!Doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ludus-ERP : Template</title>
    <link rel="stylesheet" href="../src/style/template.css">
</head>
<body>
    <header>
        <h2 style="margin-left: .4em;"><?php echo '<span style="color: green;">'.$_SESSION['username'].'</span>';?></h2>
        <ul>
            <li><a href="account_settings.php">Mon compte</a></li>
        </ul>
    </header>
    <div id="container">
        <div id="main">
            <?php
                if (!isset($_GET['success'])) {
                    echo '<h2>Ici vous pouvez créer vos template email, pour les réutiliser lors des réponses</h2>';
                } else {
                    echo '<h3 class="success">Enregistrement réussi !</h3>';
                }
            ?>
            <form action="./template.php" method="post">
                <textarea name="message" id="message" cols="80" rows="20" placeholder="Insérer votre texte ici..."></textarea>
                <br>
                <button type="submit">Enregistrer</button>
            </form>
            <button id="disabled">Voir messages</button>
        </div>

        <div id="child">
            <div id="tableDiv">
                <table border class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Message</th>
                            <th>Date de crétaion</th>
                            <th>Bouton</th>
                            <th>Bouton</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $msg_array = array();
                        $id_array = array();
                        $i = 0;
                        while ($donnees = $request->fetch()) {
                            echo " 
                            <tr>
                                <td><span id=\"idUser".$i."\"/>".$donnees['id']."</span></td>
                                <td><button id=\"btn_msg".$i."\">Voir message</button></td>
                                <td>".$donnees['date_creation']."</td>
                                <td><button class=\"modifier\" id=\"btn_modif".$i."\">Modifier</button></td>
                                <td><button class=\"supr\" id=\"btn_supr".$i."\">Supprimer</button></td>
                            </tr>";
                            array_push($id_array, $donnees['id']);
                            array_push($msg_array, $donnees['message']);
                            $i += 1;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        let btn = document.querySelector('#disabled');
        btn.disabled = true;
        let isBtnActivate = '<?php Print($btnActivate) ?>';
        if (isBtnActivate) {
            btn.disabled = false;
            btn.style.backgroundColor = "#5f14ff";
        } else {
            alert('Vous n\'avez pas de message pour le moment');
        }

        btn.addEventListener("click", () => {
            
        });

        btn.onmousedown = function() {
            btn.style.boxShadow = "0 0 6px 2px #000d7a inset";
            btn.style.backgroundColor = "#007bff";
        }

        btn.onmouseenter = function() {
            btn.style.boxShadow = "4px 3px 1px #014ca9";
            btn.style.backgroundColor = "#007bff";
        }

        btn.onmouseout = function() {
            btn.style.boxShadow = "none";
            btn.style.backgroundColor = "#5f14ff";
        }
    </script>
</body>
</html>