<?php
session_start();
require_once 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_commande = intval($_POST['id'] ?? 0);
    $id_service = intval($_POST['service'] ?? 0);
    $adresse = trim($_POST['address'] ?? '');
    $ville = trim($_POST['city'] ?? '');
    $code_postal = trim($_POST['postal'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    
    if (!$id_commande || !$id_service || !$adresse || !$ville || !$code_postal) {
        $response['message'] = "Tous les champs obligatoires doivent être remplis";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        $db = getDB();
        
        // Récupérer l'ID de l'adresse actuelle
        $stmt = $db->prepare("SELECT id_adresse_livraison FROM commandes WHERE id_commande = ?");
        $stmt->execute([$id_commande]);
        $commande = $stmt->fetch();
        
        if (!$commande) {
            $response['message'] = "Commande introuvable";
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Mettre à jour l'adresse
        $stmt = $db->prepare("
            UPDATE adresses 
            SET adresse_ligne1 = ?, ville = ?, code_postal = ?
            WHERE id_adresse = ?
        ");
        $stmt->execute([$adresse, $ville, $code_postal, $commande['id_adresse_livraison']]);
        
        // Récupérer le nouveau prix
        $stmt = $db->prepare("SELECT prix_base FROM services WHERE id_service = ?");
        $stmt->execute([$id_service]);
        $service = $stmt->fetch();
        $prix_total = $service['prix_base'];
        
        // Mettre à jour la commande
        $stmt = $db->prepare("
            UPDATE commandes 
            SET id_service = ?, prix_total = ?, instructions_livraison = ?
            WHERE id_commande = ?
        ");
        $stmt->execute([$id_service, $prix_total, $instructions, $id_commande]);
        
        $response['success'] = true;
        $response['message'] = "Livraison modifiée avec succès";
        
    } catch (PDOException $e) {
        $response['message'] = "Erreur : " . $e->getMessage();
    }
} else {
    $response['message'] = "Méthode non autorisée";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
