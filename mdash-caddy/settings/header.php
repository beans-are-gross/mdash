<div class="settings-nav">
    <link rel="stylesheet" href="/settings/style.css">
    <div>
        <div class="settings-header">
            <h1 id="header-title" style="margin: 0;">Settings</h1>
            <p class="secondary">Version
                <?php
                $version = file_get_contents("/mdash/mdash.version");
                echo $version;
                ?>
            </p>
            <?php
            $updateCheck = curl_init("https://api.github.com/repos/beans-are-gross/mdash/releases/latest");
            curl_setopt($updateCheck, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');");
            curl_setopt($updateCheck, CURLOPT_RETURNTRANSFER, true);
            $updateCheck = json_decode(curl_exec($updateCheck), true);
            $latestVersion = str_replace("v", "", $updateCheck["tag_name"]);

            if($version !== $latestVersion){
                echo "<a href='{$updateCheck["html_url"]}'>Update to $latestVersion</a>";
            }
            ?>
        </div>
        <div class="settings-nav-buttons">
            <button type="button" id="dashboard" onclick="window.location.href = '/dashboard/';">Home</button>
            <button type="button" id="settings" onclick="window.location.href = '/settings/';">Settings</button>
            <button type="button" id="system-info" onclick="window.location.href = '/settings/system-info/';">System
                Info</button>
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