<?php
// Inclut la gestion de session pour l'authentification
include 'session_management.php';

// Définit le chemin du fichier JSON des lieux
define('LOCATIONS_FILE', 'json/locations.json');

/**
 * Lit et décode le fichier JSON des lieux.
 * @return array Le tableau des lieux ou un tableau vide si erreur/fichier inexistant.
 */
function getLocationsData()
{
    if (!file_exists(LOCATIONS_FILE)) {
        return [];
    }

    $json = file_get_contents(LOCATIONS_FILE);
    $data = json_decode($json, true);

    return is_array($data) ? $data : [];
}

/**
 * Écrit les données des lieux dans le fichier JSON.
 * @param array $data Le tableau de données à écrire.
 * @return bool Vrai en cas de succès, faux sinon.
 */
function saveLocationsData(array $data)
{
    $json_data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(LOCATIONS_FILE, $json_data, LOCK_EX) !== false;
}

/**
 * Génère un identifiant unique basé sur le format 'LOC-XXX'.
 * @param array $currentLocations Tableau des lieux existants pour vérifier l'unicité.
 * @return string Nouvel ID unique.
 */
function generateUniqueLocationId(array $currentLocations)
{
    $ids = array_column($currentLocations, 'id');
    $i = 1;
    do {
        $newId = 'LOC-' . str_pad($i, 3, '0', STR_PAD_LEFT);
        $i++;
    } while (in_array($newId, $ids));

    return $newId;
}


$message = '';

// Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {

    $nom = trim($_POST['nom'] ?? '');
    $lat = trim($_POST['lat'] ?? '');
    $lon = trim($_POST['lon'] ?? '');

    // Validation des champs
    if (empty($nom) || empty($lat) || empty($lon)) {
        $message = '<p class="error">Le nom, la latitude et la longitude sont obligatoires.</p>';
    } elseif (!is_numeric($lat) || !is_numeric($lon)) {
        $message = '<p class="error">La Latitude et la Longitude doivent être des valeurs numériques (utiliser un point "." comme séparateur décimal).</p>';
    } else {
        $locations = getLocationsData();

        // Vérification de l'unicité du nom
        $nameExists = false;
        foreach ($locations as $location) {
            if (strtolower($location['nom']) === strtolower($nom)) {
                $nameExists = true;
                break;
            }
        }

        if ($nameExists) {
            $message = '<p class="error">Ce nom de lieu existe déjà.</p>';
        } else {
            // Génération de l'ID unique
            $newId = generateUniqueLocationId($locations);

            // Création de la nouvelle entrée
            $newLocation = [
                'id' => $newId,
                'nom' => $nom,
                'lat' => (float) $lat, // Conversion en float pour le JSON
                'lon' => (float) $lon
            ];

            // Ajout et sauvegarde
            $locations[] = $newLocation;
            if (saveLocationsData($locations)) {
                $message = '<p class="success">Le lieu **' . htmlspecialchars($nom) . '** a été ajouté avec succès (ID: ' . $newId . ').</p>';
            } else {
                $message = '<p class="error">Erreur lors de l\'enregistrement du fichier JSON.</p>';
            }
        }
    }
}

// Charger les données pour l'affichage
$locationsData = getLocationsData();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des Lieux de Tournées</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/admin_locations.css">
</head>

<body>

    <div class="admin-container">
        <h1>Administration des Lieux de Tournées</h1>

        <nav class="admin-nav">
            <ul>
                <li><a href="admin.php">Retour au Tableau de Bord</a></li>
                <li><a href="admin_tournees.php">Gérer les Tournées</a></li>
                <li><a href="admin_types.php">Gérer les Types d'événements</a></li>
                <li><a href="?logout=1">Déconnexion</a></li>
            </ul>
        </nav>

        <div class="admin-content">

            <?php echo $message; // Affiche les messages (erreur/succès) ?>
            <?php // --- Traitement de l'Action Suppression (DELETE) ---
            if (isset($_GET['delete_id'])) {
                $deleteId = trim($_GET['delete_id']);
                $locations = getLocationsData();
                $initialCount = count($locations);

                // Filtre pour garder tous les éléments SAUF celui à supprimer
                $locations = array_filter($locations, function ($location) use ($deleteId) {
                    return $location['id'] !== $deleteId;
                });

                // Si le nombre d'éléments a changé, cela signifie qu'un élément a été supprimé
                if (count($locations) < $initialCount) {
                    if (saveLocationsData($locations)) {
                        $message = '<p class="success">Le lieu **' . htmlspecialchars($deleteId) . '** a été supprimé avec succès.</p>';
                    } else {
                        $message = '<p class="error">Erreur lors de la suppression et de l\'enregistrement du fichier JSON.</p>';
                    }
                    // Redirection pour nettoyer l'URL et éviter la double soumission
                    header("Location: admin_locations.php");
                    exit();
                } else {
                    $message = '<p class="error">Erreur : Lieu avec l\'ID ' . htmlspecialchars($deleteId) . ' introuvable.</p>';
                }
            } ?>
            <?php // --- Détection du mode édition ---
            $editingLocation = null;
            $editId = null;

            if (isset($_GET['edit_id'])) {
                $editId = trim($_GET['edit_id']);
                $locations = getLocationsData();

                // Recherche de l'élément à éditer dans le tableau
                foreach ($locations as $location) {
                    if ($location['id'] === $editId) {
                        $editingLocation = $location;
                        break;
                    }
                }

                if (!$editingLocation) {
                    $message = '<p class="error">Lieu à éditer introuvable.</p>';
                    $editId = null; // Sortir du mode édition si l'ID est invalide
                }
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['add', 'edit'])) {

                $nom = trim($_POST['nom'] ?? '');
                $lat = trim($_POST['lat'] ?? '');
                $lon = trim($_POST['lon'] ?? '');
                $isEdit = $_POST['action'] === 'edit';
                $currentId = $isEdit ? trim($_POST['id'] ?? '') : null; // L'ID n'est présent qu'en mode édition
            
                // Validation des champs (reste inchangé)
                if (empty($nom) || empty($lat) || empty($lon)) {
                    $message = '<p class="error">Le nom, la latitude et la longitude sont obligatoires.</p>';
                } elseif (!is_numeric($lat) || !is_numeric($lon)) {
                    $message = '<p class="error">La Latitude et la Longitude doivent être des valeurs numériques (utiliser un point "." comme séparateur décimal).</p>';
                } else {
                    $locations = getLocationsData();
                    $success = false;

                    if ($isEdit) {
                        // Logique d'ÉDITION
                        foreach ($locations as $key => $location) {
                            if ($location['id'] === $currentId) {
                                $locations[$key]['nom'] = $nom;
                                $locations[$key]['lat'] = (float) $lat;
                                $locations[$key]['lon'] = (float) $lon;
                                $success = saveLocationsData($locations);
                                $message = $success ?
                                    '<p class="success">Le lieu **' . htmlspecialchars($nom) . '** a été mis à jour avec succès.</p>' :
                                    '<p class="error">Erreur lors de la mise à jour du fichier JSON.</p>';
                                break;
                            }
                        }
                    } else {
                        // Logique d'AJOUT (reste la même)
                        // ... (Vérification de l'unicité du nom et ajout) ...
                    }

                    // Si l'édition ou l'ajout a réussi, rediriger pour nettoyer l'état et l'URL.
                    if ($success) {
                        header("Location: admin_locations.php");
                        exit();
                    }
                }
            }
            ?>

            <h2>
                <?php echo $editingLocation ? 'Modifier le Lieu : ' . htmlspecialchars($editingLocation['nom']) : 'Ajouter un Nouveau Lieu'; ?>
            </h2>
            <form action="admin_locations.php" method="post">
                <input type="hidden" name="action" value="<?php echo $editingLocation ? 'edit' : 'add'; ?>">
                <?php if ($editingLocation): ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($editingLocation['id']); ?>">
                <?php endif; ?>

                <div>
                    <label for="nom">Nom du Lieu (Ville-Département) *</label><br>
                    <input type="text" id="nom" name="nom" required style="width: 80%; padding: 5px;"
                        value="<?php echo htmlspecialchars($editingLocation['nom'] ?? ''); ?>">
                </div>

                <div style="margin-top: 15px;">
                    <label for="lat">Latitude (Ex: 45.15) *</label><br>
                    <input type="text" id="lat" name="lat" required pattern="[0-9\.\-]+"
                        style="width: 80%; padding: 5px;"
                        value="<?php echo htmlspecialchars($editingLocation['lat'] ?? ''); ?>">
                </div>

                <div style="margin-top: 15px;">
                    <label for="lon">Longitude (Ex: 1.5333) *</label><br>
                    <input type="text" id="lon" name="lon" required pattern="[0-9\.\-]+"
                        style="width: 80%; padding: 5px;"
                        value="<?php echo htmlspecialchars($editingLocation['lon'] ?? ''); ?>">
                </div>

                <button type="submit" style="margin-top: 20px;" class="admin-headers-btns">
                    <?php echo $editingLocation ? 'Enregistrer les Modifications' : 'Ajouter le Lieu'; ?>
                </button>

                <?php if ($editingLocation): ?>
                    <a href="admin_locations.php" style="margin-left: 15px;">Annuler l'édition</a>
                <?php endif; ?>
            </form>

            <hr style="margin: 30px 0;">

            <h2>Lieux de Tournées Existants (<?php echo count($locationsData); ?>)</h2>
            <?php if (!empty($locationsData)): ?>
                <table class="locations-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom du Lieu</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($locationsData as $location): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($location['id']); ?></td>
                                <td><?php echo htmlspecialchars($location['nom']); ?></td>
                                <td><?php echo htmlspecialchars($location['lat']); ?></td>
                                <td><?php echo htmlspecialchars($location['lon']); ?></td>
                                <td>
                                    <a href="?edit_id=<?php echo htmlspecialchars($location['id']); ?>"
                                        class="action-link-edit">Éditer</a>
                                    |
                                    <a href="?delete_id=<?php echo htmlspecialchars($location['id']); ?>"
                                        class="action-link-delete"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce lieu ? Cette action est irréversible.');">
                                        Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun lieu de tournée n'est encore enregistré.</p>
            <?php endif; ?>

        </div>
    </div>

</body>

</html>