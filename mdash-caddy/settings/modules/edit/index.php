<?php
$dbConnRequired = true;
require_once "/var/www/mdash/header.php";
?>

<body>
    <div class="darken">
        <div>
            <h1>Edit Modules</h1>
            <?php
            if ($docker) {
                echo "<p class='secondary'>Editing modules is not supported on Docker.</p>";
                exit;
            }
            ?>
            <p class="secondary">Do not put http:// or https:// in the link.</p>
            <form>
                <div id="module-fields">
                </div>
                <button type="button" id="add-link">Add Link</button>
                <script src="./script.js"></script>
                <div class="center">
                    <button type="submit">Update Modules</button>
                    <button type="button" onclick="window.location.href= '../';">Back </button>
                </div>
            </form>
        </div>
    </div>
</body>