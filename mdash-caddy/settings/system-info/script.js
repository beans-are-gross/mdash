var totalIconsPerRow = Math.floor(
  document.getElementById("darken").offsetWidth / 200
);
var totalIconsPerRowCss = "";

for (var i = 0; i < totalIconsPerRow; i++){
  totalIconsPerRowCss += " auto";
}

document.getElementById("system-info-grid").style.gridTemplateColumns =
  totalIconsPerRowCss;

var idArray = [
  "total-space",
  "free-space",
  "used-space",
  "storage",
  "total-memory",
  "used-memory",
  "available-memory",
  "memory",
  "timezone",
  "server-time",
  "server-date",
  "up-since",
  "uptime",
  "ip-addr",
  "hostname",
];

function updateInfo() {
    fetch("./info.api.php")
    .then((res) => res.json())
    .then((json) => {
        idArray.forEach((id) => {
        if(id == "storage"){
            let progress = document.getElementById("storage");
            progress.value = json["used-space"];
            progress.max = json["total-space"];
        } else if(id == "memory") {
            let progress = document.getElementById("memory");
            progress.value = json["used-memory"];
            progress.max = json["total-memory"];
        } else {
            let textContent = document.getElementById(id).textContent;
            document.getElementById(id).textContent = json[id];
        }
        });
    });
}

updateInfo();
setInterval(updateInfo, 1000);