//for making the outer field expand when the input element is clicked
function addHoverAnimation(field, container) {
  field = document.getElementById(field);
  container = document.getElementById(container);

  field.addEventListener("focus", () => {
    container.style.animation = "hoverShow .5s forwards";
  });

  field.addEventListener("blur", () => {
    container.style.animation = "hoverHide .5s forwards";
  });
}

function addButtonBorderAnimation(button) {
  button = document.getElementById(button);

  button.addEventListener("mouseover", () => {
    button.style.animation = "hoverShow .5s forwards";
  });

  button.addEventListener("mouseout", () => {
    button.style.animation = "hoverHide .5s forwards";
  });
}

//for revealing passwords with the eyeball button
function addReveal(button, field) {
  button = document.getElementById(button);
  field = document.getElementById(field);
  button.addEventListener("click", () => {
    if (field.type == "text") {
      field.type = "password";
      button.innerHTML =
        '<span class="material-symbols-rounded"> visibility </span>';
    } else if (field.type == "password") {
      field.type = "text";
      button.innerHTML =
        '<span class="material-symbols-rounded"> visibility_off </span>';
    }
  });
}

function showError(header, message, disappear){
  document.getElementById("error-container").style.display = "flex";
  document.getElementById("error-container").style.animation = "showPopup .5s forwards";
  document.getElementById("error-header").textContent = header;
  document.getElementById("error-message").innerHTML = message;

  if(disappear !== 0){
    setTimeout(() => {
      document.getElementById("error-container").style.animation = "hidePopup .5s forwards";
      setTimeout(() => {
        document.getElementById("error-container").style.display = "none";
      }, 500);
    }, disappear);
  }
}