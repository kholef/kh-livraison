<?php
session_start();
require_once 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $mot_de_passe = $_POST['password'] ?? '';
    
    $errors = [];
    
    if (empty($nom)) $errors[] = "Le nom est requis";
    if (empty($prenom)) $errors[] = "Le prénom est requis";
    if (empty($email)) $errors[] = "L'email est requis";
    if (empty($telephone)) $errors[] = "Le téléphone est requis";
    if (empty($mot_de_passe)) $errors[] = "Le mot de passe est requis";
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }
    
    if (!empty($mot_de_passe) && strlen($mot_de_passe) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
    }
    
    if (!empty($telephone) && !preg_match('/^[0-9]{10}$/', str_replace(' ', '', $telephone))) {
        $errors[] = "Le numéro de téléphone n'est pas valide";
    }
    
    if (empty($errors)) {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $response['message'] = "Cet email est déjà utilisé";
            } else {
                $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nom, $prenom, $email, $telephone, $mot_de_passe_hash]);
                
                $id_utilisateur = $db->lastInsertId();
                $_SESSION['user_id'] = $id_utilisateur;
                $_SESSION['user_nom'] = $nom;
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_email'] = $email;
                
                $response['success'] = true;
                $response['message'] = "Inscription réussie ! Bienvenue " . $prenom . " !";
                $response['redirect'] = 'livraisons.html';
            }
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
