<?php
session_start();
require_once 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pour le test, utiliser un ID utilisateur fixe
    $id_utilisateur = $_SESSION['user_id'] ?? 1;
    
    $id_service = intval($_POST['service'] ?? 0);
    $adresse = trim($_POST['address'] ?? '');
    $ville = trim($_POST['city'] ?? '');
    $code_postal = trim($_POST['postal'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    
    // Validation
    if (!$id_service || !$adresse || !$ville || !$code_postal) {
        $response['message'] = "Tous les champs obligatoires doivent être remplis";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        $db = getDB();
        
        // Récupérer le prix du service
        $stmt = $db->prepare("SELECT prix_base FROM services WHERE id_service = ?");
        $stmt->execute([$id_service]);
        $service = $stmt->fetch();
        
        if (!$service) {
            $response['message'] = "Service invalide";
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        $prix_total = $service['prix_base'];
        
        // Créer l'adresse
        $stmt = $db->prepare("
            INSERT INTO adresses (id_utilisateur, adresse_ligne1, ville, code_postal, type_adresse) 
            VALUES (?, ?, ?, ?, 'livraison')
        ");
        $stmt->execute([$id_utilisateur, $adresse, $ville, $code_postal]);
        $id_adresse = $db->lastInsertId();
        
        // Générer un code de suivi unique
        $code_suivi = 'KH' . strtoupper(substr(md5(uniqid()), 0, 10));
        
        // Créer la commande
        $stmt = $db->prepare("
            INSERT INTO commandes (
                id_utilisateur, id_service, id_adresse_livraison, 
                statut, prix_total, instructions_livraison, code_suivi
            ) VALUES (?, ?, ?, 'en_attente', ?, ?, ?)
        ");
        $stmt->execute([
            $id_utilisateur, $id_service, $id_adresse,
            $prix_total, $instructions, $code_suivi
        ]);
        
        $response['success'] = true;
        $response['message'] = "Livraison créée avec succès ! Code de suivi : " . $code_suivi;
        
    } catch (PDOException $e) {
        $response['message'] = "Erreur : " . $e->getMessage();
    }
} else {
    $response['message'] = "Méthode non autorisée";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
