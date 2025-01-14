<?php
// Configurez votre adresse email
$to = "o.verdifinanssor@gmail.com"; // Remplacez par votre adresse email
$subject = "Nouveau message du formulaire de contact";

// Vérifiez si les données du formulaire sont envoyées
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $message = htmlspecialchars($_POST["message"]);

    // Vérifiez que tous les champs sont remplis
    if (!empty($name) && !empty($email) && !empty($message)) {
        // Composez le message
        $body = "Nom: $name\n";
        $body .= "Email: $email\n\n";
        $body .= "Message:\n$message\n";

        // En-têtes pour l'email
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";

        // Tentez d'envoyer l'email
        if (mail($to, $subject, $body, $headers)) {
            echo "<script>
                    alert('Merci ! Votre message a été envoyé avec succès.');
                    window.location.href = 'contact.html';
                  </script>";
        } else {
            echo "<script>
                    alert('Erreur : Impossible d\'envoyer le message. Veuillez réessayer.');
                    window.history.back();
                  </script>";
        }
    } else {
        echo "<script>
                alert('Veuillez remplir tous les champs.');
                window.history.back();
              </script>";
    }
} else {
    echo "<script>
            alert('Méthode non autorisée.');
            window.history.back();
          </script>";
}
?>
