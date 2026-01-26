<?php
// Définit le chemin du fichier principal des tournées
define('TOURNEE_FILE', '../json/tournees.json'); 
// Assurez-vous d'avoir les chemins des fichiers de référence si nécessaires, 
// mais l'API se concentrera principalement sur TOURNEES_FILE

// Définit le type de contenu de la réponse comme JSON
header('Content-Type: application/json');

// Inclut les fonctions utilitaires pour la lecture/écriture (issues de admin.php)
// Normalement, ces fonctions devraient être isolées dans un fichier 'db_functions.php' 
// pour une API propre, mais nous les réimplémentons ici pour la simplicité.

/**
 * Lit et décode le fichier JSON des tournées.
 * @return array Le tableau complet (incluant 'tournees' et 'last-updated').
 */
function getTourneesData() {
    // Le chemin doit être adapté pour être appelé depuis le dossier 'api'
    $filePath = '../' . TOURNEES_FILE; 
    
    if (!file_exists($filePath)) {
        return ['tournees' => [], 'last-updated' => date('Y-m-d H:i:s')];
    }
    
    $json = file_get_contents($filePath);
    $data = json_decode($json, true);
    
    // S'assurer que la structure est valide
    if (!is_array($data) || !isset($data['tournees']) || !is_array($data['tournees'])) {
        return ['tournees' => [], 'last-updated' => date('Y-m-d H:i:s')];
    }
    
    return $data;
}

/**
 * Écrit les données de tournées dans le fichier JSON.
 * @param array $data Le tableau de données complet à écrire.
 * @return bool Vrai en cas de succès, faux sinon.
 */
function saveTourneesData(array $data) {
    // Met à jour la date de la dernière modification
    $data['last-updated'] = date('Y-m-d H:i:s');
    
    // Encode les données en JSON formaté
    $json_data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Écriture dans le fichier avec verrouillage
    $filePath = '../' . TOURNEES_FILE;
    return file_put_contents($filePath, $json_data, LOCK_EX) !== false;
}

// -------------------------------------------------------------------
// --- LOGIQUE DE TRAITEMENT DES REQUÊTES ---
// -------------------------------------------------------------------

// S'assurer que la requête est de type POST (méthode privilégiée pour les actions CRUD)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Méthode non autorisée
    echo json_encode(['status' => 'error', 'message' => 'Méthode non supportée. Utilisez POST.']);
    exit;
}

// Récupérer les données envoyées par le JavaScript (JSON brut)
$input = file_get_contents('php://input');
$requestData = json_decode($input, true);

// Vérification de la validité des données
if (!isset($requestData['action'])) {
    http_response_code(400); // Mauvaise requête
    echo json_encode(['status' => 'error', 'message' => 'Action requise.']);
    exit;
}

$action = $requestData['action'];
$tourneesData = getTourneesData();
$tourneesList = $tourneesData['tournees'];

// -------------------------------------------------------------------
// --- TRAITEMENT DES ACTIONS ---
// -------------------------------------------------------------------

switch ($action) {
    case 'add':
    case 'edit':
        // Vérification des champs requis pour l'ajout/édition
        if (
            empty($requestData['date']) || 
            empty($requestData['type']) || 
            empty($requestData['locationId']) || 
            !isset($requestData['etat']) || 
            !isset($requestData['location'])
        ) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Données de tournée incomplètes.']);
            exit;
        }

        $newTournee = [
            'date' => $requestData['date'],
            'location' => [
                'nom' => $requestData['location']['nom'],
                'lat' => (float)$requestData['location']['lat'],
                'lon' => (float)$requestData['location']['lon'],
            ],
            'type' => $requestData['type'],
            'text' => $requestData['text'] ?? '',
            'état' => (string)$requestData['etat']
        ];
        
        $index = $requestData['index'] ?? -1;
        
        if ($action === 'add') {
            // Ajout : on insère la nouvelle tournée à la fin
            $tourneesList[] = $newTournee;
            $message = 'Tournée ajoutée avec succès.';
            $newIndex = count($tourneesList) - 1;

        } elseif ($action === 'edit' && $index !== -1 && isset($tourneesList[$index])) {
            // Édition : on remplace l'ancienne entrée par la nouvelle
            $tourneesList[$index] = $newTournee;
            $message = 'Tournée mise à jour avec succès.';
            $newIndex = $index;
            
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Index d\'édition invalide.']);
            exit;
        }

        break;

    case 'delete':
        $index = $requestData['index'] ?? -1;

        if ($index !== -1 && isset($tourneesList[$index])) {
            // Suppression : on retire l'élément du tableau
            array_splice($tourneesList, $index, 1);
            $message = 'Tournée supprimée avec succès.';
            $newIndex = -1; // Pas d'index de retour pour la suppression
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Index de suppression invalide.']);
            exit;
        }
        
        break;

    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Action inconnue.']);
        exit;
}

// -------------------------------------------------------------------
// --- SAUVEGARDE ET RÉPONSE FINALE ---
// -------------------------------------------------------------------

// Mettre à jour la liste des tournées dans le tableau complet
$tourneesData['tournees'] = $tourneesList;

if (saveTourneesData($tourneesData)) {
    // Succès
    echo json_encode([
        'status' => 'success', 
        'message' => $message,
        'tournees' => $tourneesList, // Retourne la liste complète pour rafraîchir le front-end
        'lastUpdated' => $tourneesData['last-updated']
    ]);
} else {
    // Échec de l'écriture sur le disque
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erreur critique lors de l\'écriture du fichier JSON.']);
}