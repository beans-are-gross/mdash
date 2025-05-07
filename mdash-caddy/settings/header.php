<div class="settings-nav">
    <link rel="stylesheet" href="/settings/style.css">
    <div>
        <div class="settings-header">
            <h1 id="header-title" style="margin: 0;">Settings</h1>
            <p class="secondary">Version 1.1</p>
        </div>
        <div class="settings-nav-buttons">
            <button type="button" id="dashboard" onclick="window.location.href = '/dashboard/';">Home</button>
            <button type="button" id="settings" onclick="window.location.href = '/settings/';">Settings</button>
            <button type="button" id="system-info" onclick="window.location.href = '/settings/system-info/';">System Info</button>
            <button type="button" id="users" onclick="window.location.href = '/settings/users/';">Users</button>
            <button type="button" id="tokens" onclick="window.location.href = '/settings/tokens/';">Tokens</button>
            <button type="button" id="modules" onclick="window.location.href = '/settings/modules/';">Modules</button>
            <button type="button" id="custom-config" onclick="window.location.href = '/settings/custom-config/';">Custom
                Config</button>
            <button type="button" onclick="window.location.href = '/settings/logout.php';">Log Out</button>
            <script>
                let uri = window.location.href;
                uri = uri.split("/");
                uri = uri[uri.length - 2];

                if (document.getElementById(uri) !== null) {
                    document.getElementById(uri).style.backgroundColor = "#303036";
                }

                function titleCase(str) {
                    return str
                        .toLowerCase()
                        .split(' ')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .join(' ');
                }
                uri = uri.replace("-", " ");
                document.getElementById("header-title").textContent = titleCase(uri);
            </script>
        </div>
    </div>
</div>