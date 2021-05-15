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

    if (isset($_GET['idMsg'])) {
        $msgIdToDel = $_GET['idMsg'];
        $request = $bdd->prepare("DELETE FROM message_perso WHERE id = ?");
        $request->execute(array($msgIdToDel));
        header("location: ./template.php");
    }

    if (!empty($_POST['newMsg'])) {
        $id = $_POST['nbMsgId'];
        $newMsg = $_POST['newMsg'];
        $request = $bdd->prepare("UPDATE message_perso SET message = ? WHERE id = ?");
        $request->execute(array($newMsg, $id));
        header('location: ./template.php?mod=1');
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
                    echo '<h2>Créer votre template :</h2>';
                    if (isset($_GET['mod'])) {
                        echo '<h3 class="success">Modification apportés !</h3>';
                    }
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
            <button id="retour" type="button" onclick="location.href = 'accueil.php'">Retour</button>
        </div>

        <div id="child1">
            <h2 style="text-align:center;">Liste des messages</h2>
            <div id="tableDiv">
                <table border class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Message</th>
                            <th>Date de création</th>
                            <th>Modifier</th>
                            <th>Supprimer</th>
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
                                <td><span id=\"msgId".$i."\"/>".$donnees['id']."</span></td>
                                <td><button id=\"btn_msg".$i."\">Voir message</button></td>
                                <td>".$donnees['date_creation']."</td>
                                <td><button class=\"modifier\" id=\"btn_modif".$i."\">Modifier</button></td>
                                <td>
                                    <form action=\"./template.php\" method=\"GET\">
                                        <input type=\"number\" name=\"idMsg\" style=\"display: none;\" value=\"".intval($donnees['id'])."\"/>
                                        <button type=\"submit\" class=\"supr\">Supprimer</button>
                                    </form>
                                </td>
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
        <div id="child2">
            <header class="titre">
                <h2>Message</h2>
            </header>
            <?php
                $j = 0;
                while ($j < $i) {
                    echo "<pre id=\"p".$j."\">".$msg_array[$j]."</pre>";
                    $j+=1;
                }
            ?>
        </div>
        <div id="child3">
            <header class="titre">
                <h2>Modifer le message</h2>
            </header>
            <form method="POST" action="./template.php">
                <h3>Ancien message :</h3>
                <?php
                    $j = 0;
                    while ($j < $i) {
                        echo "<pre class=\"par\" id=\"p2".$j."\">".$msg_array[$j]."</pre>";
                        $j+=1;
                    }
                ?>
                <h3>Nouveau message :</h3>
                <textarea name="newMsg" id="newMsg" cols="30" rows="10" placeholder="Entrez nouveau message"></textarea>
                <input type="number" name="nbMsgId" id="nbMsgId" style="display: none;"/>
                <br>
                <button type="submit" class="modifier">Modifier et envoyer</button>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        //Premiere fenetre à ouvrir
        const btn = document.querySelector('#disabled');
        const btnDel = document.querySelector(".supr");
        btn.disabled = true;
        let isBtnActivate = '<?php Print($btnActivate) ?>';
        let counterArea1 = 1;

        //Seconde fenetre une fois l'autre ouverte
        let counterArea2 = 1;
        let nbDonnee = "<?php Print($i) ?>";

        // Bouton "voir message"
        const tabIdBtn = new Array();
        const tabButtons = new Array();

        //Bouton "modifier"
        let counterArea3 = 1;
        const tabIdMsg = new Array();
        const tabIdBtnMod = new Array();
        const tabButtonsMod = new Array();
        
        // Si le bouton est activé alors on affiche la première fenêtre
        if (isBtnActivate) {
            btn.disabled = false;

            //Style pour le bouton
            btn.style.backgroundColor = "#5f14ff";
            btn.onmouseenter = function() {
                btn.style.boxShadow = "4px 3px 1px #014ca9";
                btn.style.backgroundColor = "#007bff";
                btn.style.color = "white";
            }
            //Style pour le bouton
            btn.onmouseout = function() {
                btn.style.boxShadow = "none";
                btn.style.backgroundColor = "#5f14ff";
                btn.style.color = "white";
            }

            //Affichage première fenêtre au click
            btn.addEventListener("click", () => {
                if (counterArea1 % 2 === 1) {
                    if (document.querySelector('#child1').style.display != "block") {
                        document.querySelector('#child1').style.display = "block";
                    }
                } else { // Suppression de la première et seconde fenêtre
                    document.querySelector('#child1').style.display = "none";
                    document.querySelector('#container').style.justifyContent = "space-around";
                    document.querySelector('#child2').style.display = "none";

                }
                counterArea1++;
            });
        } else {
            alert('Vous n\'avez pas de message pour le moment');
        }

        if (counterArea1 === 2) {
            counterArea1 = 1;
        }

        //Style pour le bouton
        btn.onmousedown = function() {
            btn.style.boxShadow = "0 0 6px 2px #000d7a inset";
            btn.style.backgroundColor = "#007bff";
            btn.style.color = "#cccccc";
        }

        //Style pour le bouton
        btn.onmouseup = function() {
            btn.style.boxShadow = "none";
            btn.style.backgroundColor = "#5f14ff";
            btn.style.color = "white";
        }
        
        //TRAITEMENT Fenêtre liste des boutons
        messageListe();

        function messageListe() {
            //Récolte des boutons
            for (let i = 0; i < nbDonnee; i++) {
                tabButtons.push(document.querySelector(`#btn_msg${i}`));
                tabIdBtn.push(i + 1);
            }
            //Evenement lors d'appuis sur un bouton d'une ligne
            for (let i = 0; i < tabIdBtn.length; i++) {
                tabButtons[i].addEventListener("click", function() {
                    isBtnActive = true;
                    //Analyse des boutons à desactiver lors de l'appuis sur un des boutons
                    for (let j = 0; j < tabIdBtn.length; j++) {
                        tabButtons[j].style.backgroundColor = "#a6a6a6"; //Affecte tout les boutons
                        tabButtons[j].disabled = true; // affecte tout les boutons
                        if(isBtnActive) {
                            tabButtons[i].disabled = false; // Bouton courant toujours activé
                        }
                    }
                    // On ouvre la zone d'affichage des messages
                    if (tabIdBtn[i] === i + 1 && counterArea2 % 2 === 1) {
                        document.querySelector(`#btn_msg${i}`).style.backgroundColor = '#ff3300'; // Bouton devient rouge
                        // Fait apparaître la zone de message
                        document.querySelector(`#p${i}`).style.display = "block";
                        document.querySelector('#container').style.justifyContent = "space-around";
                        document.querySelector('#child2').style.display = "block";
                    } else { // Fermeture
                        isBtnActive = false;
                        for (let j = 0; j < tabIdBtn.length; j++) {
                            tabButtons[j].style.backgroundColor = "#5f14ff";
                            tabButtons[j].disabled = false;
                        }
                        //Effacement de la seconde fenêtre et remise en place de la 1ere fenêtre et les couleurs des boutons
                        document.querySelector(`#btn_msg${i}`).style.backgroundColor = '#5f14ff';
                        document.querySelector(`#p${i}`).style.display = "none";
                        document.querySelector('#child2').style.display = "none";
                    }
                    counterArea2++;
                });
            }
        }

        //TRAITEMENT fenêtre modification
        modifierMsgListe();

        function modifierMsgListe() {
            
            //Récolte des boutons
            for (let i = 0; i < nbDonnee; i++) {
                tabIdMsg.push(document.querySelector(`#msgId${i}`)); //Ajout de l'id des messages
                tabButtonsMod.push(document.querySelector(`#btn_modif${i}`));
                tabIdBtnMod.push(i + 1);
            }
            //Evenement lors d'appuis sur un bouton d'une ligne
            for (let i = 0; i < tabIdBtnMod.length; i++) {
                tabButtonsMod[i].addEventListener("click", function() {
                    isBtnActive = true;
                    //Analyse des boutons à desactiver lors de l'appuis sur un des boutons
                    for (let j = 0; j < tabIdBtnMod.length; j++) {
                        tabButtonsMod[j].style.backgroundColor = "#a6a6a6"; //Affecte tout les boutons
                        tabButtonsMod[j].disabled = true; // affecte tout les boutons
                        if(isBtnActive) {
                            tabButtonsMod[i].disabled = false; // Bouton courant toujours activé
                        }
                    }
                    // On ouvre la zone d'affichage des messages
                    if (tabIdBtnMod[i] === i + 1 && counterArea3 % 2 === 1) {
                        document.querySelector(`#btn_modif${i}`).style.backgroundColor = '#ff3300'; // Bouton devient rouge
                        // Fait apparaître la zone de message
                        document.querySelector(`#p2${i}`).style.display = "block";
                        document.querySelector('#container').style.justifyContent = "space-around";
                        document.querySelector('#child3').style.display = "block";
                        document.getElementById("nbMsgId").value = tabIdMsg[i].innerHTML;
                    } else { // Fermeture
                        isBtnActive = false;
                        for (let j = 0; j < tabIdBtnMod.length; j++) {
                            tabButtonsMod[j].style.backgroundColor = "#ecae01";
                            tabButtonsMod[j].disabled = false;
                        }
                        //Effacement de la seconde fenêtre et remise en place de la 1ere fenêtre et les couleurs des boutons
                        document.querySelector(`#btn_modif${i}`).style.backgroundColor = '#ecae01';
                        document.querySelector(`#p2${i}`).style.display = "none";
                        document.querySelector('#child3').style.display = "none";
                    }
                    counterArea3++;
                });
            }
        }
    </script>
</body>
</html>