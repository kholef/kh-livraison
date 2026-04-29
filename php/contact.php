<?php
session_start();
require_once 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    $errors = [];
    
    if (empty($nom)) $errors[] = "Le nom est requis";
    if (empty($prenom)) $errors[] = "Le prénom est requis";
    if (empty($email)) $errors[] = "L'email est requis";
    if (empty($message)) $errors[] = "Le message est requis";
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }
    
    if (!empty($message) && strlen($message) < 10) {
        $errors[] = "Le message doit contenir au moins 10 caractères";
    }
    
    if (empty($errors)) {
        try {
            $db = getDB();
            $stmt = $db->prepare("
                INSERT INTO contacts (nom, prenom, email, telephone, message) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nom, $prenom, $email, $telephone, $subject . ' - ' . $message]);
            
            $response['success'] = true;
            $response['message'] = "Merci " . $prenom . " ! Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.";
            
        } catch (PDOException $e) {
            $response['message'] = "Erreur : " . $e->getMessage();
        }
    } else {
        $response['message'] = implode("<br>", $errors);
    }
} else {
    $response['message'] = "Méthode non autorisée";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
