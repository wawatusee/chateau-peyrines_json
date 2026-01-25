/**
 * js/admin.js
 * Interface d'administration Vanilla JS pour la gestion des tournées (tournees.json).
 * * Les constantes REFERENCE_LOCATIONS, REFERENCE_TYPES et INITIAL_TOURNEES
 * sont définies et passées par le fichier PHP (admin_tournees.php).
 */

// --- 1. FONCTIONS UTILITAIRES DE PRÉPARATION DES DONNÉES ---

/**
 * Convertit un tableau de référence (lieux ou types) en un objet
 * mappé par ID pour un accès rapide.
 * @param {Array<Object>} array Le tableau de référence (ex: REFERENCE_LOCATIONS).
 * @param {string} key La clé à utiliser comme ID (ex: 'id').
 * @returns {Object} Un objet map: { 'ID-XXX': {data}, ... }.
 */
function mapReferenceData(array, key) {
    const map = {};
    array.forEach(item => {
        map[item[key]] = item;
    });
    return map;
}

// Création des maps pour un accès rapide (lookup)
const locationsMap = mapReferenceData(REFERENCE_LOCATIONS, 'id');
const typesMap = mapReferenceData(REFERENCE_TYPES, 'id');

// --- 2. FONCTIONS DE CONSTRUCTION DU DOM ---

/**
 * Construit et retourne l'élément <option> pour un <select>.
 * @param {string} value La valeur de l'option.
 * @param {string} text Le texte affiché de l'option.
 * @returns {HTMLOptionElement}
 */
function createOption(value, text) {
    const option = document.createElement('option');
    option.value = value;
    option.textContent = text;
    return option;
}

/**
 * Crée le formulaire d'ajout/édition d'une tournée.
 * Inclut les champs date, type, lieu, texte et état.
 * @returns {HTMLFormElement}
 */
function createForm() {
    const form = document.createElement('form');
    form.id = 'tournee-form';
    form.innerHTML = `
        <h3>Ajouter / Éditer une Tournée</h3>

        <div class="form-group">
            <label for="date">Date(s) de la Tournée *:</label>
            <input type="text" id="date" name="date" required placeholder="YYYY-MM-DD ou YYYY-MM-DD et DD" />
            <small>Ex: 2024-11-18 ou 2024-date-a-definir</small>
        </div>
        
        <div class="form-group">
            <label for="type">Type d'Événement *:</label>
            <select id="type" name="type" required>
                <option value="">-- Sélectionner un type --</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="locationId">Lieu de la Tournée *:</label>
            <select id="locationId" name="locationId" required>
                <option value="">-- Sélectionner un lieu --</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="text">Détail (Texte additionnel):</label>
            <textarea id="text" name="text"></textarea>
            <small>Ex: Cabanes en fêtes</small>
        </div>
        
        <div class="form-group">
            <label for="etat">État *:</label>
            <select id="etat" name="etat" required>
                <option value="1">1 - Confirmé (Affiché)</option>
                <option value="0">0 - À définir (Masqué)</option>
            </select>
        </div>
        
        <input type="hidden" id="tournee-index" name="index" value="-1" />
        <button type="submit" class="admin-headers-btns">Sauvegarder la Tournée</button>
        <button type="button" id="reset-form" class="btn-secondary">Annuler / Nouveau</button>
        
        <p id="form-message" class="message"></p>
    `;

    // Remplissage du select 'type'
    const typeSelect = form.querySelector('#type');
    REFERENCE_TYPES.forEach(type => {
        typeSelect.appendChild(createOption(type.name, type.name + ' (' + type.id + ')'));
    });
    
    // Remplissage du select 'locationId'
    const locationSelect = form.querySelector('#locationId');
    REFERENCE_LOCATIONS.forEach(loc => {
        locationSelect.appendChild(createOption(loc.id, loc.nom + ' (ID: ' + loc.id + ')'));
    });
    
    return form;
}


/**
 * Crée le tableau listant toutes les tournées existantes.
 * @param {Array<Object>} tournees Le tableau des objets tournée.
 * @returns {HTMLTableElement}
 */
function createTourneesTable(tournees) {
    const table = document.createElement('table');
    table.classList.add('tournees-table');
    
    // En-têtes du tableau
    table.innerHTML = `
        <thead>
            <tr>
                <th>Date</th>
                <th>Lieu</th>
                <th>Type</th>
                <th>Détail</th>
                <th>État</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    `;

    const tbody = table.querySelector('tbody');

    tournees.forEach((tournee, index) => {
        const tr = document.createElement('tr');
        
        // Trouver le nom du lieu et son ID pour l'affichage
        const locationRef = locationsMap[tournee.locationId];
        const locationName = locationRef ? htmlspecialchars(locationRef.nom) : 'Lieu Inconnu (' + tournee.locationId + ')';
        
        // Contenu de la ligne
        tr.innerHTML = `
            <td>${htmlspecialchars(tournee.date)}</td>
            <td>
                ${locationName}
                <br><small>Lat: ${tournee.location.lat}, Lon: ${tournee.location.lon}</small>
            </td>
            <td>${htmlspecialchars(tournee.type)}</td>
            <td>${htmlspecialchars(tournee.text)}</td>
            <td>${tournee.état === '1' ? 'Confirmé ✅' : 'À définir ❌'}</td>
            <td>
                <button data-index="${index}" class="action-edit btn-action">Éditer</button>
                <button data-index="${index}" class="action-delete btn-action btn-delete">Supprimer</button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    return table;
}

/**
 * Fonction d'échappement pour les valeurs HTML (sécurité de base)
 * @param {string} str
 * @returns {string}
 */
function htmlspecialchars(str) {
    if (typeof str !== 'string') return '';
    return str.replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#039;');
}

// --- 3. FONCTION PRINCIPALE D'INITIALISATION ---

/**
 * Initialise l'application : construit les éléments DOM et ajoute les écouteurs d'événements.
 */
function initTourneesApp() {
    const appContainer = document.getElementById('tournees-management-app');
    if (!appContainer) {
        console.error("Conteneur #tournees-management-app non trouvé.");
        return;
    }
    
    // Nettoyer le conteneur et ajouter le formulaire et la liste
    appContainer.innerHTML = ''; 
    
    // 1. Ajouter le formulaire
    const form = createForm();
    appContainer.appendChild(form);
    
    // 2. Ajouter le tableau d'affichage
    const tableTitle = document.createElement('h2');
    tableTitle.textContent = 'Tournées Enregistrées (' + INITIAL_TOURNEES.length + ')';
    tableTitle.style.marginTop = '30px';
    appContainer.appendChild(tableTitle);
    
    const table = createTourneesTable(INITIAL_TOURNEES);
    appContainer.appendChild(table);

    // TODO: 3. Ajouter les écouteurs d'événements pour la soumission, l'édition et la suppression.
    // Cette partie nécessite un script API PHP dédié pour gérer les actions POST/GET.
}


// Démarrer l'application une fois que le DOM est complètement chargé
document.addEventListener('DOMContentLoaded', initTourneesApp);