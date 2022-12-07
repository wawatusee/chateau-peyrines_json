/*Création evenement */
/*Format des dates, le jour de la semaine, mois, année, le tout au format long */
const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
let evenement = new Date(Date.UTC(2012, 11, 20, 3, 0, 0));
/*toLocaleString associé à l'abbréviation de la langue permet d'afficher la date adapté à la région concernée*/
/*console.log(evenement.toLocaleDateString('fr-FR', options));*/
var map = L.map('map').setView([46.7 , 2.00], 6);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);