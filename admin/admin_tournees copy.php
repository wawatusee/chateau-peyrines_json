<?php
// Inclut la gestion de session
include 'session_management.php';

// --- Définitions des chemins des fichiers JSON ---
define('TOURNEE_FILE', '../json/tournees.json'); // Fichier principal à administrer
define('LOCATIONS_FILE', 'json/locations.json'); // Fichier de référence des lieux (LOC-XXX)
define('TYPES_FILE', 'json/events_types.json');   // Fichier de référence des types (TYPE-XXX)

// -------------------------------------------------------------------
// --- FONCTIONS DE GESTION DES FICHIERS JSON ---
// -------------------------------------------------------------------

/**
 * Lit et décode un fichier JSON spécifié.
 * @param string $filePath Le chemin du fichier JSON.
 * @return array Les données décodées ou un tableau vide si erreur/inexistant.
 */
function getJsonData(string $filePath): array
{
    if (!file_exists($filePath)) {
        // Pour le fichier des tournées, nous retournons une structure de base pour éviter les erreurs
        if ($filePath === TOURNEES_FILE) {
            return ['tournees' => [], 'last-updated' => 'N/A'];
        }
        return [];
    }

    $json = file_get_contents($filePath);
    $data = json_decode($json, true);

    return is_array($data) ? $data : [];
}

// Les fonctions saveTourneesData, getTourneesData, saveLocationsData, etc. seront gérées
// par des fonctions AJAX (API) dans les prochaines étapes pour une gestion propre des actions CRUD.

// -------------------------------------------------------------------
// --- PRÉPARATION DES DONNÉES POUR JAVASCRIPT ---
// -------------------------------------------------------------------

// 1. Chargement des données de référence (Lieux et Types)
$locationsData = getJsonData(LOCATIONS_FILE);
$typesData = getJsonData(TYPES_FILE);

// 2. Chargement du fichier des tournées (avec sa structure 'tournees' si présent)
$tourneesData = getJsonData(TOURNEES_FILE);
$tourneesList = $tourneesData['tournees'] ?? [];
$lastUpdated = $tourneesData['last-updated'] ?? 'N/A';


// --- Encodage JSON pour le JavaScript ---
// Ces variables seront accessibles par admin.js
$locationsJson = json_encode($locationsData, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
$typesJson = json_encode($typesData, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
$tourneesListJson = json_encode($tourneesList, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des Tournées</title>
    <link rel="stylesheet" href="css/admin.css">
</head>

<body>

    <div class="admin-container">
        <h1>Administration des Tournées et Événements</h1>

        <nav class="admin-nav">
            <ul>
                <li><a href="admin.php">Tableau de Bord</a></li>
                <li><a href="admin_locations.php">Gérer les Lieux (<?php echo count($locationsData); ?>)</a></li>
                <li><a href="admin_types.php">Gérer les Types (<?php echo count($typesData); ?>)</a></li>
                <li><a href="?logout=1">Déconnexion</a></li>
            </ul>
        </nav>

        <div class="admin-content">

            <p>Dernière mise à jour du fichier **`tournees.json`** : **<?php echo $lastUpdated; ?>**</p>

            <hr style="margin: 20px 0;">

            <div id="tournees-management-app">
                <p>Initialisation de l'interface de gestion des tournées...</p>
            </div>

        </div>
    </div>

    <script>
        // Transmet les données de référence chargées en PHP au script JS
        const REFERENCE_LOCATIONS = JSON.parse('<?php echo $locationsJson; ?>');
        const REFERENCE_TYPES = JSON.parse('<?php echo $typesJson; ?>');
        const INITIAL_TOURNEES = JSON.parse('<?php echo $tourneesListJson; ?>');
    </script>

    <script src="js/admin.js"></script>

</body>

</html>