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
        header('location: ../pages/reponse.php');
    }
    $idUser = $_COOKIE['idUser'];

    $nom = null;
    $prenom = null;
    $date = null;
    $time = null;
    $msg = null;
    $email = null;
    $msgAns = null;
    
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

    if (!empty($_POST['subject'])) {
        if ($_POST['subject'] == "rdv" && !empty($_POST['dateRdv']) && !empty($_POST['heureRdv'] && !empty($_POST['area_answer']))) {
            $subject = "Rendez-vous";
            $answer  = $_POST['area_answer'];
            $date = $_POST['dateRdv'];
            $time = $_POST['heureRdv'];

            $sended = sendMailRdv($email, $nom, $prenom, $subject, $answer, $date, $time);
            if ($sended) {
                $request = $bdd->prepare("INSERT INTO prise_rdv(nom, prenom, email, date, temps) VALUES (?, ?, ?, ?, ?)");
                $request->execute(array($nom, $prenom, $email, $date, $time));
                header("location: ./reponse.php?success=1");
                exit();
            }
        }
    }


    if (!empty($_POST['subject']) && !empty($_POST['area_answer'])) {
        if ($_POST['subject'] == "reponse") {
            $subject = "Demande de renseignement";
            $answer  = $_POST['area_answer'];
            $sended = sendMail($email, $nom, $prenom, $subject, $answer);
            if ($sended) {
                header("location: ./reponse.php?success=1");
                exit();
            }
        }
    }

    if (isset($_GET['nbIdMsg'])) {
        $idMsgAnsw = $_GET['nbIdMsg'];
        $request = $bdd->prepare("SELECT message FROM message_perso WHERE id = ?");
        $request->execute(array($idMsgAnsw));
        $msgAns = $request->fetchColumn(0);
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
            <section id="rappelMsg">
                <p>
                    <?php echo $msg ?>
                </p>
            </section>
            <button id="retour" type="button" onclick="location.href = './demande_renseignement.php'">Retour</button>
        </div>

        <div id="main">
            <h2>Réponse au message de : <span style="color: #ca5b00;"> <?php echo $nom.' '.$prenom?></span></h2>
            <?php 
                if (isset($_GET['success'])) {
                    echo '<span class="success">Email envoyé</span>';
                }

                if (isset($_GET['err'])) {
                    if (isset($_GET['nbMsg'])) {
                        echo '<span class="error">Aucun template disponible.</span>';
                    }
                }
            ?>
            <form id="form" action="./reponse.php" method="post">
                <select required name="subject" id="subject">
                    <option value="">*Choisissez un sujet</option>
                    <option value="reponse">Réponse</option>
                    <option value="rdv">Rendez-vous</option>
                </select>
                <br>
                <section id="reponseSection">
                    <textarea name="area_answer" id="area_answer" cols="50" rows="10" placeholder="*Insérer votre message..." required><?php echo $msgAns ?></textarea>
                </section>
                <section id="rendezvous">
                    <label for="dateRdv">
                        <span>Date :</span>
                        <input type="date" name="dateRdv" id="dateRdv">
                    </label>
                    <label for="heureRdv">
                        <span>Heures : minutes</span>
                        <input type="time" name="heureRdv" id="heureRdv">
                    </label>
                </section>
                <button type="submit" id="send">Envoyer</button>
            </form>
            <button id="otherans" type="submit" onclick="location.href='./reponse_perso.php'">Répondre avec un message personnaliser</button>
            
            <button id="retour" type="button" onclick="location.href = './demande_renseignement.php'">Retour</button>
        </div>
    </div>

    <script type="text/javascript">
        const btnSend = document.getElementById('send');
        
        //Réponse
        const sectionAns = document.getElementById("reponseSection");
        document.getElementById('subject').addEventListener('change', function() {
            if (this.value == "") {
                btnSend.style.display = "none";
                sectionAns.style.display = "none";
                sectionRdv.style.display = "none";
            } else {
                btnSend.style.display = "block";
            }

            if (this.value == "reponse") {
                btnSend.style.display = "block";
                sectionAns.style.display = "block";
                sectionRdv.style.display = "none";
            } else {
                sectionAns.style.display = "none";
            }
        });

        //Rendez vous
        const sectionRdv = document.querySelector('#rendezvous');
        document.getElementById('subject').addEventListener('change', function() {
            if (this.value == "rdv") {
                sectionAns.style.display = "block";
                sectionRdv.style.display = "block";
            } else {
                sectionRdv.style.display = "none";
            }
        });

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
        document.getElementById("dateRdv").setAttribute("min", today);
        
    </script>
</body>
</html>
