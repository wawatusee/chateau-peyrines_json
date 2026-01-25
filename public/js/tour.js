/*Création evenement */
/*Format des dates, le jour de la semaine, mois, année, le tout au format long */
const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
let evenement = new Date(Date.UTC(2012, 11, 20, 3, 0, 0));
/*toLocaleString associé à l'abbréviation de la langue permet d'afficher la date adapté à la région concernée*/
console.log(evenement.toLocaleDateString('fr-FR', options));
/*Paramètres des élèments Leaflet */
const map = L.map('map').setView([46.7, 2.00], 6);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    /*Chargement de la couche openstreetmap */
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);
let popup = L.popup()
    .setLatLng([44.648166, -0.228111])
    .setContent("<a href='https://www.bing.com/maps?osid=4179e578-2a85-4e37-b3c7-21788912bf07&cp=44.647881~-0.23479&lvl=16&v=2&sV=2&form=S00027'>Chateau Peyrines</a><BR> Maison mère")
    .openOn(map);
let lieux = a_tour.events;
for (let index in lieux) {
    let event = lieux[index];

    // Conversion de l'ID technique en libellé pour les popups
    const typeName = EVENT_TYPES.find(t => t.id === event.type)?.name || event.type;

    // Formatage de la date
    const eventDate = new Date(event.date);
    const formattedDate = eventDate.toLocaleDateString('fr-FR', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });

    // Création du marqueur et du popup
    let marker = L.marker([event.location.lat, event.location.lon]).addTo(map);
    marker.bindPopup(`
        <b>${formattedDate}</b><br>
        ${event.location.nom}<br>
        <strong>${typeName}</strong><br>
        ${event.text ? event.text : ''}
    `);
}
