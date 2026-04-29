<?php
session_start();
require_once 'config.php';

$response = ['success' => false, 'message' => '', 'deliveries' => []];

// Vérifier l'authentification (simplifiée pour test)
// if (!isset($_SESSION['user_id'])) {
//     $response['message'] = "Non authentifié";
//     header('Content-Type: application/json');
//     echo json_encode($response);
//     exit;
// }

try {
    $db = getDB();
    
    // Pour le moment, récupérer toutes les livraisons
    // En production, filtrer par utilisateur : WHERE id_utilisateur = ?
    $stmt = $db->prepare("
        SELECT 
            c.id_commande as id,
            c.id_service,
            c.statut,
            c.prix_total,
            c.instructions_livraison as instructions,
            c.date_commande,
            c.code_suivi,
            a.adresse_ligne1 as adresse,
            a.ville,
            a.code_postal
        FROM commandes c
        LEFT JOIN adresses a ON c.id_adresse_livraison = a.id_adresse
        ORDER BY c.date_commande DESC
    ");
    
    $stmt->execute();
    $deliveries = $stmt->fetchAll();
    
    $response['success'] = true;
    $response['deliveries'] = $deliveries;
    
} catch (PDOException $e) {
    $response['message'] = "Erreur : " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
