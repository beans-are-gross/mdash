addHoverAnimation("name", "name-field");
addHoverAnimation("int-url", "int-url-field");
addHoverAnimation("ext-url", "ext-url-field");
addHoverAnimation("icon", "icon-field");

var userIds = [];

document.getElementById("edit-form").addEventListener("submit", (e) => {
  e.preventDefault();

  let submit = document.getElementById("edit-form-submit");
  submit.innerHTML = "<div class='loader small'></div> Update App";
  submit.disabled = true;
  submit.style.cursor = "not-allowed";

  let name = document.getElementById("name").value;
  let intUrl = document.getElementById("int-url").value;
  let intUrlSsl = document.getElementById("int-url-ssl").checked;
  let extUrl = document.getElementById("ext-url").value;
  let icon = document.getElementById("icon").value;
  let id = document.getElementById("id").value;

  if (name.trim() !== "") {
    let httpsRegex = /^https?:\/\//gm;

    if (!httpsRegex.test(extUrl)) {
      if (icon.trim() !== "") {
        //get all of the shared users
        let sharing = "";
        userIds.forEach((id) => {
          let sharingValue = document.getElementById("sharing-" + id).value;
          sharing += id + "=" + sharingValue + ",";
        });

        //get the mdash-token cookie to pass to the api
        let cookies = document.cookie;
        cookies = cookies.split(";");
        let mdashToken = cookies.find((c) =>
          c.trim().startsWith("mdash-token=")
        );
        mdashToken = mdashToken.split("=")[1];

        fetch("edit.api.php", {
          method: "POST",
          credentials: "same-origin",
          body: JSON.stringify({
            name: name,
            intUrl: intUrl,
            intUrlSsl: intUrlSsl,
            extUrl: extUrl,
            icon: icon,
            id: id,
            sharing: sharing,
          }),
          headers: {
            "Content-type": "application/json; charset=UTF-8",
            Cookie: "mdash-token=" + mdashToken,
          },
        })
          .then((response) => response.json())
          .then((json) => {
            if (json["success"]) {
              window.location.href = "/dashboard/";
            } else if (json["error"] !== undefined) {
              showError("Error", json["error"], 5000);
            } else {
              showError("Invald Response", JSON.stringify(json), 5000);
            }
          });
      } else {
        showError("Icon", "Please enter an icon name.", 5000);
      }
    } else {
      showError("External URL", "Do not enter http:// or https://.", 5000);
    }
  } else {
    showError("Name", "Please enter a name.", 5000);
  }

  submit.innerHTML = "Update App";
  submit.disabled = false;
  submit.style.cursor = "pointer";
});
