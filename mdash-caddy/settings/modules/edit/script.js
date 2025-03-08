document.getElementById("add-link").addEventListener("click", () => {
    addLink("");
});

var linkIds = [];
var id = 0;

function addLink(value){
    id++;

    let formField = document.createElement("div");
    formField.setAttribute("class", "form-field");
    formField.setAttribute("id", "module-field-" + id);
    
    let input = document.createElement("input");
    input.setAttribute("type", "text");
    input.setAttribute("id", "module-" + id);
    input.setAttribute("placeholder", "Module " + id + " URL");
    input.setAttribute("value", value);

    let inputIcon = document.createElement("button");
    inputIcon.setAttribute("class", "input-small-icon");
    inputIcon.setAttribute("onclick", "deleteLink(" + id + ");");
    inputIcon.innerHTML = "<span class='material-symbols-rounded'>delete</span>";

    formField.appendChild(input);
    formField.appendChild(inputIcon);
    document.getElementById("module-fields").appendChild(formField);

    linkIds.push(id);
}

function deleteLink(id){
    document.getElementById("module-field-" + id) . outerHTML = "";
    linkIds = linkIds.filter(linkId => linkId !== id);
}