<?php
session_start();
require_once 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_commande = intval($data['id'] ?? 0);
    
    if (!$id_commande) {
        $response['message'] = "ID de commande invalide";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    try {
        $db = getDB();
        
        // Supprimer la commande (cascade supprimera les relations)
        $stmt = $db->prepare("DELETE FROM commandes WHERE id_commande = ?");
        $stmt->execute([$id_commande]);
        
        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = "Livraison supprimée avec succès";
        } else {
            $response['message'] = "Commande introuvable";
        }
        
    } catch (PDOException $e) {
        $response['message'] = "Erreur : " . $e->getMessage();
    }
} else {
    $response['message'] = "Méthode non autorisée";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
