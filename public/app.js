const btn = document.querySelector("#chrono");
const cpt = document.querySelector("#cpt");

let start_date;
let stop_date;
let compteur = 0;
let interval = null;

btn.addEventListener("click", doClick);

function doClick() {
  if (!interval) {
    start_date = new Date();
    btn.innerHTML = "stop";
    interval = setInterval(() => {
      compteur++;
      console.log(compteur);
      cpt.innerHTML = compteur;
    }, 1000);
  } else {
    console.log("STOP");
    btn.innerHTML = "start";
    clearInterval(interval);
    stop_date = new Date();
    // compteur = 0;
    interval = null;

    url = `/chrono/save/${start_date.getTime()}/${stop_date.getTime()}/${compteur}`;

    console.log(url);

    fetch(url)
      .then(function (response) {
        return response.json();
      })
      .then(function (data) {
        console.log(data);
      });
  }
}
