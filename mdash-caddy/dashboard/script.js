var totalIconsPerRow = Math.floor(window.innerWidth / 350);
var totalIconsPerRowCss = "";

for(var i = 0; i < totalIconsPerRow; i++){
    totalIconsPerRowCss += " auto";
}

document.getElementById("app-grid").style.gridTemplateColumns = totalIconsPerRowCss;

function addAppFeatures(id, extUrl) {
    //when the box is clicked, go to this website
    document.getElementById('app-' + id).addEventListener('mouseup', ()=>{
        window.location.href = 'https://' + extUrl;
    });

    //when the box is hovered over, show the edit button
    document.getElementById('app-' + id).addEventListener('mouseover', ()=>{
        document.getElementById('app-edit-' + id).style.display = "block";
        document.getElementById('app-edit-' + id).style.animation = "show .25s forwards";
    });

    document.getElementById('app-' + id).addEventListener('mouseleave', ()=>{
        document.getElementById('app-edit-' + id).style.animation = "hide .25s forwards";
        setTimeout(() => {
            document.getElementById('app-edit-' + id).style.display = "none";
        }, 250);
    });

    document.getElementById('app-edit-' + id).style.opacity = 0;
    setTimeout(() => {
        document.getElementById('app-edit-' + id).style.display = "none";
        document.getElementById('app-edit-' + id).style.opacity = 1;
    }, 250);
}

// document.getElementById('darken').style.display = "none";

// window.addEventListener("load", () => {
//     document.getElementById("darken").style.animation = "show .5s forwards";
//     document.getElementById('darken').style.display = "flex";

//     document.getElementById("loader-container").style.animation = "hide .5s forwards";
//     setTimeout(() => {
//         document.getElementById("loader-container").outerHTML = "";
//     }, 500);
// });