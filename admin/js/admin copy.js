/**
 * js/admin.js
 * Interface d'administration Vanilla JS pour la gestion des tournées.
 * API Endpoint: api/tournees_api.php
 */

// Les constantes sont passées par admin_tournees.php :
// const REFERENCE_LOCATIONS, const REFERENCE_TYPES, const INITIAL_TOURNEES

const API_ENDPOINT = 'api/tournees_api.php';
const appContainer = document.getElementById('tournees-management-app');
let currentTourneesList = INITIAL_EVENTS; // Liste mutable des tournées

// --- 1. FONCTIONS UTILITAIRES DE PRÉPARATION DES DONNÉES ---

/**
 * Convertit un tableau de référence (lieux ou types) en un objet mappé par ID pour un accès rapide.
 */
function mapReferenceData(array, key) {
    const map = {};
    array.forEach(item => {
        map[item[key]] = item;
    });
    return map;
}

const locationsMap = mapReferenceData(REFERENCE_LOCATIONS, 'id');
const typesMap = mapReferenceData(REFERENCE_TYPES, 'id');


/**
 * Fonction d'échappement pour les valeurs HTML (sécurité de base)
 */
function htmlspecialchars(str) {
    if (typeof str !== 'string') return '';
    return str.replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#039;');
}

// --- 2. GESTION DES REQUÊTES AJAX (Cœur de l'API) ---

/**
 * Envoie une requête POST à l'API et met à jour l'interface.
 * @param {Object} data Les données à envoyer (incluant l'action 'add', 'edit', ou 'delete').
 */
async function sendApiRequest(data) {
    const formMessage = document.getElementById('form-message');
    formMessage.className = 'message';
    formMessage.textContent = 'Envoi en cours...';

    try {
        const response = await fetch(API_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok && result.status === 'success') {
            // Succès
            formMessage.className = 'message success';
            formMessage.textContent = result.message;
            
            // Mise à jour de la liste locale et de l'interface
            updateTourneesInterface(result.tournees, result.lastUpdated);
            resetForm();

        } else {
            // Erreur de l'API ou du serveur
            const message = result.message || 'Erreur inconnue lors de l\'opération.';
            formMessage.className = 'message error';
            formMessage.textContent = message;
        }

    } catch (error) {
        // Erreur réseau
        formMessage.className = 'message error';
        formMessage.textContent = 'Erreur réseau : La connexion à l\'API a échoué.';
        console.error('Erreur AJAX:', error);
    }
}


// --- 3. FONCTIONS DE GESTION DES ÉVÉNEMENTS (Formulaire, Édition, Suppression) ---

/**
 * Gère la soumission du formulaire (Ajout ou Édition).
 */
function handleFormSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const index = parseInt(form.elements['tournee-index'].value); // -1 pour ajout, index pour édition
    const locationId = form.elements['locationId'].value;
    
    // Récupérer les données de la référence Lieu
    const locationRef = locationsMap[locationId];
    
    if (!locationRef) {
        document.getElementById('form-message').className = 'message error';
        document.getElementById('form-message').textContent = 'Erreur: Lieu de référence introuvable.';
        return;
    }

    const data = {
        action: index === -1 ? 'add' : 'edit',
        index: index,
        date: form.elements['date'].value.trim(),
        type: form.elements['type'].value.trim(),
        text: form.elements['text'].value.trim(),
        etat: form.elements['etat'].value,
        
        // Structure de localisation exacte comme dans tournees.json
        locationId: locationId, // Clé non utilisée par l'API PHP mais utile en JS
        location: {
            nom: locationRef.nom, // Nom complet du lieu (Ville-Dép)
            lat: locationRef.lat, // Latitude
            lon: locationRef.lon  // Longitude
        }
    };
    
    // L'API PHP validera et traitera l'action
    sendApiRequest(data);
}

/**
 * Charge les données d'une tournée sélectionnée dans le formulaire pour l'édition.
 * @param {number} index Index de la tournée dans la liste.
 */
function editTournee(index) {
    if (index < 0 || index >= currentTourneesList.length) return;

    const tournee = currentTourneesList[index];
    const form = document.getElementById('tournee-form');

    // 1. Remplir les champs principaux
    form.elements['tournee-index'].value = index;
    form.elements['date'].value = tournee.date;
    form.elements['type'].value = tournee.type;
    form.elements['text'].value = tournee.text;
    form.elements['etat'].value = tournee.état;
    
    // 2. Trouver l'ID du lieu pour le select
    // On doit chercher la référence Lieu à partir des coordonnées/nom. 
    // Pour simplifier, on suppose que l'ID du lieu (LOC-XXX) est stocké ou facilement retrouvable.
    // Dans l'implémentation actuelle, le PHP lit nom, lat, lon. 
    // Si on veut vraiment l'ID LOC-XXX pour le select, il faut que 'locationId' soit inclus dans tournees.json.
    // P.S. : Pour le moment, nous allons faire une recherche par NOM du lieu dans la map de référence.
    let foundLocationId = '';
    for (const [id, locRef] of Object.entries(locationsMap)) {
        if (locRef.nom === tournee.location.nom && locRef.lat === tournee.location.lat) {
             foundLocationId = id;
             break;
        }
    }
    
    form.elements['locationId'].value = foundLocationId;
    
    // Mettre à jour le texte du bouton et scroll up
    form.querySelector('button[type="submit"]').textContent = 'Enregistrer les Modifications';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}


/**
 * Envoie une requête de suppression.
 * @param {number} index Index de la tournée à supprimer.
 */
function deleteTournee(index) {
    if (index < 0 || index >= currentTourneesList.length) return;

    const tournee = currentTourneesList[index];
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer la tournée du ${tournee.date} à ${tournee.location.nom} ?`)) {
        sendApiRequest({
            action: 'delete',
            index: index
        });
    }
}

/**
 * Réinitialise le formulaire d'édition/ajout.
 */
function resetForm() {
    const form = document.getElementById('tournee-form');
    form.reset();
    form.elements['tournee-index'].value = '-1';
    form.querySelector('button[type="submit"]').textContent = 'Sauvegarder la Tournée';
    document.getElementById('form-message').textContent = '';
    document.getElementById('form-message').className = 'message';
}


// --- 4. FONCTIONS DE CONSTRUCTION/MISE À JOUR DE L'INTERFACE ---

/**
 * Crée le formulaire d'ajout/édition d'une tournée (initialisation).
 */
function createForm() {
    // Le code de cette fonction reste le même que dans le draft précédent.
    // [CODE DU FORMULAIRE PRÉCÉDENT]
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
        typeSelect.appendChild(createOption(type.name, type.name));
    });
    
    // Remplissage du select 'locationId'
    const locationSelect = form.querySelector('#locationId');
    REFERENCE_LOCATIONS.forEach(loc => {
        locationSelect.appendChild(createOption(loc.id, loc.nom + ' (ID: ' + loc.id + ')'));
    });
    
    return form;
}

/**
 * Construit et retourne l'élément <option> pour un <select>.
 */
function createOption(value, text) {
    const option = document.createElement('option');
    option.value = value;
    option.textContent = text;
    return option;
}


/**
 * Re-crée et affiche le tableau listant toutes les tournées existantes.
 */
function createTourneesTable(tournees) {
    const table = document.createElement('table');
    table.classList.add('tournees-table');
    
    table.innerHTML = `
        <thead>
            <tr>
                <th>Date</th>
                <th>Lieu (Coordonnées)</th>
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
        
        // Pour l'affichage, on se base directement sur les données du JSON tournees
        const locationName = htmlspecialchars(tournee.location.nom);
        
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
 * Met à jour l'interface après une action réussie.
 * @param {Array<Object>} newTournees La nouvelle liste complète des tournées.
 * @param {string} lastUpdated La nouvelle date de dernière mise à jour.
 */
function updateTourneesInterface(newTournees, lastUpdated) {
    currentTourneesList = newTournees;

    // 1. Mise à jour de la date d'enregistrement
    const lastUpdatedElement = document.querySelector('.admin-content p:first-child'); 
    if (lastUpdatedElement) {
        lastUpdatedElement.innerHTML = `Dernière mise à jour du fichier <b>\`tournees.json\`</b> : <b>${lastUpdated}</b>`;
    }

    // 2. Mise à jour du tableau
    const tableTitle = document.querySelector('#tournees-management-app h2');
    let currentTable = document.querySelector('.tournees-table');

    if (tableTitle) {
        tableTitle.textContent = `Tournées Enregistrées (${currentTourneesList.length})`;
    }
    
    if (currentTable) {
        currentTable.remove(); // Supprime l'ancien tableau
    }
    
    const newTable = createTourneesTable(currentTourneesList);
    appContainer.appendChild(newTable); // Ajoute le nouveau tableau
    
    // 3. Rattacher les nouveaux écouteurs d'événements à la nouvelle table
    attachTableEventListeners();
}

/**
 * Attache les événements de clic pour Édition et Suppression au conteneur de l'application.
 */
function attachTableEventListeners() {
    appContainer.querySelectorAll('.action-edit').forEach(button => {
        button.addEventListener('click', (e) => {
            const index = parseInt(e.target.dataset.index);
            editTournee(index);
        });
    });

    appContainer.querySelectorAll('.action-delete').forEach(button => {
        button.addEventListener('click', (e) => {
            const index = parseInt(e.target.dataset.index);
            deleteTournee(index);
        });
    });
}


// --- 5. INITIALISATION PRINCIPALE ---

/**
 * Initialise l'application : construit les éléments DOM et ajoute les écouteurs d'événements initiaux.
 */
function initTourneesApp() {
    if (!appContainer) {
        console.error("Conteneur #tournees-management-app non trouvé.");
        return;
    }
    
    // Nettoyer le conteneur
    appContainer.innerHTML = ''; 
    
    // 1. Ajouter le formulaire et attacher son événement de soumission
    const form = createForm();
    form.addEventListener('submit', handleFormSubmit);
    form.querySelector('#reset-form').addEventListener('click', resetForm);
    appContainer.appendChild(form);
    
    // 2. Ajouter le tableau d'affichage initial
    const tableTitle = document.createElement('h2');
    tableTitle.textContent = `Tournées Enregistrées (${currentTourneesList.length})`;
    tableTitle.style.marginTop = '30px';
    appContainer.appendChild(tableTitle);
    
    const table = createTourneesTable(currentTourneesList);
    appContainer.appendChild(table);
    
    // 3. Attacher les écouteurs pour les actions d'édition et de suppression
    attachTableEventListeners();
}


// Démarrer l'application une fois que le DOM est complètement chargé
document.addEventListener('DOMContentLoaded', initTourneesApp);