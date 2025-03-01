addHoverAnimation("nickname", "nickname-field");
addHoverAnimation("username", "username-field");
addHoverAnimation("password", "password-field");
addHoverAnimation("password-verify", "password-verify-field");

addReveal("password-reveal", "password");
addReveal("password-verify-reveal", "password-verify");

document.getElementById("welcome-form").addEventListener("submit", (e) => {
  e.preventDefault();
  
  let nickname = document.getElementById("nickname").value;
  let username = document.getElementById("username").value;
  let password = document.getElementById("password").value;
  let passwordVerify = document.getElementById("password-verify").value;

  if (nickname.trim() === "") {
    showError("Nickname", "Please enter a nickname.", 5000);
    return;
  }

  if (username.trim() === "") {
    showError("Username", "Please enter a username.", 5000);
    return;
  }

  if (password.trim() === "") {
    showError("Password", "Please enter a password.", 5000);
    return;
  }

  if (passwordVerify.trim() === "") {
    showError("Password Verify", "Please enter a password again.", 5000);
    return;
  }

  if (password !== passwordVerify) {
    showError("Passwords", "The passwords do not match.", 5000);
    return;
  }

  fetch("welcome.api.php", {
    method: "POST",
    body: JSON.stringify({
      nickname: nickname,
      username: username,
      password: password,
    }),
    headers: {
      "Content-type": "application/json; charset=UTF-8",
    },
  })
    .then((response) => response.json())
    .then((json) => {
      if (json["success"]) {
        window.location.href = "/";
      } else {
        showError("Invald Response", JSON.stringify(json), 5000);
      }
    });
});
