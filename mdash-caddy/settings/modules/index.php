<?php
require_once "/var/www/mdash/header.php";
?>

<head>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="darken">
        <div>
            <h1>Modules</h1>
            <p id="stats" class="secondary"></p>
            <div class="center">
                <button onclick="window.location.href= '../';">Back </button>
                <button onclick="window.location.href = './add'">Add a Module</button>
                <select id="filter-select" onchange="window.location.href = './?filter=' + this.value;">
                    <option value="all">All</option>
                    <option value="skip" id="skip">Non-Standard</option>
                </select>
            </div>
            <table>
                <thead>
                    <th>Module</th>
                    <th>Version</th>
                </thead>
                <tbody>
                    <?php
                    if (isset($_GET["filter"])) {
                        if ($_GET["filter"] == "skip") {
                            $modules = shell_exec("caddy list-modules --skip-standard --versions");
                            echo "<script>document.getElementById('skip').selected = true;</script>";
                        } else {
                            $modules = shell_exec("caddy list-modules --versions");
                        }
                    } else {
                        $modules = shell_exec("caddy list-modules --versions");
                    }
                    $modules = explode("\n", $modules);
                    $stats = "";
                    foreach ($modules as $module) {
                        if (str_contains($module, ":")) {
                            $stats .= "$module <br>";
                            continue;
                        }

                        $moduleInfo = explode(" ", $module);

                        if (empty($moduleInfo[0]) && empty($moduleInfo[1])) {
                            continue;
                        }

                        echo "<tr>";
                        echo "<td> $moduleInfo[0] </td>";
                        echo "<td> $moduleInfo[1] </td>";
                        echo "</tr>";
                    }

                    echo "<script>document.getElementById('stats').innerHTML = '$stats';</script>";
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>