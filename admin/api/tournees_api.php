<?php
/**
 * api/tournees_api.php
 * Gestion CRUD du fichier tournee.json
 */

// 1. BUFFERING & ERROR HANDLING
// On démarre la mémoire tampon pour capturer tout texte parasite (warnings, espaces)
ob_start();
ini_set('display_errors', 0); // Pas d'erreurs PHP dans la sortie HTTP
error_reporting(E_ALL);       // On logue tout en interne si besoin

header('Content-Type: application/json; charset=utf-8');

// 2. DÉFINITION ROBUSTE DU CHEMIN
// __DIR__ est ".../admin/api"
// On remonte de 2 niveaux pour atteindre la racine du projet "D:/..."
$projectRoot = dirname(__DIR__, 2);
$jsonFilePath = $projectRoot . '/json/tournee.json';

// --- FONCTIONS UTILITAIRES ---

/**
 * Envoie une réponse JSON propre et arrête le script.
 */
function sendResponse($data, $code = 200) {
    // On nettoie le buffer de sortie (supprime les warnings PHP potentiels avant le JSON)
    ob_clean();
    http_response_code($code);
    echo json_encode($data);
    exit;
}

/**
 * Lit les données du fichier JSON.
 */
function getTourneeData($filePath) {
    if (!file_exists($filePath)) {
        // Retourne une structure par défaut si le fichier n'existe pas
        return ['events' => [], 'last-updated' => date('Y-m-d H:i:s')];
    }
    
    $content = file_get_contents($filePath);
    $data = json_decode($content, true);

    if (!is_array($data)) {
        return ['events' => [], 'last-updated' => date('Y-m-d H:i:s')];
    }
    return $data;
}

/**
 * Sauvegarde les données.
 */
function saveTourneeData($filePath, array $data) {
    $data['last-updated'] = date('Y-m-d H:i:s');
    $jsonString = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    // Vérification que le dossier parent existe, sinon on le crée
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    return file_put_contents($filePath, $jsonString, LOCK_EX) !== false;
}

// --- LOGIQUE PRINCIPALE ---

try {
    // Vérification Méthode
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée (POST requis).', 405);
    }

    // Lecture de l'input
    $input = file_get_contents('php://input');
    $requestData = json_decode($input, true);

    if (!$requestData || !isset($requestData['action'])) {
        throw new Exception('Données invalides ou action manquante.', 400);
    }

    // Chargement des données
    $tourneeData = getTourneeData($jsonFilePath);
    $eventsList = $tourneeData['events'] ?? [];
    $action = $requestData['action'];
    $message = '';

    // Traitement des actions
    switch ($action) {
        case 'add':
        case 'edit':
            // Validation des champs obligatoires
            if (empty($requestData['date']) || empty($requestData['type']) || empty($requestData['location'])) {
                throw new Exception('Champs requis manquants.', 400);
            }

            $newEvent = [
                'date' => $requestData['date'],
                'location' => [
                    'nom' => $requestData['location']['nom'],
                    'lat' => $requestData['location']['lat'],
                    'lon' => $requestData['location']['lon'],
                ],
                'type' => $requestData['type'],
                'text' => $requestData['text'] ?? '',
                'état' => (string) ($requestData['etat'] ?? '0')
            ];

            if ($action === 'add') {
                $eventsList[] = $newEvent;
                $message = 'Événement ajouté avec succès.';
            } else {
                $index = (int) ($requestData['index'] ?? -1);
                if (!isset($eventsList[$index])) {
                    throw new Exception('Index introuvable pour modification.', 400);
                }
                $eventsList[$index] = $newEvent;
                $message = 'Événement modifié avec succès.';
            }
            break;

        case 'delete':
            $index = (int) ($requestData['index'] ?? -1);
            if (!isset($eventsList[$index])) {
                throw new Exception('Index introuvable pour suppression.', 400);
            }
            array_splice($eventsList, $index, 1);
            $message = 'Événement supprimé avec succès.';
            break;

        default:
            throw new Exception("Action '$action' inconnue.", 400);
    }

    // Sauvegarde finale
    $tourneeData['events'] = $eventsList;

    if (saveTourneeData($jsonFilePath, $tourneeData)) {
        sendResponse([
            'status' => 'success',
            'message' => $message,
            'events' => $eventsList,
            'lastUpdated' => $tourneeData['last-updated']
        ]);
    } else {
        throw new Exception("Échec de l'écriture du fichier JSON (Permissions ?).", 500);
    }

} catch (Exception $e) {
    // Gestion centralisée des erreurs
    $code = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
    sendResponse(['status' => 'error', 'message' => $e->getMessage()], $code);
}
?>