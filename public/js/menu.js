//Menu responsive
/*function responsiveMenu() {
    var x = document.getElementById("responsiveMenu");
    if (x.className === "responsiveMenu") {
      x.className += " responsive";
    } else {
      x.className = "responsiveMenu";
    }
  }*/
function responsiveMenu() {
  const menu = document.querySelector('.menu-list');
  menu.classList.toggle('open');
}

