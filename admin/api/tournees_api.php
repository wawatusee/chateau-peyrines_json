<?php
// Définit le chemin du fichier principal des tournées
define('TOURNEE_FILE', '../json/tournee.json');

// Définit le type de contenu de la réponse comme JSON
header('Content-Type: application/json');

/**
 * Lit et décode le fichier JSON de la tournée.
 * @return array Le tableau complet (incluant 'events' et 'last-updated').
 */
function getTourneeData()
{
    $filePath = '../' . TOURNEE_FILE;

    if (!file_exists($filePath)) {
        return ['events' => [], 'last-updated' => date('Y-m-d H:i:s')];
    }

    $json = file_get_contents($filePath);
    $data = json_decode($json, true);

    if (!is_array($data) || !isset($data['events']) || !is_array($data['events'])) {
        return ['events' => [], 'last-updated' => date('Y-m-d H:i:s')];
    }

    return $data;
}

/**
 * Écrit les données de la tournée dans le fichier JSON.
 * @param array $data Le tableau de données complet à écrire.
 * @return bool Vrai en cas de succès, faux sinon.
 */
function saveTourneeData(array $data)
{
    $data['last-updated'] = date('Y-m-d H:i:s');
    $json_data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $filePath = '../' . TOURNEE_FILE;
    return file_put_contents($filePath, $json_data, LOCK_EX) !== false;
}

// -------------------------------------------------------------------
// --- LOGIQUE DE TRAITEMENT DES REQUÊTES ---
// -------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Méthode non supportée. Utilisez POST.']);
    exit;
}

$input = file_get_contents('php://input');
$requestData = json_decode($input, true);

if (!isset($requestData['action'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Action requise.']);
    exit;
}

$action = $requestData['action'];
$tourneeData = getTourneeData();
$eventsList = $tourneeData['events']; // Utilisation cohérente de 'events'

// -------------------------------------------------------------------
// --- TRAITEMENT DES ACTIONS ---
// -------------------------------------------------------------------

switch ($action) {
    case 'add':
    case 'edit':
        // Vérification des champs requis
        if (empty($requestData['date']) || empty($requestData['type']) || empty($requestData['location'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Données manquantes.']);
            exit;
        }

        $newEvent = [
            'date' => $requestData['date'],
            'location' => [
                'nom' => $requestData['location']['nom'],
                'lat' => (float) $requestData['location']['lat'],
                'lon' => (float) $requestData['location']['lon'],
            ],
            'type' => $requestData['type'],
            'text' => $requestData['text'] ?? '',
            'état' => (string) $requestData['etat']
        ];

        $index = $requestData['index'] ?? -1;

        if ($action === 'add') {
            $eventsList[] = $newEvent;
            $message = 'Événement ajouté avec succès.';
            $newIndex = count($eventsList) - 1;
        } elseif ($action === 'edit' && $index !== -1 && isset($eventsList[$index])) {
            $eventsList[$index] = $newEvent;
            $message = 'Événement mis à jour avec succès.';
            $newIndex = $index;
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Index d\'édition invalide.']);
            exit;
        }
        break;


    case 'delete':
        $index = $requestData['index'] ?? -1;

        if ($index !== -1 && isset($eventsList[$index])) {
            array_splice($eventsList, $index, 1);
            $message = 'Événement supprimé avec succès.';
            $newIndex = -1;
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

$tourneeData['events'] = $eventsList; // Mise à jour de la clé 'events'

if (saveTourneeData($tourneeData)) {
    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'events' => $eventsList, // Retourne la liste des événements
        'lastUpdated' => $tourneeData['last-updated']
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erreur critique lors de l\'écriture du fichier JSON.']);
}