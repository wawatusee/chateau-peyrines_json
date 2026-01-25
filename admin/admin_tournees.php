<?php
// Inclut la gestion de session
include 'session_management.php';

// --- Définitions des chemins des fichiers JSON ---
define('TOURNEE_FILE', '../json/tournee.json');      // Fichier principal à administrer
define('LOCATIONS_FILE', 'json/locations.json');     // Fichier de référence des lieux
define('TYPES_FILE', 'json/events_types.json');      // Fichier de référence des types

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
        // Pour le fichier des tournées, retourne une structure de base
        if ($filePath === TOURNEE_FILE) {
            return ['events' => [], 'last-updated' => 'N/A'];
        }
        return [];
    }

    $json = file_get_contents($filePath);
    $data = json_decode($json, true);

    return is_array($data) ? $data : [];
}

// -------------------------------------------------------------------
// --- PRÉPARATION DES DONNÉES POUR JAVASCRIPT ---
// -------------------------------------------------------------------

// 1. Chargement des données de référence (Lieux et Types)
$locationsData = getJsonData(LOCATIONS_FILE);
$typesData = getJsonData(TYPES_FILE);

// 2. Chargement du fichier de la tournée
$tourneeData = getJsonData(TOURNEE_FILE);
$eventsList = $tourneeData['events'] ?? [];
$lastUpdated = $tourneeData['last-updated'] ?? 'N/A';  // Correction : $tourneeData au lieu de $tourneesData

// --- Encodage JSON pour le JavaScript ---
// Transmet les données de référence et les événements initiaux
$locationsJson = json_encode($locationsData, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
$typesJson = json_encode($typesData, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); // Contient les IDs et les libellés
$eventsListJson = json_encode($eventsList, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration de la tournée</title>
    <link rel="stylesheet" href="css/admin.css">
</head>

<body>
    <div class="admin-container">
        <h1>Administration des événements de la tournée</h1>

        <nav class="admin-nav">
            <ul>
                <li><a href="admin.php">Tableau de Bord</a></li>
                <li><a href="admin_locations.php">Gérer les Lieux (<?php echo count($locationsData); ?>)</a></li>
                <li><a href="admin_types.php">Gérer les Types d'événements (<?php echo count($typesData); ?>)</a></li>
                <li><a href="?logout=1">Déconnexion</a></li>
            </ul>
        </nav>

        <div class="admin-content">
            <p>Dernière mise à jour du fichier <strong>tournee.json</strong> :
                <strong><?php echo $lastUpdated; ?></strong>
            </p>

            <hr style="margin: 20px 0;">

            <div id="tournees-management-app">
                <p>Initialisation de l'interface de gestion des tournées...</p>
            </div>
        </div>
    </div>

    <script>
        // Transmet les données de référence chargées en PHP au script JS
        // Données de référence : lieux et types (avec IDs et libellés)
        const REFERENCE_LOCATIONS = <?php echo $locationsJson; ?>;
        const REFERENCE_TYPES = <?php echo $typesJson; ?>; // Ex: [{"id": "TYPE-001", "name": "Livraison"}, 
        const INITIAL_EVENTS = <?php echo $eventsListJson; ?>;
    </script>

    <script src="js/admin.js"></script>
</body>

</html>