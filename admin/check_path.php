<?php
echo "Dossier courant : " . __DIR__ . "<br>";
echo "Dossier parent : " . dirname(__DIR__) . "<br>";
echo "Chemin includes : " . dirname(__DIR__) . "/includes/admin_header.php" . "<br>";

if (file_exists(dirname(__DIR__) . "/includes/admin_header.php")) {
    echo "✅ admin_header.php trouvé !";
} else {
    echo "❌ admin_header.php NON trouvé !";
}
?>