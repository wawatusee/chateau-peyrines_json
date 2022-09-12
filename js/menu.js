//Menu responsive
function responsiveMenu() {
    var x = document.getElementById("responsiveMenu");
    if (x.className === "responsiveMenu") {
      x.className += " responsive";
    } else {
      x.className = "responsiveMenu";
    }
  }