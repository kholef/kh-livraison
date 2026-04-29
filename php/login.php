<?php
session_start();
require_once 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['password'] ?? '';
    $se_souvenir = isset($_POST['remember']);
    
    if (empty($email)) {
        $response['message'] = "L'email est requis";
    } elseif (empty($mot_de_passe)) {
        $response['message'] = "Le mot de passe est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "L'email n'est pas valide";
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT id_utilisateur, nom, prenom, email, mot_de_passe, actif FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                if (!$user['actif']) {
                    $response['message'] = "Votre compte a été désactivé";
                } elseif (password_verify($mot_de_passe, $user['mot_de_passe'])) {
                    $update = $db->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id_utilisateur = ?");
                    $update->execute([$user['id_utilisateur']]);
                    
                    $_SESSION['user_id'] = $user['id_utilisateur'];
                    $_SESSION['user_nom'] = $user['nom'];
                    $_SESSION['user_prenom'] = $user['prenom'];
                    $_SESSION['user_email'] = $user['email'];
                    
                    if ($se_souvenir) {
                        $token = bin2hex(random_bytes(32));
                        setcookie('remember_token', $token, time() + (86400 * 30), "/");
                    }
                    
                    $response['success'] = true;
                    $response['message'] = "Connexion réussie ! Bienvenue " . $user['prenom'] . " !";
                    $response['redirect'] = 'livraisons.html';
                } else {
                    $response['message'] = "Email ou mot de passe incorrect";
                }
            } else {
                $response['message'] = "Email ou mot de passe incorrect";
            }
        } catch (PDOException $e) {
            $response['message'] = "Erreur : " . $e->getMessage();
        }
    }
} else {
    $response['message'] = "Méthode non autorisée";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
