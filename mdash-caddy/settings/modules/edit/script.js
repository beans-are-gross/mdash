document.getElementById("add-link").addEventListener("click", () => {
  addLink("");
});

var linkIds = [];
var id = 0;

function addLink(value) {
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

function deleteLink(id) {
  document.getElementById("module-field-" + id).outerHTML = "";
  linkIds = linkIds.filter((linkId) => linkId !== id);
}

document.getElementById("modules-form").addEventListener("submit", (e) => {
  e.preventDefault();

  let submit = document.getElementById("modules-form-submit");
  submit.innerHTML = "<div class='loader small'></div> Update Modules";
  submit.disabled = true;
  submit.style.cursor = "not-allowed";

  let modules = "";
  linkIds.forEach((module) => {
    let moduleUrl = document.getElementById("module-" + module).value;
    if (moduleUrl.trim() == "" || module == null) {
      return;
    } else {
      let httpsRegex = /^https?:\/\//gm;

      if (httpsRegex.test(moduleUrl)) {
        showError("Module " + module + " URL", "Do not enter http:// or https://.", 5000);
        return;
      } else {
        modules += moduleUrl + ",";
      }
    }
  });

  //get the mdash-token cookie to pass to the api
  let cookies = document.cookie;
  cookies = cookies.split(";");
  let mdashToken = cookies.find((c) => c.trim().startsWith("mdash-token="));
  mdashToken = mdashToken.split("=")[1];

  fetch("edit.api.php", {
    method: "POST",
    body: JSON.stringify({
      modules: modules,
    }),
    headers: {
      "Content-type": "application/json; charset=UTF-8",
      Cookie: "mdash-token=" + mdashToken,
    },
  })
    .then((response) => response.json())
    .then((json) => {
      if (json["success"]) {
        window.location.href = "/settings/modules/";
      } else if (json["error"] !== undefined) {
        showError("Error", json["error"], 5000);
      } else {
        showError("Invald Response", JSON.stringify(json), 5000);
      }
    });

  submit.innerHTML = "Update Modules";
  submit.disabled = false;
  submit.style.cursor = "pointer";
});
