<?php
// Inclut la gestion de session (authentification et déconnexion)
include 'session_management.php';

// Définit le chemin du fichier JSON des tournées
define('TOURNEE_FILE', '../json/tournee.json'); // Assurez-vous que le chemin est correct

/**
 * Lit et décode le fichier JSON des tournées.
 * @return array Les données des tournées ou un tableau vide si erreur.
 */
function getTourneeData() {
    if (!file_exists(TOURNEE_FILE)) {
        // En cas d'absence du fichier, créer une structure vide pour éviter une erreur fatale.
        // La structure doit contenir un tableau 'tournees' si l'on suit le modèle du JSON initial.
        return ['events' => [], 'last-updated' => date('Y-m-d H:i:s')];
    }
    
    $json = file_get_contents(TOURNEE_FILE);
    $data = json_decode($json, true);

    
    // Si $data n'est pas un tableau ou que la clé 'tournees' n'existe pas, initialiser correctement.
    if (!is_array($data) || !isset($data['events']) || !is_array($data['events'])) {
        // Si le JSON ne respecte pas la structure attendue, on retourne une structure par défaut
        if (is_array($data)) {
            // Si $data existe mais 'tournees' manque (ex: il y a d'autres clés), on l'ajoute.
            $data['events'] = [];
        } else {
            // Sinon (JSON mal formé ou vide), on initialise tout.
            $data = ['events' => [], 'last-updated' => date('Y-m-d H:i:s')];
        }
    }

    return $data;
    
}


/**
 * Écrit les données de tournées dans le fichier JSON, en mettant à jour la date.
 * @param array $data Le tableau de données complet à écrire.
 * @return bool Vrai en cas de succès, faux sinon.
 */
function saveTourneeData(array $data) {
    // Met à jour la date de la dernière modification
    // Nous utiliserons ce champ pour indiquer quand la dernière action d'écriture a eu lieu.
    $data['last-updated'] = date('Y-m-d H:i:s');
    
    // Encode les données en JSON formaté
    $json_data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Écriture dans le fichier avec verrouillage
    return file_put_contents(TOURNEE_FILE, $json_data, LOCK_EX) !== false;
}

// --- Logique de l'administration ---

// Lire les données actuelles
$tourneeData = getTourneeData();

$lastUpdated = $tourneeData['last-updated'] ?? 'N/A';
$nombreEvents = count($tourneeData['events'] ?? []); // Utilise null coalesce pour la sûreté
echo $nombreEvents;

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
        <h1>Administration des Tournées</h1>
        
        <nav class="admin-nav">
            <ul>
                <li><a href="admin_tournees.php" class="admin-headers-btns">Gérer les Tournées (Composition des événements)</a></li>
                <li><a href="admin_locations.php">Gérer les Lieux de Tournées</a></li>
                <li><a href="admin_types.php">Gérer les Types d'événements</a></li>
                <li><a href="?logout=1">Déconnexion</a></li>
            </ul>
        </nav>
        
        <div class="admin-content">
            <h2>Tableau de Bord</h2>
            <p>Bienvenue sur l'interface d'administration de tournée.</p>
            <p>Le fichier JSON **`<?=TOURNEE_FILE?>`** contient actuellement **<?php echo $nombreEvents; ?>** entrées de tournée.</p>
            <p>Dernière écriture/modification du fichier : **<?php echo $lastUpdated; ?>**</p>
            
            <p>Veuillez sélectionner une section dans le menu ci-dessus pour commencer l'administration.</p>
            
            <div id="tournees-management-app" style="display:none;"></div>
            
        </div>
    </div>
    
    </body>
</html>