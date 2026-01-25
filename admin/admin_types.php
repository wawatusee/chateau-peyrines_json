<?php
// Inclut la gestion de session pour l'authentification
include 'session_management.php';

// Définit le chemin du fichier JSON des types d'événements
define('TYPES_FILE', 'json/events_types.json');

/**
 * Lit et décode le fichier JSON des types d'événements.
 * @return array Le tableau des types ou un tableau vide si erreur/fichier inexistant.
 */
function getTypesData() {
    if (!file_exists(TYPES_FILE)) {
        // Crée un tableau vide si le fichier n'existe pas
        return [];
    }
    
    $json = file_get_contents(TYPES_FILE);
    $data = json_decode($json, true);
    
    // Assure que le résultat est un tableau
    return is_array($data) ? $data : [];
}

/**
 * Écrit les données des types d'événements dans le fichier JSON.
 * @param array $data Le tableau de données à écrire.
 * @return bool Vrai en cas de succès, faux sinon.
 */
function saveTypesData(array $data) {
    // Encode les données en JSON formaté (avec support des caractères spéciaux français)
    $json_data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Écriture dans le fichier avec verrouillage
    return file_put_contents(TYPES_FILE, $json_data, LOCK_EX) !== false;
}

/**
 * Génère un identifiant unique basé sur le format 'TYPE-XXX'.
 * @param array $currentTypes Tableau des types existants pour vérifier l'unicité.
 * @return string Nouvel ID unique.
 */
function generateUniqueTypeId(array $currentTypes) {
    $ids = array_column($currentTypes, 'id');
    $i = 1;
    do {
        // Formatage de l'ID avec des zéros en tête (ex: TYPE-001)
        $newId = 'TYPE-' . str_pad($i, 3, '0', STR_PAD_LEFT);
        $i++;
    } while (in_array($newId, $ids));
    
    return $newId;
}


$message = '';

// Traitement du formulaire d'ajout/édition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($name)) {
        $message = '<p class="error">Le nom du type d\'événement est obligatoire.</p>';
    } else {
        $types = getTypesData();
        
        // 1. Vérification de l'unicité du nom (optionnel mais recommandé)
        $nameExists = false;
        foreach ($types as $type) {
            if (strtolower($type['name']) === strtolower($name)) {
                $nameExists = true;
                break;
            }
        }

        if ($nameExists) {
             $message = '<p class="error">Ce nom de type d\'événement existe déjà.</p>';
        } else {
            // 2. Génération de l'ID unique
            $newId = generateUniqueTypeId($types);
            
            // 3. Création de la nouvelle entrée
            $newType = [
                'id' => $newId,
                'name' => $name,
                'description' => $description
            ];
            
            // Ajout et sauvegarde
            $types[] = $newType;
            if (saveTypesData($types)) {
                $message = '<p class="success">Le type **' . htmlspecialchars($name) . '** a été ajouté avec succès (ID: ' . $newId . ').</p>';
                // Réinitialiser les champs du formulaire après succès (pour un POST/Redirect/GET)
                // Ici, nous faisons simplement un succès-message
            } else {
                $message = '<p class="error">Erreur lors de l\'enregistrement du fichier JSON.</p>';
            }
        }
    }
}

// Charger les données pour l'affichage
$typesData = getTypesData();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des Types d'Événements</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/admin_types.css">
    </head>
<body>

    <div class="admin-container">
        <h1>Administration des Types d'Événements</h1>
        
        <nav class="admin-nav">
            <ul>
                <li><a href="admin.php">Retour au Tableau de Bord</a></li>
                <li><a href="admin_tournees.php">Gérer les Tournées</a></li>
                <li><a href="admin_locations.php">Gérer les Lieux</a></li>
                <li><a href="?logout=1">Déconnexion</a></li>
            </ul>
        </nav>
        
        <div class="admin-content">
            
            <?php echo $message; // Affiche les messages (erreur/succès) ?>

            <h2>Ajouter un Nouveau Type</h2>
            <form action="admin_types.php" method="post">
                <input type="hidden" name="action" value="add">
                
                <div>
                    <label for="name">Nom du Type (ex: Livraison, Evenement) *</label><br>
                    <input type="text" id="name" name="name" required style="width: 80%; padding: 5px;">
                </div>
                
                <div style="margin-top: 15px;">
                    <label for="description">Description :</label><br>
                    <textarea id="description" name="description" rows="3" style="width: 80%; padding: 5px;"></textarea>
                </div>
                
                <button type="submit" style="margin-top: 20px;" class="admin-headers-btns">
                    Ajouter le Type
                </button>
            </form>

            <hr style="margin: 30px 0;">

            <h2>Types d'Événements Existants (<?php echo count($typesData); ?>)</h2>
            <?php if (!empty($typesData)): ?>
                <table class="types-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($typesData as $type): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($type['id']); ?></td>
                                <td><?php echo htmlspecialchars($type['name']); ?></td>
                                <td><?php echo htmlspecialchars($type['description']); ?></td>
                                <td><a href="#" style="color: #FFC107;">Éditer</a> | <a href="#" style="color: #DC3545;">Supprimer</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun type d'événement n'est encore enregistré.</p>
            <?php endif; ?>
            
        </div>
    </div>

</body>
</html>