<?php
    require 'connexion_déconnexion/bdd_connexion.php';

// Début
    session_start();
    //Si le client ne s'est pas connecté on renvoie à la page de connexion
    if (empty($_SESSION['username'])) {
        header('location: ../pages/connexion.php');
        exit();
    }

    $request = $bdd->prepare('SELECT COUNT(*) as nbrenseignement FROM renseignements');
    $request->execute();
    while ($donnee = $request->fetch()) {
        if ($donnee['nbrenseignement'] != 0) {
            $btnActivateDemande = true;
        }
    }

    //Compter si il existe des rdv
    $request = $bdd->prepare('SELECT COUNT(*) as nbRdv FROM prise_rdv');
    $request->execute();
    while ($donnee = $request->fetch()) {
        if ($donnee['nbRdv'] != 0) {
            $btnActivateRdv = true;
        }
    }
?>

<!Doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ludus-ERP : Accueil</title>
    <link type="text/css" rel="stylesheet" href="../src/style/accueil.css?t=<? echo time(); ?>"/>
</head>
<body>
    <header>
        <h2 style="margin-left: .4em;">Bienvenue  <?php echo '<span style="color: green;">'.$_SESSION['username'].'</span>';?></h2>
        <ul>
            <li><a href="account_settings.php">Mon compte</a></li>
        </ul>
    </header>

    <div id="main">
        <div class="container">
            <button type="button" id="disabledAsk" onclick="location.href = 'demande_renseignement.php'">Demandes de renseignement</button>

            <button type="button" onclick="location.href = 'template.php'">Template Mail</button>

            <button type="button" id="disabled">Voir les rendez-vous</button>
        
        </div>
        <div class="container">
            <button disabled id="disabled1" type="button" onclick="location.href = 'dossier_candidature.php'">Dossier candidature</button>
        </div>
        <button type="button" id="btn_form" onclick="location.href = 'connexion_déconnexion/deconnexion.php'">Se déconnecter</button>
    </div>
    <script name="btn-rendez-vous" type="text/javascript">
        //Premiere fenetre à ouvrir
        const btn = document.querySelector('#disabled');
        btn.disabled = true;
        let btnActivateRdv = '<?php Print($btnActivateRdv) ?>';
        let counterArea1 = 1;

        if (!btnActivateRdv) {
            btn.addEventListener("click", () => {
                alert(`Vous n'avez pas de rendez-vous en ce moment...`);
            });
        }

        // Si le bouton est activé alors on affiche la première fenêtre
        if (btnActivateRdv) {
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
                window.location.replace('./consulter_rdv.php');
            });
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
    </script>

    <script name="btn-demande-renseignement" type="text/javascript">
        //Premiere fenetre à ouvrir
        const btnAsk = document.querySelector('#disabledAsk');
        btnAsk.disabled = true;
        let btnActivateDemande = '<?php Print($btnActivateDemande) ?>';
        let counterArea2 = 1;

        if (!btnActivateDemande) {
            btnAsk.addEventListener("click", () => {
                alert(`Vous n'avez pas de rendez-vous en ce moment...`);
            });
        }

        // Si le bouton est activé alors on affiche la première fenêtre
        if (btnActivateDemande) {
            btnAsk.disabled = false;

            //Style pour le bouton
            btnAsk.style.backgroundColor = "#5f14ff";
            btnAsk.onmouseenter = function() {
                btnAsk.style.boxShadow = "4px 3px 1px #014ca9";
                btnAsk.style.backgroundColor = "#007bff";
                btnAsk.style.color = "white";
            }
            //Style pour le bouton
            btnAsk.onmouseout = function() {
                btnAsk.style.boxShadow = "none";
                btnAsk.style.backgroundColor = "#5f14ff";
                btnAsk.style.color = "white";
            }

            //Affichage première fenêtre au click
            btnAsk.addEventListener("click", () => {
                window.location.replace('./demande_renseignement.php');
            });
        }

        if (counterArea2 === 2) {
            counterArea2 = 1;
        }

        //Style pour le bouton
        btnAsk.onmousedown = function() {
            btn.style.boxShadow = "0 0 6px 2px #000d7a inset";
            btn.style.backgroundColor = "#007bff";
            btn.style.color = "#cccccc";
        }

        //Style pour le bouton
        btnAsk.onmouseup = function() {
            btn.style.boxShadow = "none";
            btn.style.backgroundColor = "#5f14ff";
            btn.style.color = "white";
        }
    </script>
</body>
</html>