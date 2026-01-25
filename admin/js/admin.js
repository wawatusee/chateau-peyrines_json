/**
 * js/admin.js
 * Interface d'administration Vanilla JS pour la gestion des événements de tournée.
 * API Endpoint: api/tournees_api.php
 */

// Constantes passées par admin_tournees.php
const API_ENDPOINT = 'api/tournees_api.php';
const appContainer = document.getElementById('tournees-management-app');
let currentEventsList = INITIAL_EVENTS; // Liste mutable des événements

// --- 1. FONCTIONS UTILITAIRES ---
/**
 * Convertit un tableau de référence en un objet mappé par ID.
 */
function mapReferenceData(array, key) {
    return array.reduce((map, item) => {
        map[item[key]] = item;
        return map;
    }, {});
}

const locationsMap = mapReferenceData(REFERENCE_LOCATIONS, 'id');
const typesMap = mapReferenceData(REFERENCE_TYPES, 'id');

/**
 * Échappement HTML pour la sécurité.
 */
function escapeHtml(str) {
    if (typeof str !== 'string') return '';
    return str.replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// --- 2. GESTION DES REQUÊTES AJAX ---
/**
 * Envoie une requête à l'API et met à jour l'interface.
 */
/*async function sendApiRequest(data) {
    const formMessage = document.getElementById('form-message');
    formMessage.className = 'message success show';
    formMessage.textContent = result.message;
    setTimeout(() => { formMessage.classList.remove('show'); }, 5000); // Cache après 5 secondes

    try {
        const response = await fetch(API_ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok && result.status === 'success') {
            formMessage.className = 'message success';
            formMessage.textContent = result.message;
            updateEventsInterface(result.events, result.lastUpdated);
            resetForm();
        } else {
            formMessage.className = 'message error';
            formMessage.textContent = result.message || 'Erreur inconnue.';
        }
    } catch (error) {
        formMessage.className = 'message error';
        formMessage.textContent = 'Erreur réseau : impossible de joindre l\'API.';
        console.error('Erreur AJAX:', error);
    }
}*/
async function sendApiRequest(data) {
    const formMessage = document.getElementById('form-message');
    formMessage.className = 'message';
    formMessage.textContent = 'Envoi en cours...';

    try {
        const response = await fetch(API_ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        // Vérifie si la réponse est valide
        if (!response.ok) {
            throw new Error(`Erreur HTTP : ${response.status}`);
        }

        // Essaye de parser la réponse JSON
        const result = await response.json();

        // Vérifie si la réponse contient bien un statut
        if (!result || typeof result !== 'object') {
            throw new Error('Réponse API invalide');
        }

        // Traite la réponse
        if (result.status === 'success') {
            formMessage.className = 'message success show';
            formMessage.textContent = result.message;

            // Met à jour l'interface avec les nouvelles données
            if (result.events) {
                updateEventsInterface(result.events, result.lastUpdated);
            }
            resetForm();
        } else {
            formMessage.className = 'message error show';
            formMessage.textContent = result.message || 'Erreur inconnue lors de l\'opération.';
        }

    } catch (error) {
        formMessage.className = 'message error show';
        formMessage.textContent = `Erreur : ${error.message}`;
        console.error('Erreur AJAX:', error);
    }
}


// --- 3. GESTION DES ÉVÉNEMENTS (CRUD) ---
/**
 * Gère la soumission du formulaire (ajout/édition).
 */
function handleFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const index = parseInt(form.elements['event-index'].value);
    const locationId = form.elements['locationId'].value;
    const locationRef = locationsMap[locationId];

    if (!locationRef) {
        showFormError('Lieu introuvable.');
        return;
    }

    const data = {
        action: index === -1 ? 'add' : 'edit',
        index: index,
        date: form.elements['date'].value.trim(),
        type: form.elements['type'].value, // ID technique (ex: "TYPE-001")
        text: form.elements['text'].value.trim(),
        etat: form.elements['etat'].value,
        locationId: locationId,
        location: {
            nom: locationRef.nom,
            lat: locationRef.lat,
            lon: locationRef.lon
        }
    };

    sendApiRequest(data);
}

/**
 * Charge un événement dans le formulaire pour édition.
 */
function editEvent(index) {
    if (index < 0 || index >= currentEventsList.length) return;

    const event = currentEventsList[index];
    const form = document.getElementById('event-form');

    form.elements['event-index'].value = index;
    form.elements['date'].value = event.date;
    form.elements['type'].value = event.type;
    form.elements['text'].value = event.text;
    form.elements['etat'].value = event.état;

    // Trouver l'ID du lieu
    let foundLocationId = '';
    for (const [id, locRef] of Object.entries(locationsMap)) {
        if (locRef.nom === event.location.nom && locRef.lat === event.location.lat) {
            foundLocationId = id;
            break;
        }
    }
    form.elements['locationId'].value = foundLocationId;

    form.querySelector('button[type="submit"]').textContent = 'Enregistrer les modifications';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/**
 * Supprime un événement après confirmation.
 */
function deleteEvent(index) {
    if (index < 0 || index >= currentEventsList.length) return;

    const event = currentEventsList[index];
    if (confirm(`Supprimer l'événement du ${event.date} à ${event.location.nom} ?`)) {
        sendApiRequest({ action: 'delete', index: index });
    }
}

/**
 * Réinitialise le formulaire.
 */
function resetForm() {
    const form = document.getElementById('event-form');
    form.reset();
    form.elements['event-index'].value = '-1';
    form.querySelector('button[type="submit"]').textContent = 'Ajouter l\'événement';
    document.getElementById('form-message').textContent = '';
}

/**
 * Affiche une erreur dans le formulaire.
 */
function showFormError(message) {
    const formMessage = document.getElementById('form-message');
    formMessage.className = 'message error';
    formMessage.textContent = message;
}

// --- 4. INTERFACE UTILISATEUR ---
/**
 * Crée le formulaire d'ajout/édition.
 */
function createForm() {
    const form = document.createElement('form');
    form.id = 'event-form';
    form.innerHTML = `
        <h3>Ajouter / Éditer un Événement</h3>
        <div class="form-group">
            <label for="date">Date *:</label>
            <input type="text" id="date" name="date" required placeholder="YYYY-MM-DD">
        </div>
        <div class="form-group">
            <label for="type">Type *:</label>
            <select id="type" name="type" required></select>
        </div>
        <div class="form-group">
            <label for="locationId">Lieu *:</label>
            <select id="locationId" name="locationId" required></select>
        </div>
        <div class="form-group">
            <label for="text">Détails :</label>
            <textarea id="text" name="text"></textarea>
        </div>
        <div class="form-group">
            <label for="etat">État *:</label>
            <select id="etat" name="etat" required>
                <option value="1">Confirmé</option>
                <option value="0">À définir</option>
            </select>
        </div>
        <input type="hidden" id="event-index" name="index" value="-1">
        <button type="submit">Ajouter l'événement</button>
        <button type="button" id="reset-form">Annuler</button>
        <p id="form-message" class="message"></p>
    `;

    // Remplit les selects
    const typeSelect = form.querySelector('#type');
    REFERENCE_TYPES.forEach(type => {
        typeSelect.appendChild(createOption(type.id, type.name)); // value = ID, text = libellé
    });


    const locationSelect = form.querySelector('#locationId');
    REFERENCE_LOCATIONS.forEach(loc => {
        locationSelect.appendChild(createOption(loc.id, `${loc.nom} (${loc.id})`));
    });

    return form;
}

/**
 * Crée un élément <option>.
 */
function createOption(value, text) {
    const option = document.createElement('option');
    option.value = value;
    option.textContent = text;
    return option;
}

/**
 * Crée le tableau des événements.
 */
function createEventsTable(events) {
    const table = document.createElement('table');
    table.classList.add('events-table');
    table.innerHTML = `
        <thead>
            <tr>
                <th>Date</th>
                <th>Lieu</th>
                <th>Type</th>
                <th>Détails</th>
                <th>État</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    `;

    const tbody = table.querySelector('tbody');
    events.forEach((event, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${escapeHtml(event.date)}</td>
            <td>${escapeHtml(event.location.nom)}</td>
            <td>${escapeHtml(REFERENCE_TYPES.find(t => t.id === event.type)?.name || event.type)}</td>
            <td>${escapeHtml(event.text)}</td>
            <td>${event.état === '1' ? '✅ Confirmé' : '❌ À définir'}</td>
            <td>
                <button data-index="${index}" class="action-edit">Éditer</button>
                <button data-index="${index}" class="action-delete">Supprimer</button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    return table;
}

/**
 * Met à jour l'interface après une modification.
 */
function updateEventsInterface(newEvents, lastUpdated) {
    currentEventsList = newEvents;

    // Met à jour la date de dernière modification
    const lastUpdatedElement = document.querySelector('.admin-content p:first-child');
    if (lastUpdatedElement) {
        lastUpdatedElement.innerHTML = `Dernière mise à jour : <b>${lastUpdated}</b>`;
    }

    // Met à jour le tableau
    const oldTable = document.querySelector('.events-table');
    if (oldTable) oldTable.remove();

    const tableTitle = document.querySelector('#tournees-management-app h2');
    if (tableTitle) {
        tableTitle.textContent = `Événements (${newEvents.length})`;
    }

    const newTable = createEventsTable(newEvents);
    appContainer.appendChild(newTable);

    // Réattache les écouteurs
    attachEventListeners();
}

/**
 * Attache les écouteurs d'événements aux boutons.
 */
function attachEventListeners() {
    appContainer.querySelectorAll('.action-edit').forEach(button => {
        button.addEventListener('click', (e) => editEvent(parseInt(e.target.dataset.index)));
    });

    appContainer.querySelectorAll('.action-delete').forEach(button => {
        button.addEventListener('click', (e) => deleteEvent(parseInt(e.target.dataset.index)));
    });
}

// --- 5. INITIALISATION ---
function initApp() {
    if (!appContainer) {
        console.error("Conteneur introuvable.");
        return;
    }

    appContainer.innerHTML = '';
    const form = createForm();
    form.addEventListener('submit', handleFormSubmit);
    form.querySelector('#reset-form').addEventListener('click', resetForm);
    appContainer.appendChild(form);

    const tableTitle = document.createElement('h2');
    tableTitle.textContent = `Événements (${currentEventsList.length})`;
    appContainer.appendChild(tableTitle);

    const table = createEventsTable(currentEventsList);
    appContainer.appendChild(table);

    attachEventListeners();
}

// Démarre l'application
document.addEventListener('DOMContentLoaded', initApp);
