addHoverAnimation("login-username", "login-form-username-field");
addHoverAnimation("login-password", "login-form-password-field");
addReveal("login-password-reveal", "login-password");

document.getElementById("login-form").addEventListener("submit", (e) => {
  e.preventDefault();

  let username = document.getElementById("login-username").value;
  let password = document.getElementById("login-password").value;

  if (username.trim() === "") {
    showError("Error", "Please fill in your username.", 5000);
    return;
  }

  if (password.trim() === "") {
    showError("Error", "Please fill in your password.", 5000);
    return;
  }

  fetch("login.api.php", {
    method: "POST",
    body: JSON.stringify({
      username: username,
      password: password,
    }),
    headers: {
      "Content-type": "application/json; charset=UTF-8",
    },
  })
    .then((response) => response.json())
    .then((json) => {
      if(json["error"] !== undefined){
        showError("Error", $json["error"], 5000);
      } else if (json["correct"]) {
        window.location.href = "/dashboard/";
      } else if (!json["correct"]) {
        showError("Error", "Incorrect username or password.", 5000);
      } else {
        showError("Invald Response",  JSON.stringify(json), 5000);
      }
    });
});
