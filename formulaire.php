<?php
// Inclure PHPMailer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Variables pour les champs du formulaire
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    // Validation de l'email
    if (!$email) {
        die("Adresse e-mail invalide.");
    }

    // Préparation des fichiers joints
    $uploadDirectory = "uploads/";
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);
    }

    $attachments = [];
    $allowedFileTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

    // Gestion du CV
    if (!empty($_FILES['cv']['name']) && in_array($_FILES['cv']['type'], $allowedFileTypes)) {
        $cvPath = $uploadDirectory . basename($_FILES['cv']['name']);
        if (move_uploaded_file($_FILES['cv']['tmp_name'], $cvPath)) {
            $attachments['cv'] = $cvPath;
        }
    }

    // Gestion de la lettre de motivation
    if (!empty($_FILES['lm']['name']) && in_array($_FILES['lm']['type'], $allowedFileTypes)) {
        $lmPath = $uploadDirectory . basename($_FILES['lm']['name']);
        if (move_uploaded_file($_FILES['lm']['tmp_name'], $lmPath)) {
            $attachments['lm'] = $lmPath;
        }
    }

    // Configurer PHPMailer
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 2; // Affiche les messages de débogage


    try {
        // Paramètres SMTP
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // Remplacez par votre adresse Gmail
        $mail->Password = 'your-app-password'; // Remplacez par un mot de passe d'application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        


        // Destinataire
        $mail->setFrom($email, "$prenom $nom");
        $mail->addAddress('o.verdifinanssor@gmail.com', 'Recrutement'); // Adresse du destinataire

        // Sujet et contenu
        $mail->isHTML(true);
        $mail->Subject = "Nouvelle candidature : $prenom $nom";
        $mail->Body = "
            <h1>Nouvelle Candidature</h1>
            <p><strong>Nom :</strong> $nom</p>
            <p><strong>Prénom :</strong> $prenom</p>
            <p><strong>Email :</strong> $email</p>
        ";

        // Ajouter les pièces jointes
        foreach ($attachments as $filename => $filepath) {
            $mail->addAttachment($filepath, $filename);
        }

        // Envoyer l'e-mail
        if ($mail->send()) {
            echo "Votre candidature a été envoyée avec succès.";
        } else {
            echo "Erreur lors de l'envoi. Veuillez réessayer.";
        }
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi : {$mail->ErrorInfo}";
    }

    // Nettoyage des fichiers téléchargés
    foreach ($attachments as $filepath) {
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}
