addHoverAnimation("nickname", "nickname-field");
addHoverAnimation("username", "username-field");
addHoverAnimation("password", "password-field");
addHoverAnimation("password-verify", "password-verify-field");

addReveal("password-reveal", "password");
addReveal("password-verify-reveal", "password-verify");

document.getElementById("add-form").addEventListener("submit", (e) => {
  e.preventDefault();

  let id = document.getElementById("id").value;

  let nickname = document.getElementById("nickname").value;
  let username = document.getElementById("username").value;
  let password = document.getElementById("password").value;
  let passwordVerify = document.getElementById("password-verify").value;

  if (nickname.trim() === "") {
    showError("Nickname", "Please enter a nickname.", 5000);
    return;
  }

  if (username.trim() === "") {
    showError("Userame", "Please enter a username.", 5000);
    return;
  }

  //if the first password field is not empty
  if (password.trim() != "") {
    //if the second password field is empty
    if (passwordVerify.trim() === "") {
        showError("Password Verify", "Please enter a password again.", 5000);
      return;
    } else {
      if (password !== passwordVerify) {
        showError("Passwords", "Your passwords do not match.", 5000);
        return;
      }
    }
  }

  let admin = document.getElementById("admin").checked;

  //get the mdash-token cookie to pass to the api
  let cookies = document.cookie;
  cookies = cookies.split(";");
  let mdashToken = cookies.find((c) => c.trim().startsWith("mdash-token="));
  mdashToken = mdashToken.split("=")[1];

  fetch("edit.api.php", {
    method: "POST",
    body: JSON.stringify({
      nickname: nickname,
      username: username,
      password: password,
      admin: admin,
      id: id,
    }),
    headers: {
      "Content-type": "application/json; charset=UTF-8",
      "Cookie": "mdash-token=" + mdashToken,
    },
  })
    .then((response) => response.json())
    .then((json) => {
      if (json["success"]) {
        window.location.href = "/settings/users";
      } else if (json["error"] !== undefined) {
        showError("Error", json["error"], 5000);
      } else {
        showError("Invald Response", JSON.stringify(json), 5000);
      }
    });
});
