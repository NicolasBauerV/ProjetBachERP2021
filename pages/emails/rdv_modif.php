<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    function sendMailRdv($email, $nom, $prenom, $motif, $date, $time) {
        $fullName = $nom.' '.$prenom;
        $dateTime = 'Rendez-vous modifier : '.$date.' à : '.$time;
        $mail = new PHPMailer;
        $mail->isSMTP(); 
        //$mail->SMTPDebug = 2; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
        $mail->Host = "smtp.gmail.com"; // use $mail->Host = gethostbyname('smtp.gmail.com'); // if your network does not support SMTP over IPv6
        $mail->Port = 587; // TLS only
        $mail->SMTPSecure = 'tls'; // ssl is deprecated
        $mail->SMTPAuth = true;
        $mail->Username = 'projet.bachelor202021@gmail.com'; // email
        $mail->Password = 'LudusAcademieBach20202021'; // password
        
        //Recipients
        $mail->setFrom('projet.bachelor202021@gmail.com', 'ProjBach');
        $mail->addAddress($email, $fullName);     //Add a recipient
        //$mail->addAddress('ellen@example.com');               //Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('nikovcuurt@gmail.com');
        //$mail->addBCC('n.bauer@ludus-academie.com');

        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Rendez-vous modifier';
        $mail->Body    = "Votre rendez-vous a été modifié pour le motif suivant : ".$motif." . <br><b>Nouveau rendez-vous le : </b>"." <span style=\"color: red;\">".$dateTime."</span>";
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
        if(!$mail->send()){
            echo "Mailer Error: " . $mail->ErrorInfo;
        }else{
            echo "Message sent!";
            $isSended = true;
            return $isSended;
        }
    }
?>