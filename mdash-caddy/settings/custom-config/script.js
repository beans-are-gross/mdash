function closeInfoPopup(){
    document.getElementById("info-container").style.animation =
        "infoHidePopup .5s forwards";
    setTimeout(() => {
        document.getElementById("info-container").style.display = "none";
    }, 500);
}

document.getElementById("config-form").addEventListener("submit", (e) => {
    e.preventDefault();
    
    var customConfig = document.getElementById("custom-config-textarea").value;
    
    //get the mdash-token cookie to pass to the api
    let cookies = document.cookie;
    cookies = cookies.split(";");
    let mdashToken = cookies.find((c) =>
        c.trim().startsWith("mdash-token=")
    );
    mdashToken = mdashToken.split("=")[1];

    fetch("submit.api.php", {
        method: "POST",
        credentials: "same-origin",
        body: JSON.stringify({
            customConfig: customConfig,
        }),
        headers: {
        "Content-type": "application/json; charset=UTF-8",
        Cookie: "mdash-token=" + mdashToken,
        },
    })
        .then((response) => response.json())
        .then((json) => {
        if (json["success"]) {
            window.location.href = "/settings/";
        } else if (json["error"] !== undefined) {
            document.getElementById("info-container").style.display = "flex";
            document.getElementById("info-container").style.animation =
                "infoShowPopup .5s forwards";
            
            document.getElementById("custom-config-log").innerHTML = json["error"];
        } else {
            showError("Invald Response", JSON.stringify(json), 5000);
        }
        });
});