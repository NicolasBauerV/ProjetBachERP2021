<?php
    require 'connexion_déconnexion/bdd_connexion.php';

// Début
    session_start();
    //Si le client ne s'est pas connecté on renvoie à la page de connexion
    if (empty($_SESSION['username'])) {
        header('location: ../pages/connexion.php');
        exit();
    }

    $request = $bdd->prepare('SELECT * from renseignements');
    $request->execute();
?>

<!Doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ludus-ERP : Demande Renseignement</title>
    <link type="text/css" rel="stylesheet" href="../src/style/liste_renseignements.css?t=<? echo time(); ?>"/>
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
            <h2>Liste des demandes de renseignement</h2>
            <div id="tableDiv">
                <table border class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Formations</th>
                            <th>N°tel</th>
                            <th>Newsletters</th>
                            <th>Message</th>
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
                                <td>".$donnees['id']."</td>
                                <td>".$donnees['nom']."</td>
                                <td>".$donnees['prenom']."</td>
                                <td>".$donnees['email']."</td>
                                <td>".$donnees['formations']."</td>
                                <td>".$donnees['tel']."</td>
                                <td>".$donnees['newletters']."</td>
                                <td><button class=\"msgView\" id=\"btn_msg".$i."\" onclick=\"afficheMsg();\">Voir message</button></td>
                            </tr>";
                            array_push($id_array, $donnees['id']);
                            array_push($msg_array, $donnees['msg']);
                            $i += 1;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <button id="retour" type="button" onclick="location.href = 'accueil.php'">Retour</button>
        </div>
        <div id="child">
            <h2 style="text-align: center;">Message</h2>
            <form method="POST" action="reponse.php">
                <?php
                    $j = 0;
                    while ($j < $i) {
                        echo "<p id=\"p".$j."\">".$msg_array[$j]."</p>";
                        $j+=1;
                    }
                ?>
                <button id="answer" type="submit" onclick="location.href = './reponse.php'">Répondre</button>
            </form>

        </div>
    </div>
    <script type="text/javascript">

        let nbMsg = "<?php Print($i) ?>"; //Récupère la variable $i php
        let tabId = new Array();

        let isBtnActive = false;
        let counter = 0;

        //Tableau contenant les différents boutons
        let arrBtn = new Array();

        //Récolte des boutons du tableau
        for (let j = 0; j < nbMsg; j++) {
            arrBtn.push(document.getElementById(`btn_msg${j}`));
            tabId.push(j + 1);
        }

        for (let i = 0; i < tabId.length; i++) {
            let nbBtn = tabId.length;
            arrBtn[i].onclick = function() {
                isBtnActive = true;

                for (let j = 0; j < tabId.length; j++) {
                    arrBtn[j].style.backgroundColor = "#a6a6a6"; //Affecte tout les boutons
                    arrBtn[j].disabled = true; // affecte tout les boutons

                    if(isBtnActive) {
                        arrBtn[i].disabled = false; // Bouton courant
                    }
                }
                counter++;
                
                // On ouvre la zone d'affichage des messages
                if (tabId[i] === i + 1 && counter % 2 === 1) {
                    document.getElementById(`btn_msg${i}`).style.backgroundColor = '#ff3300'; // Bouton devient rouge
                    // Fait apparaître la zone de message
                    document.getElementById(`p${i}`).style.display = "block";
                    document.querySelector('#container').style.justifyContent = "space-around";
                    document.querySelector('#child').style.display = "block";
                } else { // Fermeture
                    isBtnActive = false;

                    for (let j = 0; j < tabId.length; j++) {
                        arrBtn[j].style.backgroundColor = "#5f14ff";
                        arrBtn[j].disabled = false;
                    }
                    
                    //Effacement de la seconde fenêtre et remise en place de la 1ere fenêtre et les couleurs des boutons
                    document.getElementById(`btn_msg${i}`).style.backgroundColor = '#5f14ff';
                    document.getElementById(`p${i}`).style.display = "none";
                    document.querySelector('#container').style.justifyContent = "center";
                    document.querySelector('#child').style.display = "none";
                }

                if (counter === 2 ) {
                    counter = 0;
                }
            }

            arrBtn[i].onmouseenter = function () {
                if (!isBtnActive) {
                    arrBtn[i].style.backgroundColor = "#007bff";
                }
            }

            arrBtn[i].onmouseout = function () {
                if (!isBtnActive) {
                    arrBtn[i].style.backgroundColor = "#5f14ff";
                }
            }
        }
    </script>
</body>
</html>
