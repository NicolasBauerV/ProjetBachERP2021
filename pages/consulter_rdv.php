<?php
    require 'connexion_déconnexion/bdd_connexion.php';
    require './emails/rdv_modif.php';
// Début
    session_start();

    $btnActivate = null;
    //Si le client ne s'est pas connecté on renvoie à la page de connexion
    if (empty($_SESSION['username'])) {
        header('location: ../pages/connexion.php');
        exit();
    }

    $request = $bdd->prepare('SELECT COUNT(*) as nbRdv FROM prise_rdv');
    $request->execute();
    while ($donnee = $request->fetch()) {
        if ($donnee['nbRdv'] == 0) {
            header("location: ./accueil.php");
            exit();
        }
    }

    if (isset($_GET['idRdv'])) {
        $rdvIdToDel = $_GET['idRdv'];
        $request = $bdd->prepare("DELETE FROM prise_rdv WHERE id = ?");
        $request->execute(array($rdvIdToDel));
        header("location: ./consulter_rdv.php");
    }

    //Modification rdv
    if (!empty($_POST['newRdvDate']) && !empty($_POST['newRdvDate']) && !empty($_POST['motif'])) {
        
        // Récupération id du rdv
        $id = $_POST['nbrdvId'];
        
        // Récupération du motif
        $motif = $_POST['motif'];
        
        //Sélectionne les infos d'une ligne
        $request = $bdd->prepare('SELECT * FROM prise_rdv WHERE id = ?');
        $request->execute(array($id));
        while ($donnee = $request->fetch()) {
            $nom = $donnee['nom'];
            $prenom = $donnee['prenom'];
            $email = $donnee['email'];
        }

        // Récupération infos date et time
        $newRdvDate = $_POST['newRdvDate'];
        $newRdvTime = $_POST['newRdvTime'];
        
        // Envoie à la BDD et envoie d'email
        $sended = sendMailRdv($email, $nom, $prenom, $motif, $newRdvDate, $newRdvTime);
        if ($sended) {
            $request = $bdd->prepare("UPDATE prise_rdv SET date = ?, temps = ? WHERE id = ?");
            $request->execute(array($newRdvDate, $newRdvTime, $id));
            header('location: ./consulter_rdv.php?mod=1');
            exit();
        }
    }

    $request = $bdd->prepare('SELECT * FROM prise_rdv');
    $request->execute();
?>

<!Doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ludus-ERP : Rendez-vous</title>
    <link rel="stylesheet" href="../src/style/rdv.css">
</head>
<body>
    <header>
        <h2 style="margin-left: .4em;"><?php echo '<span style="color: green;">'.$_SESSION['username'].'</span>';?></h2>
        <ul>
            <li><a href="account_settings.php">Mon compte</a></li>
        </ul>
    </header>
    <div id="container">
        <div id="child1">
            <h2 style="text-align:center;">Liste des rendez-vous</h2>
            <?php if (isset($_GET['mod'])) {
                echo '<h3 class="success">Rendez-vous modifié avec succès !</h3>';
            }?>
            <div id="tableDiv">
                <table border class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Temps</th>
                            <th>Modifier</th>
                            <th>Supprimer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rdvDate_array = array();
                        $rdvTime_array = array();
                        $i = 0;
                        while ($donnees = $request->fetch()) {
                            echo " 
                            <tr>
                                <td><span id=\"rdvId".$i."\"/>".$donnees['id']."</span></td>
                                <td>".$donnees['nom']."</td>
                                <td>".$donnees['prenom']."</td>
                                <td>".$donnees['email']."</td>
                                <td>".$donnees['date']."</td>
                                <td>".$donnees['temps']."</td>
                                <td><button class=\"modifier\" id=\"btn_modif".$i."\">Modifier</button></td>
                                <td>
                                    <form action=\"./consulter_rdv.php\" method=\"GET\">
                                        <input type=\"number\" name=\"idRdv\" style=\"display: none;\" value=\"".intval($donnees['id'])."\"/>
                                        <button type=\"submit\" class=\"supr\">Supprimer</button>
                                    </form>
                                </td>
                            </tr>";
                            array_push($rdvDate_array, $donnees['date']);
                            array_push($rdvTime_array, $donnees['temps']);
                            $i += 1;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="child3">
            <header class="titre">
                <h2>Modifer le rendez-vous</h2>
            </header>
            <form method="POST" action="./consulter_rdv.php">
                <h3>Ancien rendez-vous :</h3>
                <?php
                    $j = 0;
                    while ($j < $i) {
                        echo "<pre class=\"par\" id=\"p2".$j."\"><span style=\"color: blue;\">Le: ".$rdvDate_array[$j]."</span> \n\n<span style=\"color: green;\">À: ".$rdvTime_array[$j]."</span></pre>";
                        $j+=1;
                    }
                ?>
                <h3>Nouveau rendez-vous :</h3>
                <label for="motif"> Motif: 
                    <input type="text" name="motif" id="motif" placeholder="Saisisssez un motif">
                </label>
                <label for="newRdvDate"> Date:
                    <input type="date" name="newRdvDate" id="newRdvDate">
                </label>

                <label for="newRdvTime"> Heures : minutes
                    <input type="time" name="newRdvTime" id="newRdvTime">
                </label>
                <input type="number" name="nbrdvId" id="nbrdvId" style="display: none;"/>
                <br>
                <button type="submit" class="modifier">Modifier et envoyer</button>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        //Seconde fenetre une fois l'autre ouverte
        let counterArea2 = 1;
        let nbDonnee = "<?php Print($i) ?>";

        //Bouton "modifier"
        let counterArea3 = 1;
        const tabidRdv = new Array();
        const tabIdBtnMod = new Array();
        const tabButtonsMod = new Array();  

        //TRAITEMENT fenêtre modification
        modifierMsgListe();

        function modifierMsgListe() {
            
            //Récolte des boutons
            for (let i = 0; i < nbDonnee; i++) {
                tabidRdv.push(document.querySelector(`#rdvId${i}`)); //Ajout de l'id des messages
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
                        document.getElementById("nbrdvId").value = tabidRdv[i].innerHTML;
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

        let today = new Date();
        let dd = today.getDate();
        let mm = today.getMonth()+1; //January is 0!
        let yyyy = today.getFullYear();
        if(dd<10){
                dd='0'+dd
            } 
            if(mm<10){
                mm='0'+mm
            } 

        today = yyyy+'-'+mm+'-'+dd;
        document.getElementById("newRdvDate").setAttribute("min", today);
    </script>
</body>
</html>