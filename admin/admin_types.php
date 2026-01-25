<?php
// Inclut la gestion de session pour l'authentification
include 'session_management.php';

// Définit le chemin du fichier JSON des types d'événements
define('TYPES_FILE', 'json/events_types.json'); // Chemin corrigé

/**
 * Lit et décode le fichier JSON des types d'événements.
 */
function getTypesData()
{
    if (!file_exists(TYPES_FILE)) {
        return [];
    }
    $json = file_get_contents(TYPES_FILE);
    return json_decode($json, true) ?: [];
}

/**
 * Écrit les données des types d'événements dans le fichier JSON.
 */
function saveTypesData(array $data)
{
    $json_data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(TYPES_FILE, $json_data, LOCK_EX) !== false;
}

/**
 * Génère un identifiant unique pour un type.
 */
function generateUniqueTypeId(array $currentTypes)
{
    $ids = array_column($currentTypes, 'id');
    $i = 1;
    do {
        $newId = 'TYPE-' . str_pad($i, 3, '0', STR_PAD_LEFT);
        $i++;
    } while (in_array($newId, $ids));
    return $newId;
}

$message = '';
$typesData = getTypesData();

// Traitement des actions (ajout, édition, suppression)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $typeId = $_POST['type_id'] ?? '';

    if ($action === 'add') {
        if (empty($name)) {
            $message = '<p class="error">Le nom du type est obligatoire.</p>';
        } else {
            // Vérification de l'unicité du nom
            $nameExists = false;
            foreach ($typesData as $type) {
                if (strtolower($type['name']) === strtolower($name)) {
                    $nameExists = true;
                    break;
                }
            }
            if ($nameExists) {
                $message = '<p class="error">Ce nom existe déjà.</p>';
            } else {
                $newId = generateUniqueTypeId($typesData);
                $typesData[] = [
                    'id' => $newId,
                    'name' => $name,
                    'description' => $description
                ];
                if (saveTypesData($typesData)) {
                    $message = '<p class="success">Type **' . htmlspecialchars($name) . '** ajouté avec succès (ID: ' . $newId . ').</p>';
                } else {
                    $message = '<p class="error">Erreur lors de l\'enregistrement.</p>';
                }
            }
        }
    } elseif ($action === 'edit' && $typeId) {
        foreach ($typesData as &$type) {
            if ($type['id'] === $typeId) {
                $type['name'] = $name;
                $type['description'] = $description;
                break;
            }
        }
        if (saveTypesData($typesData)) {
            $message = '<p class="success">Type mis à jour avec succès.</p>';
        } else {
            $message = '<p class="error">Erreur lors de la mise à jour.</p>';
        }
    } elseif ($action === 'delete' && $typeId) {
        $typesData = array_filter($typesData, fn($type) => $type['id'] !== $typeId);
        if (saveTypesData(array_values($typesData))) { // Réindexe le tableau
            $message = '<p class="success">Type supprimé avec succès.</p>';
        } else {
            $message = '<p class="error">Erreur lors de la suppression.</p>';
        }
    }
}

// Récupération des données pour l'édition
$editType = null;
if (isset($_GET['edit']) && $_GET['edit']) {
    $editId = $_GET['edit'];
    foreach ($typesData as $type) {
        if ($type['id'] === $editId) {
            $editType = $type;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des Types d'Événements</title>
    <link rel="stylesheet" href="./css/admin.css">
    <style>
        /* Styles spécifiques pour cette page */
        .types-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .types-table th,
        .types-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .types-table th {
            background-color: #f8f9fa;
        }

        .types-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .action-links a {
            margin-right: 10px;
            text-decoration: none;
            font-weight: bold;
        }

        .edit-link {
            color: #FFC107;
        }

        .delete-link {
            color: #DC3545;
        }

        .success {
            color: #28a745;
            background-color: #d4edda;
            padding: 10px;
            border-radius: 4px;
        }

        .error {
            color: #721c24;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
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
            <?php echo $message; ?>

            <h2><?php echo $editType ? 'Éditer' : 'Ajouter'; ?> un Type d'Événement</h2>
            <form action="admin_types.php" method="post">
                <input type="hidden" name="action" value="<?php echo $editType ? 'edit' : 'add'; ?>">
                <?php if ($editType): ?>
                    <input type="hidden" name="type_id" value="<?php echo htmlspecialchars($editType['id']); ?>">
                <?php endif; ?>

                <div style="margin-bottom: 15px;">
                    <label for="name">Nom du Type *</label><br>
                    <input type="text" id="name" name="name"
                        value="<?php echo $editType ? htmlspecialchars($editType['name']) : ''; ?>" required
                        style="width: 80%; padding: 8px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="description">Description</label><br>
                    <textarea id="description" name="description" rows="3"
                        style="width: 80%; padding: 8px;"><?php echo $editType ? htmlspecialchars($editType['description']) : ''; ?></textarea>
                </div>

                <button type="submit" class="admin-headers-btns" style="margin-top: 10px;">
                    <?php echo $editType ? 'Mettre à jour' : 'Ajouter'; ?> le Type
                </button>
                <?php if ($editType): ?>
                    <a href="admin_types.php" class="admin-headers-btns"
                        style="background-color: #6c757d; margin-left: 10px;">Annuler</a>
                <?php endif; ?>
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
                                <td class="action-links">
                                    <a href="?edit=<?php echo htmlspecialchars($type['id']); ?>" class="edit-link">Éditer</a>
                                    <a href="#" class="delete-link"
                                        onclick="confirmDelete('<?php echo htmlspecialchars($type['id']); ?>', '<?php echo htmlspecialchars($type['name']); ?>')">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun type d'événement n'est encore enregistré.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function confirmDelete(typeId, typeName) {
            if (confirm(`Voulez-vous vraiment supprimer le type "${typeName}" ?`)) {
                // Création d'un formulaire dynamique pour la suppression
                const form = document.createElement('form');
                form.method = 'post';
                form.action = 'admin_types.php';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="type_id" value="${typeId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>