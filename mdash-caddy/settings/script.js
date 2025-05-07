var totalIconsPerRow = Math.floor(document.getElementById("settings-home").offsetWidth / 210);
var totalIconsPerRowCss = "";

for(var i = 0; i < totalIconsPerRow; i++){
    totalIconsPerRowCss += " auto";
}

document.getElementById("settings-home").style.gridTemplateColumns = totalIconsPerRowCss;