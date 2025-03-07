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
                <button onclick="history.go(-1);">Back </button>
                <button onclick="window.location.href = './add'">Add a Module</button>
            </div>
            <table>
                <thead>
                    <th>Module</th>
                    <th>Version</th>
                </thead>
                <tbody>
                    <?php
                    $modules = shell_exec("caddy list-modules --versions");
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