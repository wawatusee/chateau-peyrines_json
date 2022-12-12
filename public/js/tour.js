/*Création evenement */
/*Format des dates, le jour de la semaine, mois, année, le tout au format long */
const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
let evenement = new Date(Date.UTC(2012, 11, 20, 3, 0, 0));
/*toLocaleString associé à l'abbréviation de la langue permet d'afficher la date adapté à la région concernée*/
console.log(evenement.toLocaleDateString('fr-FR', options));
/*Paramètres des élèments Leaflet */
var map = L.map('map').setView([46.7 , 2.00], 6);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
 /*Chargement de la couche openstreetmap */
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);
var popup = L.popup()
    .setLatLng([44.648166, -0.228111])
    .setContent("<a href='http://www.chateau-peyrines.com/test-pluxml/'>Chateau Peyrines</a><BR> Maison mère")
    .openOn(map);
    let lieux=a_tour.events;
    for (index in lieux){
        let marker=L.marker([lieux[index].location.lat,lieux[index].location.lon]).addTo(map);
        marker.bindPopup(lieux[index].date+"<br>"+lieux[index].location.nom+"<br>"+lieux[index].type);
        }